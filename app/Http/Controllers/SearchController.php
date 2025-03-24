<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->input('value');
        $results = []; // Your search logic here

        return Inertia::render('Search', [
            'query' => $query,
            'results' => $results
        ]);
    }

}
