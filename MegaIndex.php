<?php
/**
 * Created by JetBrains PhpStorm.
 * User: devel
 * Date: 5/28/13
 * Time: 6:17 PM
 * To change this template use File | Settings | File Templates.
 */
use Entities\Domain,
  Entities\Words,
  Entities\Region,
  Entities\YP,
  Entities\Frequency,
  Entities\Task,
  Entities\TaskList;

require_once("Tasks/PriceTask.php");
require_once("Tasks/YpTask.php");
require_once("Tasks/FrequencyTask.php");
require_once("MegaIndexConfig.php");

class MegaIndex
{
  private $time;
  private $em;

  private $region;
  private $domain;
  private $word;

  public function __construct($region, $url)
  {
    if (empty($region))
    {
      throw new Exception ('Region field is empty.');
    }
    if (empty($url))
    {
      throw new Exception ('Url field is empty.');
    }

    $url = trim($url);

    $db = new MegaIndexDb();
    $db->createConnection(MegaIndexConfig::$dbName, MegaIndexConfig::$dbDriver, MegaIndexConfig::$dbHost, MegaIndexConfig::$dbLogin, MegaIndexConfig::$dbPassword);
    $this->em = MegaIndexDb::getEntityManager();

    $this->time = time() - MegaIndexConfig::$validTime;

    $this->region = $this->em->getRepository('Entities\Region')->findBy(array('code' => (int)$region));
    if (empty($this->region))
      throw new Exception('Table region is empty');

    $this->domain = $this->em->getRepository('Entities\Domain')->findBy(array('name' => $url));
    if (empty($this->domain))
    {
      $this->domain[0] = new Domain();
      $this->domain[0]->setName($url);
      $this->em->persist($this->domain[0]);

      $this->em->flush();
    }
  }

  public function getDomain()
  {
    return $this->domain[0];
  }

  public function getRegion()
  {
    return $this->region[0];
  }

  public function checkWords($requests)
  {
    $words = $this->em->getRepository('Entities\Words')->findBy(array('name' => $requests));
    foreach($requests as $value)
    {
      $find = false;
      foreach($words as $oneWord)
      {
        if ($oneWord->getName() == $value)
        {
          $find = true;
          break;
        }
      }
      if (!$find)
      {
        $word = new Words();
        $word->setName($value);
        $word->setPrice('0');
        $word->setDate(0);

        $this->em->persist($word);
      }
    }

    $this->em->flush();

    $words = $this->em->getRepository('Entities\Words')->findBy(array('name' => $requests));
    $this->word = $words;
  }

  public function taskPrice($requests, $list)
  {
    $query = "select words from Entities\Words words where words.name in (";
    $i=0;
    foreach ($requests as $word)
    {
      $query .= "?".$i.", ";
      $parameters[$i] = $word;
      $i++;
    }
    $query = substr_replace($query, "", strlen($query)-2);
    $query .= ") and words.date<".$this->time;
    $words = $this->em->createQuery($query)->setParameters($parameters)->getResult();

    if (!empty($words))
    {
      foreach ($words as $word)
      {
        $names[] = $word->getName();
      }
      $task = new PriceTask($names);
      $this->saveTask($task, $list);
    }
  }

  private function getWord($request)
  {
    if (empty($this->word))
    {
      $this->checkWords($request);
    }

    foreach ($this->word as $oneWord)
    {
      if ($oneWord->getName() == $request)
      {
        $word = $oneWord;
        break;
      }
    }

    return $word;
  }

  private function saveTask($task, $list)
  {
    $taskRow = new Task();
    $taskRow->setList($list);
    $taskRow->setClassName(get_class($task));
    $taskRow->setBody(serialize($task));

    $this->em->persist($taskRow);
    $this->em->flush();
  }

  public function taskYp($request, $list)
  {
    $word = $this->getWord($request);

    $yp = $this->em->createQuery("select yp from Entities\YP yp where yp.domain=".$this->domain[0]->getId()." and yp.region=".$this->region[0]->getId()." and yp.word=".$word->getId())->getResult();
    if (empty($yp))
    {
      $yp = new YP();
      $yp->setDate(0);
      $yp->setDomain($this->domain[0]);
      $yp->setRegion($this->region[0]);
      $yp->setPosition('-1');
      $yp->setWord($word);

      $this->em->persist($yp);
      $this->em->flush();
    }
    else
    {
      $yp = $yp[0];
    }

    if ($this->time > $yp->getDate())
    {
      $task = new YpTask($yp->getId(), $request);
      $this->saveTask($task, $list);
    }
  }

  public function taskFrequency($request, $list)
  {
    $word = $this->getWord($request);

    $freq = $this->em->createQuery("select freq from Entities\Frequency freq where freq.region=".$this->region[0]->getId()." and freq.word=".$word->getId())->getResult();
    if (empty($freq))
    {
      $freq = new Frequency();
      $freq->setWord($word);
      $freq->setRegion($this->region[0]);
      $freq->setDate(0);
      $freq->setFrequency('-1');

      $this->em->persist($freq);
      $this->em->flush();
    }
    else
    {
      $freq = $freq[0];
    }

    if ($this->time > $freq->getDate())
    {
      $task = new FrequencyTask($freq->getId(), $request);
      $this->saveTask($task, $list);
    }
  }

  public function createNewList()
  {
    $list = new Entities\TaskList();
    $list->setStatus('wait');

    $this->em->persist($list);
    $this->em->flush();

    return $list;
  }
}