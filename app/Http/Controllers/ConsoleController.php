<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FileService;

class ConsoleController extends Controller
{
    public function __construct(FileService $fileService)
    {
        $this->service = $fileService;
    }

    public function execute(Request $request)
    {
        $command = $request->command;
        $last_line = passthru($command, $retval);
        echo $last_line;
    }

    public function getLs(Request $request) 
    {
        $path = $request->path;
        $result = my_sort($this->service->getFilesCurrentFolder($path));

        return response()->json($result);
    }
}
