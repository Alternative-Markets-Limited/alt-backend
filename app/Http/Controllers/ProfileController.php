<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Cloudinary\Uploader;
use Illuminate\Support\Str;
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
            $this->validate($request, User::$createProfileRules);

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

            return response()->json(['success' => true, 'message' => 'Profile created successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 400);
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
            return response()->json(
                ['success' => true, 'message' => 'Profile fetched successfully', 'user' => $user],
                200
            );
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 400);
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

            //validate request
            $this->validate($request, User::$updateProfileRules);

            if ($request->has('avatar')) {
                Uploader::destroy($user->public_id);
                $extension = $request->avatar->extension();
                $image = $this->uploadImage($request->file('avatar')->getRealPath(), $user->firstname, $extension);
                $user->public_id = $image['public_id'];
                $user->avatar = $image['secure_url'];
            }
            $user->phone = $request->input('phone');
            $user->address = $request->input('address');
            $user->occupation = $request->input('occupation');
            $user->save();
            return response()->json(['success' => true, 'message' => 'Profile updated successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 409);
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
            $user->delete();
            return response()->json(['success' => true, 'message' => 'User deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 409);
        }
    }
}
