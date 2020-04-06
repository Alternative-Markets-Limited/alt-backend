<?php

namespace App\Http\Controllers;

use App\Model\Category;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class CategoriesController extends Controller
{
    /**
     * Create a new CategoriesController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $categories = Category::all();
            return $this->sendResponse($categories, 'Categories fetched successfully');
        } catch (\Exception $e) {
            return $this->sendError('category fetch error', $e->getMessage(), 409);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), Category::$categoryRules);
            if ($validator->fails()) {
                return $this->sendError('validation error', $validator->errors(), 422);
            }
            //create new category
            $category = new Category;
            $category->name = $request->input('name');
            $category->save();
            return $this->sendResponse($category, 'Category created succesfully', 201);
        } catch (\Exception $e) {
            return $this->sendError('error', $e->getMessage(), 409);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $category = Category::find($id);
            if (!$category) {
                return $this->sendError('Category not found', null, 404);
            }
            return $this->sendResponse($category, 'Category found');
        } catch (\Exception $e) {
            return $this->sendError('error', $e->getMessage(), 409);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $category = Category::find($id);
            if (!$category) {
                return $this->sendError('Category not found', null, 404);
            }
            $validator = Validator::make($request->all(), Category::$categoryRules);
            if ($validator->fails()) {
                return $this->sendError('validation error', $validator->errors(), 422);
            }
            $category->name = $request->input('name');
            $category->save();
            return $this->sendResponse($category, 'Category updated succesfully');
        } catch (\Exception $e) {
            return $this->sendError('error', $e->getMessage(), 409);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $category = Category::find($id);
            if (!$category) {
                return $this->sendError('Category not found', null, 404);
            }
            $category->delete();
            return $this->sendResponse(null, 'Category deleted successfully');
        } catch (\Exception $e) {
            return $this->sendError('error', $e->getMessage(), 409);
        }
    }
}
