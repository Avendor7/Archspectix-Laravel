<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

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

