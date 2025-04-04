<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Inertia\Response;
use Inertia\Inertia;
use stdClass;
class PackageController extends Controller
{
    public function index(Request $request){
        $query = $request->input('value');

        return Inertia::render('PackageViewALR', [
            'query' => $query
        ]);
    }

}
