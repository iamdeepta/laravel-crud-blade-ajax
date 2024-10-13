<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
    {
        $data = $this->getData();
        return view('product_form', ['data' => $data]);
    }

    public function submit(Request $request)
    {
        $request->validate([
            'product_name' => 'required|string',
            'quantity_in_stock' => 'required|integer',
            'price_per_item' => 'required|numeric',
            'temp_datetime' => 'nullable|string',
        ]);

        $data = $this->getData();
        $total_value_number = $request->quantity_in_stock * $request->price_per_item;
        $datetime_submitted = now()->format('Y-m-d H:i:s');

        $newData = [
            'product_name' => $request->product_name,
            'quantity_in_stock' => $request->quantity_in_stock,
            'price_per_item' => $request->price_per_item,
            'datetime_submitted' => $datetime_submitted,
            'total_value_number' => $total_value_number,
        ];

        if ($request->temp_datetime !== null) {
            // Edit existing data
            for($i=0; $i<count($data); $i++){
                if($data[$i]['datetime_submitted']==$request->temp_datetime){
                    $data[$i] = $newData;
                    break;
                }
            }
        } else {
            // Add new data
            $data[] = $newData;
        }

        Storage::disk('local')->put('products.json', json_encode($data, JSON_PRETTY_PRINT));

        return response()->json(['data' => $newData, 'total_sum' => array_sum(array_column($data, 'total_value_number'))]);
    }

    private function getData()
    {
        if (Storage::disk('local')->exists('products.json')) {
            return json_decode(Storage::disk('local')->get('products.json'), true);
        }

        return [];
    }
}
