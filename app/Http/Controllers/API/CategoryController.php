<?php

namespace App\Http\Controllers\API;

use App\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CategoryController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        return response()->json([
            'result' => 'success',
            'message' => 'greetings from categories'
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request){
        if (!$request->has('category')){
            return response()->json([
                'result' => 'error',
                'message' => 'You should set the category name with a parameter named category'
            ]);
        }
        $name = $request->get('category');
        $cat = new Category();
        $cat->name = $name;
        $cat->save();
        return response()->json([
            'result' => 'success',
            'category' => $cat
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request){
        if (!$request->has('category')){
            return response()->json([
                'result' => 'error',
                'message' => 'You should set the category id to delete with a parameter named category'
            ]);
        }
        $category = Category::find($request->get('category'));
        if (is_null($category)){
            return response()->json([
                'result' => 'error',
                'message' => 'Selected category is invalid'
            ]);
        }

        return response()->json([
            'result' => 'success',
            'category' => $category
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request){
        if (!$request->has('category')){
            return response()->json([
                'result' => 'error',
                'message' => 'You should set the category id to delete with a parameter named category'
            ]);
        }
        $category = Category::find($request->get('category'));
        if (is_null($category)){
            return response()->json([
                'result' => 'error',
                'message' => 'Selected category is invalid'
            ]);
        }

        if (!$request->has('nCategory')){
            return response()->json([
                'result' => 'error',
                'message' => 'You should set the new category name with a parameter named nCategory'
            ]);
        }

        $name = $request->get('nCategory');
        $category->name = $name;
        return response()->json([
            'result' => 'success',
            'category' => $category
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request){
        if (!$request->has('category')){
            return response()->json([
                'result' => 'error',
                'message' => 'You should set the category id to delete with a parameter named category'
            ]);
        }
        $category = Category::find($request->get('category'));
        if (is_null($category)){
            return response()->json([
                'result' => 'error',
                'message' => 'Selected category is invalid'
            ]);
        }

        $category->delete();
        return response()->json([
            'result' => 'success',
            'message' => 'Category deleted successfully'
        ]);
    }
}
