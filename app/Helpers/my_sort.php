<?php

if(!function_exists('my_sort')) {
    function my_sort(array $array)
    {
        $arrs = [];
        $elements = [];
        foreach ($array as $key => $item) {
            if (!is_array($item)) {
                $elements[] = $item;
            } else {
                $arrs[$key] = $item;
            }
        }
        natcasesort($elements);

        return array_merge($arrs, $elements);
    }
}
