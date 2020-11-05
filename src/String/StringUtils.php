<?php

namespace Corviz\String;

class StringUtils
{
    /**
     * Checks if $haystack ends with $needle.
     *
     * @param string $haystack
     * @param string $needle
     * @param bool   $caseSensitive
     *
     * @return bool
     */
    public static function endsWith(
        string $haystack,
        string $needle,
        $caseSensitive = true
    ): bool {
        $needleLength = strlen($needle);
        $cmp = substr_compare(
            $haystack,
            $needle,
            -($needleLength),
            $needleLength,
            !$caseSensitive
        );

        return $cmp === 0;
    }

    /**
     * Checks if $haystack starts with $needle.
     *
     * @param string $haystack
     * @param string $needle
     * @param bool   $caseSensitive
     *
     * @return bool
     */
    public static function startsWith(
        string $haystack,
        string $needle,
        $caseSensitive = true
    ): bool {
        $fName = $caseSensitive ? 'strpos' : 'stripos';

        return $fName($haystack, $needle) === 0;
    }
}
