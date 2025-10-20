<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Property;

class PropertyController extends Controller
{
    public function index(Request $request)
    {
        $q = Property::query();

        if ($request->filled('name')) {
            $name = $request->input('name');
            $q->where('name', 'LIKE', "%{$name}%");
        }
        foreach (['bedrooms','bathrooms','storeys','garages'] as $field) {
            if ($request->filled($field)) {
                $q->where($field, (int)$request->input($field));
            }
        }

        if ($request->filled('price_min')) {
            $q->where('price', '>=', (int)$request->input('price_min'));
        }
        if ($request->filled('price_max')) {
            $q->where('price', '<=', (int)$request->input('price_max'));
        }

        $results = $q->get([
            'id','name','price','bedrooms','bathrooms','storeys','garages'
        ]);

        return response()->json($results);
    }
}
