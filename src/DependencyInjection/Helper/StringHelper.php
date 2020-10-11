<?php
declare(strict_types=1);

namespace App\DependencyInjection\Helper;

class StringHelper
{

    /**
     * Limit the number of words in a string.
     *
     * @param  string  $value
     * @param  int  $words
     * @param  string  $end
     * @return string
     */
    public static function words($value, $words = 100, $end = '...'): string
    {
        preg_match('/^\s*+(?:\S++\s*+){1,'.$words.'}/u', $value, $matches);

        if (! isset($matches[0]) || mb_strlen($value) === mb_strlen($matches[0])) {
            return $value;
        }

        return rtrim($matches[0]).$end;
    }

}