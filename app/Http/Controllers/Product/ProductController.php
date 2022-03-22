<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Exception;
use Illuminate\Http\Request;

class ProductController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:sanctum')->only(['store', 'update', 'destroy']);
    }

    //get all products
    public function index(Request $request)
    {
        try {
            //if request has query params then filter products
            if ($request->has('search')) {
                $products = Product::where('name', 'like', '%' . $request->search . '%')->get();
            } else {
                $products = Product::paginate(10);
            }

            if (count($products) === 0) {
                return response()->json([
                    'message' => 'No products found'
                ], 404);
            } else {
                return response()->json([
                    'respond' => $products
                ], 200);
            }
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Internal Serval Error',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    //save products
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'slug' => 'required|string|max:255',
        ]);

        try {
            Product::create($request->all());

            return response()->json([
                'message' => 'Product created successfully'
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Product creation failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    //get single post
    public function show($id)
    {
        try {
            $product = Product::find($id);
            if ($product) {
                return response()->json([
                    'respond' => $product
                ], 200);
            } else {
                return response()->json([
                    'message' => 'Product not found'
                ], 404);
            }
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Internal Serval Error',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    //update product
    public function update(Request $request, $id)
    {
        //get request data
        $product = Product::find($id);
        if ($product) {
            //validate product
            $this->validate($request, [
                'name' => 'string|max:255',
                'description' => 'string',
                'price' => 'numeric',
                'slug' => 'string|max:255',
            ]);
            try {
                $product->update($request->all());
                return response()->json([
                    'message' => 'Product updated successfully',
                    'respond' => $product

                ], 200);
            } catch (Exception $e) {
                return response()->json([
                    'message' => 'Internal Serval Error',
                    'error' => $e->getMessage()
                ], 500);
            }
        } else {
            return response()->json([
                'message' => 'Product not found'
            ], 404);
        }
    }

    //delete product 
    public function destroy(Request $request, $id)
    {
        $product = Product::find($id);
        if ($product) {
            try {
                $product->delete();
                return response()->json([
                    'message' => 'Product deleted successfully'
                ], 200);
            } catch (Exception $e) {
                return response()->json([
                    'message' => 'Internal Serval Error',
                    'error' => $e->getMessage()
                ], 500);
            }
        } else {
            return response()->json([
                'message' => 'Product not found'
            ], 404);
        }
    }
}
