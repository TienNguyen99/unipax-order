<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;


class HomeClientController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('client.home');
    }
    // API 
    public function getData()
    {
        $data = Order::with('deliveries')
            ->get();
        return response()->json([
            'data' => $data
        ]);
    }
}
