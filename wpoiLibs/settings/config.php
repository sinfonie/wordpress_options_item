<?php

namespace wpoiLibs\settings;

class settings
{

  public static $itemClassPath = '\wpoiLibs\items\\';

  public static function getItemClassPath()
  {
    return self::$itemClassPath;
  }
}
