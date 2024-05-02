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
            'nationality' => 'required|string',
            'city' => 'required|string',
            'company_name' => 'required|string',
            'salary' => 'required|numeric',
            'bank' => 'required|string',
            'liabilities' => 'required|string',
            'liabilities_description' => 'nullable|string',
            'car_brand' => 'required|string',
            'car_name' => 'required|string',
            'traffic_violations' => 'required|string',
        ]);

        if ($request->liabilities == 'لايوجد') {
            $data['liabilities'] = false;
        } else {
            $data['liabilities'] = true;
        }

        if ($request->nationality == 'سعودي') {
            $data['is_saudi'] = true;
        } else {
            $data['is_saudi'] = false;
        }

        if ($request->traffic_violations == 'لايوجد') {
            $data['traffic_violations'] = false;
        } else {
            $data['traffic_violations'] = true;
        }

        unset ($data['nationality']);

        Order::create($data);
    }
}
