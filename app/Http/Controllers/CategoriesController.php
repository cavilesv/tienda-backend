<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoriesController extends Controller
{
    public function getListCategories(Request $request)
    {
        return Category::all();
    }
}
