<?php
/**
 * Created by JetBrains PhpStorm.
 * User: devel
 * Date: 6/3/13
 * Time: 4:51 PM
 * To change this template use File | Settings | File Templates.
 */
//use Entities\Words;
require_once('AbstractTask.php');

class PriceTask extends AbstractTask
{
  private $names;

  public function __construct($names)
  {
    $this->names = $names;
  }

  public function run()
  {
    $prices = MegaIndexApi::getApi()->getPrice($this->names);
    $em = MegaIndexDb::getEntityManager();
    $words = $em->getRepository('Entities\Words')->findBy(array('name' => $this->names));
    $countWords = count($this->names);
    for ($i=0; $i<$countWords; $i++)
    {
      $words[$i]->setDate(time());
      $words[$i]->setPrice($prices[$i]);

      $em->persist($words[$i]);
    }

    $em->flush();

    $this->setComplete();
  }
}
