<?php
/**
 * Created by JetBrains PhpStorm.
 * User: devel
 * Date: 6/3/13
 * Time: 3:36 PM
 * To change this template use File | Settings | File Templates.
 */
require_once('MegaIndexApi.php');
require_once('MegaIndexDb.php');

abstract class AbstractTask
{
  private $complete = false;

  public function isComplete()
  {
    return $this->complete;
  }

  protected function setComplete()
  {
    if (!$this->isComplete())
    {
      $this->complete = true;
    }
  }

  abstract function run();
}
