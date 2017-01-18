<?php

namespace ViewComponents\Core\Common;

use RuntimeException;

class StableSorting
{
    /**
     * Sorts collection items.
     *
     * This method preserves key order (stable sort) using Schwartzian Transform.
     * @see http://stackoverflow.com/questions/4353739/preserve-key-order-stable-sort-when-sorting-with-phps-uasort
     * @see http://en.wikipedia.org/wiki/Schwartzian_transform
     *
     * @param array $items
     * @param callable $compareFunction
     * @return array sorted items
     */
    public static function sort(array $items, callable $compareFunction)
    {
        # Sorting with Schwartzian Transform
        # If stable sort is not required,
        # following code can be replaced to usort($items, $compareFunction);
        $index = 0;
        foreach ($items as &$item) {
            $item = [$index++, $item];
        }
        usort($items, function ($a, $b) use ($compareFunction) {
            $result = call_user_func($compareFunction, $a[1], $b[1]);
            return $result == 0 ? $a[0] - $b[0] : $result;
        });
        foreach ($items as &$item) {
            $item = $item[1];
        }
        # End of sorting with Schwartzian Transform
        return $items;
    }

    private function __construct()
    {
    }
}