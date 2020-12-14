<?php

namespace wpoiLibs\src\helpers;

class helper
{
  public static function camelize(string $string, $search = '_', bool $first_letter_big = false): string
  {
    if (!is_string($search) && !is_array($search)) throw new \Exception('Delimiter should be either string or array, ' . gettype($search) . ' given');
    $delimiter = is_string($search) ? $search : implode('', $search);
    $camelized = str_replace($search, '', ucwords($string, $delimiter));
    return $first_letter_big === true ? $camelized : lcfirst($camelized);
  }
}
