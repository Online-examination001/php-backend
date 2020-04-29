<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductsController extends Controller
{
    public function index(){
        $products = Product:: all();
        return ProductResource::collection($products);
    }
    public function show($id)
    {
        $product = Product::findorfail($id);

        return new ProductResource($product);
    }
    public function create(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'title' => 'required|max:250',
            'description' => 'required|max:500',
            'price' => 'required',
            'quantity' => 'required',
        ]);
        $erros = $validator->errors();
        if ($validator->fails()) {
            $status = 400;
            return response()->json(compact('erros','status'),$status);
        }
        $product = new Product;
        $product->title = $request->title;
        $product->description = $request->description;
        $product->price = $request->price;
        $product->quantity = $request->quantity;
        $product->save();
        $msg= 'Product added';
        $status = 201;
        return response()->json(['message'=>$msg,'status' => $status],$status);
    }
    public function update(Request $request,$id)
    {
        $product = Product::findorfail($id);
        $validator = Validator::make($request->all(), [
            'title' => 'max:250',
            'description' => 'max:500',
        ]);
        $erros = $validator->errors();
        if ($validator->fails()) {
            $status = 400;
            return response()->json(compact('erros','status'),$status);
        }
        $product->id = $id;
        $product->title = $request->title;
        $product->description = $request->description;
        $product->price = $request->price;
        $product->update();
        $msg = 'Product updated succesflly';
        $status = 200;
        return response()->json(['message' => $msg,'status'=>$status],$status);
    }
    public function delete(Request $request, $id)
    {
        $product = Product::findorfail($id);
        $product->delete();
        $msg = 'Product deleted succesflly';
        $status = 200;
        return response()->json(['message' => $msg,'status'=>$status],$status);

    }
}
