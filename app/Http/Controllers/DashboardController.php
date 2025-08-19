<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class DashboardController extends Controller
{
 

     public function index()
    {
        // get all products (or only active ones)
        $products = Product::all();

        return view('dashboard', compact('products'));
    }
}
