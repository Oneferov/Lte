<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
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
