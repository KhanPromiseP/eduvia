<?php
namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\User;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckoutController extends Controller
{
    public function show(Product $product)
    {
        return view('products.show', compact('product'));
    }

    public function pay(Request $request, Product $product)
    {
        // Example: integrate with Stripe, PayPal, etc.
        // For demo, mark payment as completed immediately
        $payment = Payment::create([
            'user_id' => Auth::id(),
            'product_id' => $product->id,
            'amount' => $product->price,
            'status' => 'completed', // change after real payment
        ]);

        return redirect()->route('products.download', $product)
                         ->with('success', 'Payment completed! You can now download.');
    }
}
