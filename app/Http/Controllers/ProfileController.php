<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Cloudinary\Uploader;
use Illuminate\Support\Str;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Validator;
use App\User;

class ProfileController extends Controller
{
    /**
     * Create a new ProfileController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Function to upload image.
     * @param Request $data
     * @return Array image Array
     */

    public function uploadImage($path, $firstname, $extension)
    {
        $image = Uploader::upload(
            $path,
            [
                'public_id' => strtolower($firstname . '-' . Str::random(10) . '.' . $extension),
                'folder' => 'alt_avatars'
            ]
        );
        return $image;
    }


    //create user profile
    /**
     * Create a user profile
     * @param Request $request
     * @return Response
     */
    public function createProfile(Request $request)
    {
        try {
            //get user instance
            $user = Auth::user();

            //validate
            //validate incoming request
            $validator = Validator::make($request->all(), User::$createProfileRules);
            if ($validator->fails()) {
                return $this->sendError('validation error', $validator->errors(), 422);
            }
            //check if avatar exists
            if ($request->has('avatar')) {
                $extension = $request->avatar->extension();
                $image = $this->uploadImage($request->file('avatar')->getRealPath(), $user->firstname, $extension);
                $user->public_id = $image['public_id'];
                $user->avatar = $image['secure_url'];
            }
            $user->phone = $request->input('phone');
            $user->birthday  = $request->input('birthday');
            $user->address = $request->input('address');
            $user->occupation = $request->input('occupation');
            $user->bvn = $request->input('bvn'); //TODO: verify bvn before sending in the frontend
            $user->save();

            return $this->sendResponse($user, 'Profile created successfully');
        } catch (\Exception $e) {
            return $this->sendError('error', $e->getMessage(), 409);
        }
    }

    //read user profile
    /**
     * Read user profile
     * @return Response
     */
    public function getProfile()
    {
        try {
            $user = Auth::user();
            return $this->sendResponse($user, 'Profile fetched successfully');
        } catch (\Exception $e) {
            return $this->sendError('error', $e->getMessage(), 409);
        }
    }

    //update user profile
    /**
     * Create a user profile
     * @param Request $request
     * @return Response
     */
    public function updateProfile(Request $request)
    {
        try {
            $user = Auth::user();
            //validate incoming request
            $validator = Validator::make($request->all(), User::$updateProfileRules);
            if ($validator->fails()) {
                return $this->sendError('validation error', $validator->errors(), 422);
            }
            if ($request->has('avatar')) {
                if ($user->public_id) {
                    Uploader::destroy($user->public_id);
                }
                $extension = $request->avatar->extension();
                $image = $this->uploadImage($request->file('avatar')->getRealPath(), $user->firstname, $extension);
                $user->public_id = $image['public_id'];
                $user->avatar = $image['secure_url'];
            }
            $user->phone = $request->input('phone');
            $user->address = $request->input('address');
            $user->occupation = $request->input('occupation');
            $user->save();

            return $this->sendResponse($user, 'Profile updated successfully');
        } catch (\Exception $e) {
            return $this->sendError('error', $e->getMessage(), 409);
        }
    }

    //delete user

    /**
     * delete a user
     * @param
     * @return Response
     */
    public function deleteUser()
    {
        try {
            $user = Auth::user();
            //delete image from cloudinary if it exists
            if ($user->public_id) {
                Uploader::destroy($user->public_id);
            }
            $user->delete();
            return $this->sendResponse(null, 'User deleted successfully');
        } catch (\Exception $e) {
            return $this->sendError('error', $e->getMessage(), 409);
        }
    }

    /**
     * Verify bvn from verify.ng
     *
     * @param  Array  $data
     * @return \Illuminate\Http\Response
     */
    public function verifyBvn(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), User::$verifyBvnRules);
            if ($validator->fails()) {
                return $this->sendError('validation error', $validator->errors(), 422);
            }
            //get the firstname, surname, bvn, dob callbackURL
            $surname = $request->input('surname');
            $dob = $request->input('dob');
            $bvn = $request->input('bvn');
            $callbackURL = $request->input('callbackURL');

            $client = new Client();
            $url = getenv('VERIFY_NG_URL');
            $res = $client->request('POST', $url, [
                'json' => [
                    'surname' => $surname,
                    'dob' => $dob,
                    'bvn' => $bvn,
                    'callbackURL' => $callbackURL,
                ],
                'headers' => [
                    'Content-Type' => 'application/json',
                    'api-key' => getenv('VERIFY_NG_API_KEY'),
                    'userid' => getenv('VERIFY_NG_USER_ID')
                ]
            ]);
            $data = collect(json_decode(utf8_decode($res->getBody()->getContents()), true));
            return $this->sendResponse($data, 'Verification Request');
        } catch (\Exception  $e) {
            return $this->sendError('error', $e->getMessage(), 409);
        }
    }
}
