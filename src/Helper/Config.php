<?php

namespace App\Helper;

class Config
{

  protected $config;

  public function __construct()
  {
    $configFile = dirname(__DIR__) . "/../config/config.php";

    if (file_exists($configFile)) {
        $this->config = require $configFile;
    } else {
        throw new \Exception(
            'Make sure that config.php is present in app/config.'
        );
    }


  }
  /**
   * Returns the API Config Details for TheTVDB
   * @return Array [Array of config options]
   */
  public function getTvdbConfig():Array
  {
    return $this->config['tvdb_config'];
  }

  /**
   * Returns the Image path to save images for Series and Episodes
   * @return String [Image path]
   */
  public function getTvdbImgPath():String
  {
    return dirname(__DIR__).'/../'.$this->config['imgDir'].$this->config['tvdbDir'];
  }

  /**
   * Returns the MySQL DB Config Options
   * @return Array [DB Config]
   */
  public function getDbConfig():Array
  {
    return $this->config['db_config'];
  }
}
