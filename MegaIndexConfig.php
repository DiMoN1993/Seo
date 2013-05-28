<?php
/**
 * Created by JetBrains PhpStorm.
 * User: devel
 * Date: 5/28/13
 * Time: 3:14 PM
 * To change this template use File | Settings | File Templates.
 */
class MegaIndexConfig
{
  public $apiEmail = 'sagdiv@gmail.com';
  public $apiPassword = 'VqGPOv';
  public $dbLogin = 'root';
  public $dbPassword = 'root';
  public $dbDriver = 'pdo_mysql';
  public $dbHost = 'localhost';
  public $dbName = 'megaindex';
  public $testDbName = 'megaindex3';
  public $validTime;

  public function __construct()
  {
    $this->validTime = 3600*24*5;
  }
}
/*
$configuration = array(
  'ApiEmail' => 'sagdiv@gmail.com',
  'ApiPassword' => 'VqGPOv',
  'DbLogin' => 'root',
  'DbPassword' => 'root',
  'DbDriver' => 'pdo_mysql',
  'DbHost' => 'localhost',
  'DbName' => 'megaindex'
);*/