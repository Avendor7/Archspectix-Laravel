<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Response;
use Inertia\Inertia;

class PackageController extends Controller
{
    public function alrDetails(Request $request): Response
    {
        $query = $request->input('value');

        return Inertia::render('PackageViewALR', [
            'query' => $query
        ]);
    }

    public function aurDetails(Request $request): Response
    {
        $query = $request->input('value');

        return Inertia::render('PackageViewAUR', [
            'query' => $query
        ]);
    }
}

