<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Product;
use Illuminate\Http\Request;

class ProductsController extends Controller
{
    public function index(Request $request){
        $products = Product:: all();
        return ProductResource::collection($products);
    }
    public function show(Request $request,$id)
    {
        $product = Product::findorfail($id);

        return new ProductResource($product);
    }
    public function create(Request $request)
    {
        $product = new Product;
        $validated =$request->validate([
            'title'=>'required|max:250',
            'description' => 'required|max:500',
            'price' => 'required',
        ]);
        $product->title = $request->title;
        $product->description = $request->description;
        $product->price = $request->price;
        $product->save();
        $msg= 'Product added';
        return response()->json(['message'=>$msg]);
    }
    public function update(Request $request,$id)
    {
        $product = Product::findorfail($id);
        $validated = $request->validate([
            'title' => 'max:250',
            'description' => 'max:500',
        ]);
        $product->id = $id;
        $product->title = $request->title;
        $product->description = $request->description;
        $product->price = $request->price;
        $product->update();
        $msg = 'Product updated succesflly';
        return response()->json(['message' => $msg]);
    }
    public function delete(Request $request, $id)
    {
        $product = Product::findorfail($id);
        $product->delete();
        $msg = 'Product deleted succesflly';
        return response()->json(['message' => $msg]);

    }
}
