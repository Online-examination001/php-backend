<?php
namespace App\Http\Controllers\API\v1;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductBoughtResource;
use App\Institution;
use App\Notifications\PurchaseConfirmationNotification;
use App\Product;
use App\ProductPurchased;
use Illuminate\Support\Facades\Auth;

class PurchaseController extends Controller
{

    public function index()
    {
        $purchased = ProductPurchased::all();
        return ProductBoughtResource::collection($purchased);
    }
    public function show($id)
    {

        $purchased = ProductPurchased::findOrFail($id);
        if ($purchased == null) {
            $product = Product::findOrFail('id' == $purchased->product_id);
            return response()->json([
                'purchased_info' => $purchased,
                'purchased_info' => $product
            ], 200);
        }
    }
    public function create(Request $request)
    {
        $user = Auth::user();
        $Institution = Institution::where('user_id', $user->id);
        $purchased_qs = ProductPurchased::where('institution_id' , $Institution->id);
        if ($purchased_qs  != null) {
            return response()->json([
                'Message' => 'You have already subscribed to a package',
                'Question' => 'Do you want to delete the already made subscription'
            ]);
        } else {
            $to_purchase = new Institution();
            $to_purchase->product_id = $request->product_id;
            $to_purchase->institution_id = $Institution->id;
            $to_purchase->save();
            $user->notify(new PurchaseConfirmationNotification());
            $message = 'Purchase made succesfully';
            return response()->json(compact('message', 'to_purchase'), 200);
        }
    }
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $Institution = Institution::where('id' , $user->id);
        $purchased_to_update = ProductPurchased::where('id' , $id);
        $product = Product::where('id' , $purchased_to_update->product_id);
        if ($Institution->id != $purchased_to_update->institution_id) {
            return response()->json([
                'Message' => 'You are not allowed to update this purchase'
            ]);
        } else {
            $purchased_to_update->id = $id;
            $purchased_to_update->product_id = $request->product_id;
            $purchased_to_update->institution_id = $request->institution_id;
            $purchased_to_update->update();
            $Institution->subscribed_api_left = $Institution->subscribed_api_left + $product->quantity;
            $Institution->update();
            $message = 'Purchase made succesfully';
            return response()->json(compact('message', 'to_purchase'), 200);
        }
    }

    public function delete(Request $request, $id)
    {
        $user = Auth::user();
        $Institution = Institution::where('id' , $user->id);
        $purchased_to_delete = ProductPurchased::where('id' , $id);
        $product = Product::where('id' , $purchased_to_delete->product_id);
        if ($Institution->id != $purchased_to_delete->institution_id) {
            return response()->json([
                'Message' => 'You are not allowed to delete this purchase'
            ]);
        }
        $purchased_to_delete->delete();
        $Institution->subscribed_api_left = 0;
        $Institution->save();
        $message = 'Purchase deleted succesfully';
        return response()->json(compact('message'), 200);
    }


}