<?php

namespace App\Http\Controllers;

use App\Mail\VerificationEmail;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Exceptions as Tymon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
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
        return response()->json([
            'token' => $token,
            'message' => 'Login Successful',
            'token_type' => 'bearer',
            'expires_in' => Auth::factory()->getTTL() * 30
        ], 200);
    }

    /**
     * Registers a new user
     * @param Request $request
     * @return Response
     */

    public function register(Request $request)
    {
        //validate incoming request
        $this->validate($request, [
            'firstname' => 'required|string',
            'lastname' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required',
        ]);

        try {
            $user = new User;
            $user->firstname = $request->input('firstname');
            $user->lastname = $request->input('lastname');
            $user->email = $request->input('email');
            $user->password = app('hash')->make($request->input('password'));
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

            Mail::to($user->email, $user->name)->send(new VerificationEmail($data));

            //return successful
            return response()->json([
                'user_id' => $user->id,
                'success' => true,
                'message' => 'Thanks for signing up! Please check your email to complete your registration.'
            ], 201);
        } catch (\Exception $e) {
            //return error
            return response()->json([
                'error' => $e->getMessage(),
                'success' => false
            ], 409);
        }
    }

    /**
     * Get JWT via credential
     * @param Request $request
     * @return Response
     */

    public function login(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email|string',
            'password' => 'required|string'
        ]);
        $credentials = $request->only(['email', 'password']);
        $credentials['is_verified'] = 1;
        try {
            if (!$token = Auth::attempt($credentials)) {
                return response()->json(['error' => 'Email or Password is invalid', 'success' => false], 404);
            }
        } catch (Tymon\TokenExpiredException $e) {
            return response()->json(['token_expired' => $e->getMessage(),  'success' => false], 500);
        } catch (Tymon\TokenInvalidException $e) {
            return response()->json(['token_invalid' => $e->getMessage(), 'success' => false], 500);
        } catch (Tymon\JWTException $e) {
            return response()->json(['token_absent' => $e->getMessage(), 'success' => false], 500);
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
            return response()->json(['user' => $user, 'message' => 'User Fetched', 'success' => true], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'success' => false], 409);
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
            return response()->json(['message' => 'Successfully Logged out', 'success' => true], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'success' => false], 409);
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
                return response()->json(['success' => true, 'message' => 'Account already verified']);
            }
            $user->update(['is_verified' => 1]);
            DB::table('user_verifications')->where('token', $verification_code)->delete();
            return response()->json([
                'success' => true,
                'message' => "You have successfully verified your email address"
            ]);
        }
        return response()->json(['success' => false, 'error' => 'Verification code is invalid']);
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
            return response()->json([
                'success' => false,
                'error' => 'Your account does not exist or you have verified your account'
            ]);
        }
        //resend mail to user email
        try {
            $user = User::find($user_id);
            $data = [
                'firstname' => $user->firstname,
                'lastname' => $user->lastname,
                'verification_code' => $check->token,
            ];
            Mail::to($user->email, $user->name)->send(new VerificationEmail($data));
            return response()->json(['success' => true, 'message' => 'Verification code has been resent successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }
}
