<?php
/**
 * Created by JetBrains PhpStorm.
 * User: devel
 * Date: 6/3/13
 * Time: 6:30 PM
 * To change this template use File | Settings | File Templates.
 */
require_once('AbstractTask.php');

class FrequencyTask extends AbstractTask
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
    $wordStat = MegaIndexApi::getApi()->getWordStat($this->word);
    $em = MegaIndexDb::getEntityManager();
    $freq = $em->getRepository('Entities\Frequency')->find($this->id);
    $freq->setDate(time());
    $freq->setPosition($wordStat);

    $em->persist($freq);
    $em->flush();

    $this->setComplete();
  }
}
