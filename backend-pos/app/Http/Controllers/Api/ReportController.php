<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Sale;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index()
    {
        $total_products = Product::count();
        $total_sales = Sale::whereMonth('created_at', date('m'))->count();
        $total_purchases = Purchase::whereMonth('date', date('m'))->count();

        return response()->json([
            'success'       => true,
            'message'       => 'List Data',
            'total_products'    => $total_products,
            'total_sales'    => $total_sales,
            'total_purchases'    => $total_purchases,
        ]);
    }
}
