<?php
/**
 * Created by JetBrains PhpStorm.
 * User: devel
 * Date: 4/26/13
 * Time: 1:33 PM
 * To change this template use File | Settings | File Templates.
 */

include_once ('MegaIndex.php');

class MegaIndexTest extends PHPUnit_Framework_TestCase
{
  private $_api;
  private $_array = array ('автомобиль', 'космические рейнджеры');
  private $_str = 'космические рейнджеры';
  private $_internalType = 'string';
  private $_msg = 'Expected exception has not been call.';

  public function __construct ()
  {
    $this->_api = new MegaIndex('ru.wikipedia.org','sagdiv@gmail.com', 'VqGPOv');
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
    $this->assertInternalType('array', $this->_array);
    $this->assertInternalType('array', $this->_api->getPrice($this->_array));
    foreach ($this->_array as $value)
    {
      $this->assertArrayHasKey($value, $this->_api->getPrice($this->_array));
      $this->assertNotEmpty($value);
    }
  }

  public function testWordStatException() {
    try {
      $this->_api->getWordStat($this->_array);
    }

    catch (Exception $expected) {
      return;
    }

    $this->fail($this->_msg);
  }

  public function testYandexPosException() {
    try {
      $this->_api->getYandexPosition($this->_array);
    }

    catch (Exception $expected) {
      return;
    }

    $this->fail($this->_msg);
  }
}
