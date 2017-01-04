<?php

class Autoloader {

  public static function load($sClassName) {
    $sClassFile = str_replace((DIRECTORY_SEPARATOR.'core'), '', __DIR__).DIRECTORY_SEPARATOR.str_replace('\\', DIRECTORY_SEPARATOR, $sClassName).'.class.php';
        
    if ( file_exists($sClassFile) ) {
      require_once $sClassFile;
      return TRUE;
    }
    return FALSE; 
  }
}
spl_autoload_register('Autoloader::load');

