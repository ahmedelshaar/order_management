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
            'is_saudi' => 'required|in:غير_سعودي,سعودي',
            'city' => 'required|string',
            'company_name' => 'required|string',
            'salary' => 'required|numeric',
            'bank' => 'required|string',
            'liabilities' => 'required|in:لا_يوجد,يوجد',
            'liabilities_amount' => 'nullable|numeric',
            'car_brand' => 'required|string',
            'car_name' => 'required|string',
            'car_model' => 'required|string',
            'traffic_violations' => 'required|in:لا_يوجد,يوجد',
        ]);

        if ($data['liabilities'] === 'لا_يوجد') {
            $data['liabilities'] = false;
        }else{
            $data['liabilities'] = true;
        }

        if ($data['traffic_violations'] === 'لا_يوجد') {
            $data['traffic_violations'] = false;
        }else{
            $data['traffic_violations'] = true;
        }

        if ($data['is_saudi'] === 'غير_سعودي') {
            $data['is_saudi'] = false;
        }else{
            $data['is_saudi'] = true;
        }

        Order::create($data);

        return response()->json(['message' => 'Order created successfully']);
    }
}
