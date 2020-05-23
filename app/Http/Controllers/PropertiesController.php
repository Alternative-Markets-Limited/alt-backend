<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;
use App\Model\Property;
use Cloudinary\Uploader;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PropertiesController extends Controller
{
    /**
     * Create a new PropertiesController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => 'allProperties']);
    }


    /**
     * Function to upload image.
     * @param Request $data
     * @return Array uploaded file
     */

    public function uploadFile($path, $propertyName, $extension, $file)
    {
        $upload = null;
        $pubic_id = strtolower($propertyName . '-' . Str::random(10) . '.' . $extension);
        if ($file == 'video') {
            $upload = Uploader::upload_large($path, [
                'resource_type' => 'video',
                'public_id' => $pubic_id,
                'folder' => 'properties_videos'
            ]);
        } else {
            $upload = Uploader::upload(
                $path,
                [
                    'public_id' => $pubic_id,
                    'folder' => $file == 'image' ? 'properties' : 'properties_brochure'
                ]
            );
        }
        return $upload;
    }

    /**
     * Admin can create property
     * @param Request $request
     * @return Response
     */
    public function createProperty(Request $request)
    {
        try {
            //validate
            $validator = Validator::make($request->all(), Property::$propertyRules);
            if ($validator->fails()) {
                return $this->sendError('validation error', $validator->errors(), 422);
            }
            //create new property
            $property = new Property;
            $property->name = $request->input('name');
            $property->slug = $this->createSlug($request->input('name'));
            //upload image
            if ($request->has('image')) {
                $extension = $request->image->extension();
                $image = $this->uploadFile(
                    $request->file('image')->getRealPath(),
                    $property->name,
                    $extension,
                    'image'
                );
                $property->public_id = $image['public_id'];
                $property->image = $image['secure_url'];
            }
            $property->about = $request->input('about');

            //upload brochure
            if ($request->has('brochure')) {
                $extension = $request->brochure->extension();
                $brochure = $this->uploadFile(
                    $request->file('brochure')->getRealPath(),
                    $property->name,
                    $extension,
                    'brochure'
                );
                $property->brochure_public_id = $brochure['public_id'];
                $property->brochure = $brochure['secure_url'];
            }
            $property->location = $request->input('location');
            $property->investment_population = $request->input('investment_population');
            $property->net_rental_yield = $request->input('net_rental_yield');
            $property->min_yield = $request->input('min_yield');
            $property->max_yield = $request->input('max_yield');
            $property->holding_period = $request->input('holding_period');
            $property->min_fraction_price = $request->input('min_fraction_price');
            $property->max_fraction_price = $request->input('max_fraction_price');
            $property->category_id = $request->input('category_id');

            //upload multiple images to gallery
            if ($request->hasfile('gallery')) {
                foreach ($request->file('gallery') as $image) {
                    $extension = $image->extension();
                    $image = $this->uploadFile(
                        $image->getRealPath(),
                        $property->name,
                        $extension,
                        'image'
                    );
                    $data[] = $image['secure_url'];
                    $public_id[] = $image['public_id'];
                }
                $property->gallery = $data;
                $property->gallery_public_id = $public_id;
            }

            $property->facility = $request->input('facility');
            //upload video
            if ($request->has('video')) {
                $extension = $request->video->extension();
                $video = $this->uploadFile(
                    $request->file('video')->getRealPath(),
                    $property->name,
                    $extension,
                    'video'
                );
                $property->video_public_id = $video['public_id'];
                $property->video = $video['secure_url'];
            }
            $property->save();

            return $this->sendResponse($property, 'Property created successfully', 201);
        } catch (\Exception $e) {
            return $this->sendError('error', $e->getMessage(), 401);
        }
    }

    //update a property
    /**
     * Admin can update property
     * @param Request $request
     * @return Response
     */
    public function updateProperty($id, Request $request)
    {
        try {
            $property = Property::find($id);

            if (!$property) {
                return $this->sendError('Property not found', null, 404);
            }
            $validator = Validator::make($request->all(), Property::$propertyRules);
            if ($validator->fails()) {
                return $this->sendError('validation error', $validator->errors(), 422);
            }
            // update all fields
            $property->name = $request->input('name');

            if ($property->slug !== $this->createSlug($request->input('name'))) {
                $property->slug = $this->createSlug($request->input('name'), $id);
            }

            //update image and delete present one from cloudinary
            if ($request->has('image')) {
                if ($property->public_id) {
                    Uploader::destroy($property->public_id);
                }
                $extension = $request->image->extension();
                $image = $this->uploadFile(
                    $request->file('image')->getRealPath(),
                    $property->name,
                    $extension,
                    'image'
                );
                $property->public_id = $image['public_id'];
                $property->image = $image['secure_url'];
            }
            $property->about = $request->input('about');
            //upload brochure and delete the one in cloudinary
            if ($request->has('brochure')) {
                if ($property->brochure_public_id) {
                    Uploader::destroy($property->brochure_public_id);
                }
                $extension = $request->brochure->extension();
                $brochure = $this->uploadFile(
                    $request->file('brochure')->getRealPath(),
                    $property->name,
                    $extension,
                    'brochure'
                );
                $property->brochure_public_id = $brochure['public_id'];
                $property->brochure = $brochure['secure_url'];
            }
            $property->location = $request->input('location');
            $property->investment_population = $request->input('investment_population');
            $property->net_rental_yield = $request->input('net_rental_yield');
            $property->min_yield = $request->input('min_yield');
            $property->max_yield = $request->input('max_yield');
            $property->holding_period = $request->input('holding_period');
            $property->min_fraction_price = $request->input('min_fraction_price');
            $property->max_fraction_price = $request->input('max_fraction_price');
            $property->category_id = $request->input('category_id');
            //upload multiple images to gallery and delete present one
            if ($request->hasfile('gallery')) {
                foreach ($request->file('gallery') as $image) {
                    $extension = $image->extension();
                    $image = $this->uploadFile(
                        $image->getRealPath(),
                        $property->name,
                        $extension,
                        'image'
                    );
                    $data[] = $image['secure_url'];
                    $public_id[] = $image['public_id'];
                }
                $property->gallery = $data;
                $property->gallery_public_id = $public_id;
            }

            $property->facility = $request->input('facility');
            //upload video
            if ($request->has('video')) {
                if ($property->video_public_id) {
                    Uploader::destroy($property->video_public_id);
                }
                $extension = $request->video->extension();
                $video = $this->uploadFile(
                    $request->file('video')->getRealPath(),
                    $property->name,
                    $extension,
                    'video'
                );
                $property->video_public_id = $video['public_id'];
                $property->video = $video['secure_url'];
            }
            $property->save();

            return $this->sendResponse($property, 'Property updated successfully');
        } catch (\Exception $e) {
            return $this->sendError('error', $e->getMessage(), 409);
        }
    }

    //show all property
    /**
     * Unauthenticated users can view property
     * @return Response
     */
    public function allProperties()
    {
        try {
            $properties = Cache::remember('properties', 1800, function () {
                return Property::with('category')->select(
                    'id',
                    'name',
                    'slug',
                    'image',
                    'investment_population',
                    'net_rental_yield',
                    'min_fraction_price',
                    'min_yield',
                    'max_yield',
                    'category_id',
                    'about'
                )->get();
            });
            return $this->sendResponse($properties, 'Property fetched successfully');
        } catch (\Exception $e) {
            return $this->sendError('error', $e->getMessage(), 409);
        }
    }

    //show one property
    /**
     * Authenticated users can view property in details
     * @return Response
     */
    public function showProperty($slug)
    {
        try {
            $property = Cache::remember('property:' . $slug, 1800, function () use ($slug) {
                return Property::with('category')->where('slug', $slug)->first();
            });
            if (!$property) {
                return $this->sendError('Property not found', null, 404);
            }
            return $this->sendResponse($property, 'Property fetched successfully');
        } catch (\Exception $e) {
            return $this->sendError('error', $e->getMessage(), 409);
        }
    }

    //delete property
    /**
     * Admin can delete property
     * @return Response
     */
    public function deleteProperty($id)
    {
        try {
            $property = Property::find($id);
            if (!$property) {
                return $this->sendError('Property not found', null, 404);
            }

            if ($property->public_id) {
                Uploader::destroy($property->public_id);
            }
            if ($property->video_public_id) {
                Uploader::destroy($property->video_public_id);
            }
            if ($property->brochure_public_id) {
                Uploader::destroy($property->brochure_public_id);
            }
            if ($property->gallery_public_id) {
                foreach ($property->gallery_public_id as $image) {
                    Uploader::destroy($image);
                }
            }
            $property->delete();

            return $this->sendResponse(null, 'Property deleted successfully');
        } catch (\Exception $e) {
            return $this->sendError('error', $e->getMessage(), 409);
        }
    }
}
