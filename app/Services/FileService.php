<?php

namespace App\Services;

class FileService
{
    public function getArrayFiles(string $path)
    {
        $all_files = scandir($path);
        $result = [];
        foreach ($all_files as $item) {
            if ($item == '.' || $item === '..' || $item === '.git') continue;
            $current_path = $path."/$item";
            if (is_file($current_path)) {
                array_unshift($result, $item);
            } else {
                $result[$item] = $this->getArrayFiles($current_path);
            }
        }

        return $result;
    }

    public function getContentFile(string $path)
    {
        return file_get_contents(base_path().$path, true);
    }

    public function saveContentFile(array $data)
    {   
        return file_put_contents(base_path().$data['path'], $data['content']);;
    }
}
