<?php

namespace App\Http\Controllers;

use App\Models\Delivery;
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
    // API dữ liệu delivery
    public function getData()
    {
        $data = Delivery::all();

        return response()->json([
            'data' => $data
        ]);
    }
}
