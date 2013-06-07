<?php
/**
 * Created by JetBrains PhpStorm.
 * User: devel
 * Date: 6/6/13
 * Time: 6:25 PM
 * To change this template use File | Settings | File Templates.
 */
//use Entities\YP;
require_once('AbstractTask.php');

class YpTask extends AbstractTask
{
  private $id;
  private $word;

  public function __construct($id, $word)
  {
    $this->id = $id;
    $this->word = $word;
  }

  public function run()
  {
    $position = MegaIndexApi::getApi()->getYandexPosition($this->word);
    $em = MegaIndexDb::getEntityManager();
    $yp = $em->getRepository('Entities\YP')->find($this->id);
    $yp->setDate(time());
    $yp->setPosition($position);

    $em->persist($yp);
    $em->flush();

    $this->setComplete();
  }
}
