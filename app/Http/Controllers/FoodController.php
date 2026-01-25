<?php

namespace App\Http\Controllers;

use App\Models\Food;
use App\Models\FoodCategory;
use Illuminate\Http\Request;

class FoodController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $categoryId = $request->query('category_id');
        $perPage = (int) $request->query('per_page', 25);

        if (!in_array($perPage, [10, 25, 50, 100], true)) {
            $perPage = 25;
        }

        $query = Food::query()->with(['category', 'nutrient']);

        if ($q !== '') {
            $query->where('name', 'like', '%' . $q . '%');
        }

        if ($categoryId !== null && $categoryId !== '') {
            $query->where('category_id', (int) $categoryId);
        }

        $foods = $query->orderBy('name')->paginate($perPage)->withQueryString();

        $categories = FoodCategory::query()
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('foods.index', compact('foods', 'categories', 'q', 'categoryId', 'perPage'));
    }
}
