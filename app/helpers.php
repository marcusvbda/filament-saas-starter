<?php


if (!function_exists('implodeSuffix')) {
    function implodeSuffix(string $sep, array $items, string $final = ' e '): string
    {
        $count = count($items);

        if ($count === 0) return '';
        if ($count === 1) return $items[0];
        if ($count === 2) return $items[0] . $final . $items[1];

        return implode($sep, array_slice($items, 0, -1)) . $final . end($items);
    }
}
