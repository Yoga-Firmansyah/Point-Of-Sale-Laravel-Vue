<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseDetail;
use ArrayIterator;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use MultipleIterator;

class PurchaseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // $purchases = Purchase::whereBetween('date', [$request->date1, $request->date2])->orderBy($request->sortBy | 'id', $request->sortType | 'asc')->with('purchaseDetails')->paginate($request->limit | 5);
        // if ($role == "Super Admin") {
        //     if ($request->startDate && $request->endDate) {
        //         $purchases = Purchase::whereBetween('date', [$request->startDate, $request->endDate])->orderBy('id', 'desc')->with('purchaseDetails')->get();
        //     } else {
        //         $purchases = Purchase::orderBy('id', 'desc')->with('purchaseDetails')->get();
        //     }
        // } else {
        //     if ($request->startDate && $request->endDate) {
        //         $purchases = Purchase::where('user_id', $user->id)->whereBetween('date', [$request->startDate, $request->endDate])->orderBy('id', 'desc')->with('purchaseDetails')->get();
        //     } else {
        //         $purchases = Purchase::where('user_id', $user->id)->orderBy('id', 'desc')->with('purchaseDetails')->get();
        //     }
        //}

        $userRole = Auth::user()->roles->pluck('name')[0];

        if ($userRole != "Admin") {
            return response()->json([
                'success' => false,
                'message' => 'Forbidden Access!',
            ], 403);
        }
        $purchases = Purchase::orderBy('id', 'desc')->with('purchaseDetails')->get();
        return response()->json([
            'success'       => true,
            'message'       => 'List Data Purchases',
            'purchases'    => $purchases,
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
        $userRole = Auth::user()->roles->pluck('name')[0];

        if (
            $userRole != "Admin"
        ) {
            return response()->json([
                'success' => false,
                'message' => 'Forbidden Access!',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'date'          => 'required|date',
            'total_item'    => 'required|numeric',
            'total_price'   => 'required|numeric',
            'discount'      => 'nullable|numeric',
            'grand_total'   => 'required|numeric',
            'product_id.*'  => 'required|numeric',
            'price.*'       => 'required|numeric',
            'qty.*'         => 'required|numeric',
            'subtotal.*'    => 'required|numeric',
            'product_name.*'   => 'required',
        ]);


        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        } else {
            try {
                $userId = Auth::id();
                $purchase = Purchase::create([
                    'user_id'   => $userId,
                    'date'   => $request->date,
                    'total_item'   => $request->total_item,
                    'total_price'   => $request->total_price,
                    'discount'   => $request->discount,
                    'grand_total' => $request->grand_total,
                ]);

                $mi = new MultipleIterator();
                $mi->attachIterator(new ArrayIterator($request->product_id));
                $mi->attachIterator(new ArrayIterator($request->product_name));
                $mi->attachIterator(new ArrayIterator($request->price));
                $mi->attachIterator(new ArrayIterator($request->qty));
                $mi->attachIterator(new ArrayIterator($request->subtotal));
                foreach ($mi as list($product_id, $product_name, $price, $qty, $subtotal)) {
                    PurchaseDetail::create([
                        'purchase_id'   => $purchase->id,
                        'product_id'    => $product_id,
                        'product_name'    => $product_name,
                        'price'           => $price,
                        'qty'           => $qty,
                        'subtotal'     => $subtotal
                    ]);

                    $product = Product::find($product_id);
                    $product->update([
                        'qty'       => $product->qty + $qty,
                    ]);
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Data Saved Successfully!',
                    'data' => Purchase::where('id', $purchase->id)->with('purchaseDetails')->get(),
                ], 200);
            } catch (QueryException $exception) {
                $purchase->delete();
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
    public function show(Purchase $purchase)
    {
        return response()->json([
            'success' => true,
            'message' => 'Data Purchase',
            'data' => Purchase::where('id', $purchase->id)->with('purchaseDetails')->get(),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Purchase $purchase)
    {
        $userRole = Auth::user()->roles->pluck('name')[0];

        if ($userRole != "Admin") {
            return response()->json([
                'success' => false,
                'message' => 'Forbidden Access!',
            ], 403);
        }
        return response()->json([
            'success' => true,
            'message' => 'Data Edit Purchase',
            'data' => Purchase::where('id', $purchase->id)->with('purchaseDetails')->get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Purchase $purchase)
    {
        $userRole = Auth::user()->roles->pluck('name')[0];
        $userId = Auth::id();

        if ($userRole != "Admin") {
            return response()->json([
                'success' => false,
                'message' => 'Forbidden Access!',
            ], 403);
        } else if ($userId != $purchase->user_id) {
            return response()->json([
                'success' => false,
                'message' => 'Forbidden Access!',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'date'          => 'required|date',
            'total_item'    => 'required|numeric',
            'total_price'   => 'required|numeric',
            'discount'      => 'nullable|numeric',
            'grand_total'   => 'required|numeric',
            'product_id.*'  => 'required|numeric',
            'price.*'       => 'required|numeric',
            'qty.*'         => 'required|numeric',
            'subtotal.*'    => 'required|numeric',
            'product_name.*'   => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        } else {
            try {
                $date   = $purchase->date;
                $ti = $purchase->total_item;
                $tp = $purchase->total_price;
                $d = $purchase->discount;
                $gt = $purchase->grand_total;


                $purchaseDetails = PurchaseDetail::whereRaw('purchase_id = ' . $purchase->id)->get();
                $purchase->update([
                    'user_id'   => $userId,
                    'date'   => $request->date,
                    'total_item'   => $request->total_item,
                    'total_price'   => $request->total_price,
                    'discount'   => $request->discount,
                    'grand_total' => $request->grand_total,
                ]);

                $mi = new MultipleIterator();
                $mi->attachIterator(new ArrayIterator($request->product_id));
                $mi->attachIterator(new ArrayIterator($request->product_name));
                $mi->attachIterator(new ArrayIterator($request->price));
                $mi->attachIterator(new ArrayIterator($request->qty));
                $mi->attachIterator(new ArrayIterator($request->subtotal));
                foreach ($mi as list($product_id, $product_name, $price, $qty, $subtotal)) {
                    PurchaseDetail::create([
                        'purchase_id'   => $purchase->id,
                        'product_id'    => $product_id,
                        'product_name'    => $product_name,
                        'price'           => $price,
                        'qty'           => $qty,
                        'subtotal'     => $subtotal
                    ]);

                    $product = Product::find($product_id);
                    $product->update([
                        'qty'       => $product->qty + $qty,
                    ]);
                }

                foreach ($purchaseDetails as $pd) {
                    $product = Product::find($pd->product_id);
                    $product->update([
                        'qty' => $product->qty - $pd->qty,
                    ]);
                    $pd->delete();
                };

                return response()->json([
                    'success' => true,
                    'message' => 'Data Saved Successfully!',
                    'data' => Purchase::where('id', $purchase->id)->with('purchaseDetails')->get(),
                ], 200);
            } catch (QueryException $exception) {
                $purchase->update([
                    'purchase_id'   => $date,
                    'total_item'   => $ti,
                    'total_price'   => $tp,
                    'discount'   => $d,
                    'grand_total' => $gt,
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
    public function destroy(Purchase $purchase)
    {
        $userRole = Auth::user()->roles->pluck('name')[0];
        $userId = Auth::id();

        if ($userRole != "Admin") {
            return response()->json([
                'success' => false,
                'message' => 'Forbidden Access!',
            ], 403);
        }

        try {
            $purchaseDetail = PurchaseDetail::whereRaw('purchase_id = ' . $purchase->id)->get();

            foreach ($purchaseDetail as $pd) {
                $product = Product::find($pd->product_id);
                $product->update([
                    'qty' => $product->qty - $pd->qty,
                ]);

                $pd->delete();
            };

            $purchase->delete();

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
