<?php
/**
 * Created by JetBrains PhpStorm.
 * User: devel
 * Date: 4/26/13
 * Time: 1:33 PM
 * To change this template use File | Settings | File Templates.
 */

require_once('../MegaIndexApi.php');
require_once("../MegaIndexConfig.php");

class MiApiTest extends PHPUnit_Framework_TestCase
{
  private $_api;
  private $_array = array ('автомобиль', 'космические рейнджеры');
  private $_str = 'космические рейнджеры';
  private $_internalType = 'string';

  public function __construct ()
  {
    $this->_api = MegaIndexApi::getApi('ru.wikipedia.org', '213', MegaIndexConfig::$apiEmail, MegaIndexConfig::$apiPassword);
  }

  public function testWordStatReturnInternalType()
  {
    $this->assertInternalType($this->_internalType, $this->_api->getWordStat($this->_str));
    $this->assertNotEmpty($this->_api->getWordStat($this->_str));
  }

  public function testPriceReturnInternalType()
  {
    $this->assertInternalType($this->_internalType, $this->_api->getPrice($this->_str));
    $this->assertNotEmpty($this->_api->getPrice($this->_str));
  }

  public function testYandexPosReturnInternalType()
  {
    $this->assertInternalType($this->_internalType, $this->_api->getYandexPosition($this->_str));
    $this->assertNotEmpty($this->_api->getYandexPosition($this->_str));
  }

  public function testPriceReturnArray()
  {
    $answer = $this->_api->getPrice($this->_array);
    $this->assertInternalType('array', $answer);
    foreach ($this->_array as $value)
    {
      $this->assertNotEmpty($value);
    }
  }

  /**
   * @expectedException Exception
   */
  public function testWordStatException()
  {
    $this->_api->getWordStat($this->_array);
  }

  /**
   * @expectedException Exception
   */
  public function testYandexPosException()
  {
     $this->_api->getYandexPosition($this->_array);
  }
}
