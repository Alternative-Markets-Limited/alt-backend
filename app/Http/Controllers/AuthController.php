<?php

namespace App\Http\Controllers;

use App\Mail\ConfirmationEmail;
use App\Mail\OnePointMail;
use App\Mail\VerificationEmail;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Exceptions as Tymon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Ramsey\Uuid\Uuid;
use App\User;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['only' => ['user', 'logout']]);
    }

    /**
     * Returns token.
     *
     * @return Response
     */

    protected function respondWithToken($token)
    {
        $data = [
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => Auth::factory()->getTTL() * 30
        ];
        return $this->sendResponse($data, 'Login Successful', 200);
    }

    /**
     * Registers a new user
     * @param Request $request
     * @return Response
     */

    public function register(Request $request)
    {
        try {
            //validate incoming request
            $validator = Validator::make($request->all(), User::$registrationRules);
            //get referrer
            if ($validator->fails()) {
                return $this->sendError('validation error', $validator->errors(), 422);
            }
            $user = new User;
            $user->firstname = $request->input('firstname');
            $user->lastname = $request->input('lastname');
            $user->referral_token = Uuid::uuid1();
            $user->email = $request->input('email');
            $user->password = app('hash')->make($request->input('password'));
            if ($request->has('referrer')) {
                $referrer = User::where(['referral_token' => $request->input('referrer')])->first();
                $user->referrer_id =  $referrer->id;
                //add 1 point to the refferee points
                $referrer->points += 1;
                $referrer->update();

                $referrer_data = [
                    'firstname' => $referrer->firstname,
                    'points' => $referrer->points,
                ];

                //send mail to the person that referred
                Mail::to($referrer->email, $referrer->firstname)->send(new OnePointMail($referrer_data));
            }
            $user->save();
            //generate verification code
            $verification_code = Str::random(30);
            DB::table('user_verifications')->insert(['user_id' => $user->id, 'token' => $verification_code]);

            //send email
            $data = [
                'firstname' => $user->firstname,
                'lastname' => $user->lastname,
                'verification_code' => $verification_code
            ];

            Mail::to($user->email, $user->firstname)->send(new VerificationEmail($data));

            //return successful
            return $this->sendResponse(
                $user,
                'Thanks for signing up! Please check your email to complete your registration.',
                201
            );
        } catch (\Exception $e) {
            //return error
            return $this->sendError('registration error', $e->getMessage(), 409);
        }
    }

    /**
     * Get JWT via credential
     * @param Request $request
     * @return Response
     */

    public function login(Request $request)
    {
        try {
            //validate incoming request
            $validator = Validator::make($request->all(), User::$loginRules);
            if ($validator->fails()) {
                return $this->sendError('validation error', $validator->errors(), 422);
            }
            $credentials = $request->only(['email', 'password']);
            $credentials['is_verified'] = 1;
            if (!$token = Auth::attempt($credentials)) {
                return $this->sendError('Email or Password is invalid', null, 404);
            }
        } catch (Tymon\TokenExpiredException $e) {
            return $this->sendError('token_expired', $e->getMessage(), 500);
        } catch (Tymon\TokenInvalidException $e) {
            return $this->sendError('token_invalid', $e->getMessage(), 500);
        } catch (Tymon\JWTException $e) {
            return $this->sendError('token_absent', $e->getMessage(), 500);
        }
        return $this->respondWithToken($token);
    }


    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function user()
    {

        try {
            $user = Auth::user();
            return $this->sendResponse($user, 'User Fetched');
        } catch (\Exception $e) {
            return $this->sendError('error', $e->getMessage(), 409);
        }
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return Response
     */
    public function logout()
    {
        try {
            Auth::logout();
            return $this->sendResponse(null, 'Successfully Logged out');
        } catch (\Exception $e) {
            return $this->sendError('error', $e->getMessage(), 409);
        }
    }

    /**
     * Verify user
     * @param Request $request
     * @return Response
     */
    public function verifyUser($verification_code)
    {
        $check = DB::table('user_verifications')->where('token', $verification_code)->first();
        if ($check) {
            $user = User::find($check->user_id);
            if ($user->is_verified == 1) {
                return $this->sendResponse(null, 'Account already verified');
            }
            $user->update(['is_verified' => 1]);
            DB::table('user_verifications')->where('token', $verification_code)->delete();

            //send email
            $data = [
                'firstname' => $user->firstname,
                'lastname' => $user->lastname,
            ];

            Mail::to($user->email, $user->firstname)->send(new ConfirmationEmail($data));

            $successPath = getenv('WEBSITE_URL') . '/verification-successful';
            //Send account created successful email
            return redirect()->to($successPath);
        }
        $errorPath = getenv('WEBSITE_URL') . '/verification-error';
        return redirect()->to($errorPath);
    }

    /**
     * Verify user
     * @param Request $user_id
     * @return Response
     */
    public function requestVerification($user_id)
    {
        $check = DB::table('user_verifications')->where('user_id', $user_id)->first();
        //check if verification does not exist
        if (!$check) {
            return $this->sendError('error', 'Your account does not exist or you have verified your account', 404);
        }
        //resend mail to user email
        try {
            $user = User::find($user_id);
            $data = [
                'firstname' => $user->firstname,
                'lastname' => $user->lastname,
                'verification_code' => $check->token,
            ];
            Mail::to($user->email, $user->firstname)->send(new VerificationEmail($data));
            return $this->sendResponse(null, 'Verification code has been resent successfully');
        } catch (\Exception $e) {
            return $this->sendError('verification error', $e->getMessage(), 409);
        }
    }
}
