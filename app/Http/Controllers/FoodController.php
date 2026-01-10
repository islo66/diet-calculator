<?php

namespace App\Http\Controllers;

use App\Models\Food;
use Illuminate\Http\Request;

class FoodController extends Controller
{
    public function index()
    {
        $foods = Food::query()
            ->orderBy('name')
            ->paginate(25);

        return view('foods.index', compact('foods'));
    }
}
