<?php

namespace Intimation\Catalyst;

class Block
{
    public static function anchor(array $block): string
    {
        if (!isset($block['attrs']['anchor'])) {
            return '';
        }

        return $block['attrs']['anchor'];
    }

    public static function classes(string $name, array $block): string
    {
        $classes = [
            'catalyst-block',
            'catalyst-block-' . $name,
            $name,
        ];

        if (!empty($block['className'])) {
            $classes[] = $block['className'];
        }
        if (!empty($block['align'])) {
            $classes[] = ' align' . $block['align'];
        }

        return implode(' ', $classes);
    }
}
