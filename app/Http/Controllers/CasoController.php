<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CasoController extends Controller
{
    public function index()
    {
        return view('casos.index');
    }

    public function create()
    {
        return view('casos.create');
    }

    public function show(string $expediente)
    {
        return view('casos.show', compact('expediente'));
    }

    public function reasignar(string $expediente)
    {
        return view('casos.reasignar', compact('expediente'));
    }
}
