<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\User;
use ArrayIterator;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use MultipleIterator;

class SaleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // if ($userRole == "Super Admin" ||  $userRole == "Admin") {
        //     if ($request->startDate && $request->endDate) {
        //         $sales = Sale::whereBetween('date', [$request->startDate, $request->endDate])->orderBy('id', 'desc')->with('saleDetails')->get();
        //     } else {
        //         $sales = Sale::orderBy('id', 'desc')->with('saleDetails')->get();
        //     }
        // } else {
        //     if ($request->startDate && $request->endDate) {
        //         $sales = Sale::where('user_id', $user->id)->whereBetween('date', [$request->startDate, $request->endDate])->orderBy('id', 'desc')->with('saleDetails')->get();
        //     } else {
        //         $sales = Sale::where('user_id', $user->id)->orderBy('id', 'desc')->with('saleDetails')->get();
        //     }
        // }
        $user = Auth::user();
        $userRole = Auth::user()->roles->pluck('name')[0];
        if ($userRole == "Admin") {
            $sales = Sale::orderBy('id', 'desc')->with('saleDetails')->get();
        } else {
            $sales = Sale::where('user_id', $user->id)->orderBy('id', 'desc')->with('saleDetails')->get();
        }
        return response()->json([
            'success'  => true,
            'message'  => 'List Data Sales',
            'sales'    => $sales
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'total_item'    => 'required|numeric',
            'total_price'   => 'required|numeric',
            'discount'      => 'nullable|numeric',
            'grand_total'   => 'required|numeric',
            'pay'           => 'required|numeric',
            'change'        => 'required|numeric',
            'product_id.*'  => 'required|numeric',
            'sell_price.*'  => 'required|numeric',
            'qty.*'         => 'required|numeric',
            'product_discount.*'    => 'required|numeric',
            'subtotal.*'    => 'required|numeric',
            'product_name.*'   => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        } else {
            try {
                $userId = Auth::id();

                $sale = Sale::create([
                    'user_id'   => $userId,
                    'total_item'   => $request->total_item,
                    'total_price'   => $request->total_price,
                    'discount'   => $request->discount,
                    'grand_total' => $request->grand_total,
                    'pay' => $request->pay,
                    'change' => $request->change,
                ]);

                $mi = new MultipleIterator();
                $mi->attachIterator(new ArrayIterator($request->product_id));
                $mi->attachIterator(new ArrayIterator($request->product_name));
                $mi->attachIterator(new ArrayIterator($request->sell_price));
                $mi->attachIterator(new ArrayIterator($request->product_discount));
                $mi->attachIterator(new ArrayIterator($request->qty));
                $mi->attachIterator(new ArrayIterator($request->subtotal));
                foreach ($mi as list($product_id, $product_name, $sell_price, $product_discount, $qty, $subtotal)) {
                    SaleDetail::create([
                        'sale_id'       => $sale->id,
                        'product_id'    => $product_id,
                        'product_name'  => $product_name,
                        'sell_price'    => $sell_price,
                        'qty'           => $qty,
                        'discount'      => $product_discount,
                        'subtotal'      => $subtotal
                    ]);

                    $product = Product::find($product_id);
                    $product->update([
                        'qty'       => $product->qty - $qty,
                    ]);
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Data Saved Successfully!',
                    'data' => Sale::where('id', $sale->id)->with('saleDetails')->get(),
                ], 200);
            } catch (QueryException $exception) {
                $sale->delete();
                return response()->json([
                    'success' => false,
                    'message' => 'Data Failed to Save!',
                    'error' => $exception->errorInfo[2]
                ], 500);
            }
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Sale $sale)
    {
        $data = Sale::where('id', $sale->id)->with('saleDetails')->get();
        $user = User::where('id', $sale->user_id)->with('roles')->get();
        return response()->json([
            'success' => true,
            'message' => 'Data Sale',
            'data' => $data,
            'user' => $user,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Sale $sale)
    {
        return response()->json([
            'success' => true,
            'message' => 'Data Edit Sale',
            'data' => Sale::where('id', $sale->id)->with('saleDetails')->get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Sale $sale)
    {
        $userRole = Auth::user()->roles->pluck('name')[0];
        $userId = Auth::id();

        if ($userRole != "Admin" && $userId != $sale->user_id) {
            return response()->json([
                'success' => false,
                'message' => 'Forbidden Access!',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'total_item'    => 'required|numeric',
            'total_price'   => 'required|numeric',
            'discount'      => 'nullable|numeric',
            'grand_total'   => 'required|numeric',
            'pay'           => 'required|numeric',
            'change'        => 'required|numeric',
            'product_id.*'  => 'required|numeric',
            'sell_price.*'  => 'required|numeric',
            'qty.*'         => 'required|numeric',
            'product_discount.*'    => 'required|numeric',
            'subtotal.*'    => 'required|numeric',
            'product_name.*'   => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        } else {
            try {
                $sale = Sale::findOrFail($sale->id);
                $ti = $sale->total_item;
                $tp = $sale->total_price;
                $d = $sale->discount;
                $gt = $sale->grand_total;
                $pay = $sale->pay;
                $change = $sale->change;

                $sale->update([
                    'user_id'   => $userId,
                    'total_item'   => $request->total_item,
                    'total_price'   => $request->total_price,
                    'discount'   => $request->discount,
                    'grand_total' => $request->grand_total,
                    'pay' => $request->pay,
                    'change' => $request->change,
                ]);

                $saleDetails = SaleDetail::whereRaw('sale_id = ' . $sale->id)->get();

                foreach ($saleDetails as $sd) {
                    $product = Product::find($sd->product_id);
                    $product->update([
                        'qty' => $product->qty + $sd->qty,
                    ]);
                    $sd->delete();
                };

                $mi = new MultipleIterator();
                $mi->attachIterator(new ArrayIterator(explode(",", $request->product_id)));
                $mi->attachIterator(new ArrayIterator(explode(",", $request->product_name)));
                $mi->attachIterator(new ArrayIterator(explode(",", $request->sell_price)));
                $mi->attachIterator(new ArrayIterator(explode(",", $request->product_discount)));
                $mi->attachIterator(new ArrayIterator(explode(",", $request->qty)));
                $mi->attachIterator(new ArrayIterator(explode(",", $request->subtotal)));
                foreach ($mi as list($product_id, $product_name, $sell_price, $product_discount, $qty, $subtotal)) {
                    SaleDetail::create([
                        'sale_id'       => $sale->id,
                        'product_id'    => $product_id,
                        'product_name'  => $product_name,
                        'sell_price'    => $sell_price,
                        'qty'           => $qty,
                        'discount'      => $product_discount,
                        'subtotal'      => $subtotal
                    ]);

                    $product = Product::find($product_id);
                    $product->update([
                        'qty'       => $product->qty - $qty,
                    ]);
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Data Updated Successfully!',
                    'data' => Sale::where('id', $sale->id)->with('saleDetails')->get(),
                ], 200);
            } catch (QueryException $exception) {
                $sale->update([
                    'total_item'    => $ti,
                    'total_price'   => $tp,
                    'discount'      => $d,
                    'grand_total'   => $gt,
                    'pay'           => $pay,
                    'change'        => $change,
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Data Failed to Save!',
                    'error' => $exception->errorInfo[2]
                ], 500);
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Sale $sale)
    {
        $userRole = Auth::user()->roles->pluck('name')[0];
        $userId = Auth::id();

        if ($userRole != "Admin" && $userId != $sale->user_id) {
            return response()->json([
                'success' => false,
                'message' => 'Forbidden Access!',
            ], 403);
        }
        try {
            $saleDetails = SaleDetail::whereRaw('sale_id = ' . $sale->id)->get();

            foreach ($saleDetails as $sd) {
                $product = Product::find($sd->product_id);
                $product->update([
                    'qty' => $product->qty + $sd->qty,
                ]);

                $sd->delete();
            };

            $sale->delete();

            return response()->json([
                'status' => 'success'
            ], 200);
        } catch (QueryException $exception) {
            return response()->json([
                'status' => 'error',
                'error' => $exception->errorInfo[2]
            ], 500);
        }
    }
}
