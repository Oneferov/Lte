<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use DataTables;

class RouteController extends Controller
{
    public function index()
    {
        return view('route.index');
    }

    public function list()
    {
        return Datatables::of(User::query())->make(true);
    }
}
