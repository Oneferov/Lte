<?php

namespace App\Http\Controllers;

use App\Services\FileService;
use Illuminate\Http\Request;


class FileController extends Controller
{
    public function __construct(FileService $fileService)
    {
        $this->service = $fileService;
    }

    public function index()
    {
        $collection = my_sort($this->service->getArrayFiles(base_path()));

        return view('file.index', compact('collection'));
    }

    public function show(Request $request)
    {
        $model = $this->service->getContentFile($request['path']);
        
        return response()->json($model);
    }

    public function save(Request $request)
    {
        $model = $this->service->saveContentFile($request->only(['path', 'content']));
        
        return response()->json($model);
    }
}
