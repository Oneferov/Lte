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
        $path = $request->path;
        $arr_commands = explode(" ", $command);
        if (count($arr_commands) > 1 && $arr_commands[count($arr_commands)-2] == 'cd') {
            $good_folder = false;
            $result = $this->service->getFilesCurrentFolder($path);
            $search_folder = $arr_commands[count($arr_commands)-1];
            foreach ($result['folders'] as $item) {
                if ($item == $search_folder) {
                    $good_folder = true;
                    break;
                }
            }
            if (!$good_folder) return response()->json(['error' => true, 'message' => "Папка '$search_folder' не найдена"]);
        }

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
