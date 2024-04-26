<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'age' => 'required|numeric',
            'mobile_number' => 'required|string',
            'nationality' => 'required|string',
            'city' => 'required|string',
            'company_name' => 'required|string',
            'salary' => 'required|numeric',
            'bank' => 'required|string',
            'liabilities' => 'required|numeric',
            'liabilities_description' => 'required|string',
            'installment' => 'required|numeric',
            'car_brand' => 'required|string',
            'car_name' => 'required|string',
        ]);

        Order::create($request->validated());
    }
}
