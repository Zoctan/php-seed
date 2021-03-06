<?php

namespace App\Util;

/**
 * Tree
 */
class Tree
{
    /**
     * List to tree
     * 
     * @param array $list
     * @param string $parentIdKey
     * @param string $idKey
     * @param string $childrenKey
     */
    public static function list2Tree(array $list, string $parentIdKey = 'parent_id', string $idKey = 'id', string $childrenKey = 'children')
    {
        $grouped = [];
        foreach ($list as $node) {
            $grouped[$node[$parentIdKey]][] = $node;
        }

        $fnBuilder = function ($siblings) use (&$fnBuilder, $grouped, $idKey, $childrenKey) {
            foreach ($siblings as $k => $sibling) {
                $id = $sibling[$idKey];
                if (isset($grouped[$id])) {
                    $sibling[$childrenKey] = $fnBuilder($grouped[$id]);
                }
                $siblings[$k] = $sibling;
            }
            return $siblings;
        };

        return $fnBuilder($grouped[0]);
    }

    /**
     * Tree to list
     * 
     * @param array $root
     * @param string $childrenKey
     */
    public static function tree2List(array $root, string $childrenKey = 'children')
    {
        $list = [];
        foreach ($root as $key => $node) {
            if (array_key_exists($childrenKey, $node)) {
                $list = array_merge($list, self::tree2List($node[$childrenKey]));
                unset($child[$childrenKey]);
            }
            $list[] = $node;
        }
        return $list;
    }
}
