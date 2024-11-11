<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'age' => 'required|numeric',
            'mobile_number' => 'required',
            'is_saudi' => 'required|boolean',
            'city' => 'required|string',
            'company_name' => 'required|string',
            'salary' => 'required|numeric',
            'bank' => 'required|string',
            'liabilities' => 'required|boolean',
            'liabilities_amount' => 'required|numeric',
            'car_brand' => 'required|string',
            'car_name' => 'required|string',
            'car_model' => 'required|string',
            'traffic_violations' => 'required|boolean',
        ]);

        Order::create($data);
    }
}
