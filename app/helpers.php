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

if (!function_exists('flattenKeys')) {
    function flattenKeys(array $array, string $prefix = ''): array
    {
        $keys = [];
        foreach ($array as $key => $value) {
            $fullKey = $prefix === '' ? $key : $prefix . '.' . $key;

            if (is_array($value)) {
                $keys = array_merge($keys, flattenKeys($value, $fullKey));
            } else {
                $keys[] = $fullKey;
            }
        }
        return $keys;
    }
}
