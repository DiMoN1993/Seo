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
  public static $apiEmail = 'sagdiv@gmail.com';
  public static $apiPassword = 'VqGPOv';
  public static $dbLogin = 'root';
  public static $dbPassword = 'root';
  public static $dbDriver = 'pdo_mysql';
  public static $dbHost = 'localhost';
  public static $dbName = 'megaindex';
  public static $testDbName = 'megaindex3';
  public static $validTime = 432000; //5 days
  /*public $validTime;

  public function __construct()
  {
    $this->validTime = 3600*24*5;
  }*/
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