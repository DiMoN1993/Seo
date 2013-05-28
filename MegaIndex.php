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
  Entities\Frequency;

require_once("MegaIndexApi.php");
require_once("MegaIndexDb.php");
require_once("MegaIndexConfig.php");

class MegaIndex
{
  private $time;
  private $db;
  private $em;
  private $url;
  private $api;

  private $region;
  private $domain;
  private $word;

  public function __construct($region, $url)
  {
    $this->db = new MegaIndexDb();
    $config = new MegaIndexConfig();
    $this->api = new MegaIndexApi($this->url, $config->apiEmail, $config->apiPassword);

    $this->db->createConnection($config->dbName, $config->dbDriver, $config->dbHost, $config->dbLogin, $config->dbPassword);
    $this->em = $this->db->getEntityManager();

    $this->time = time() - $config->validTime;

    $this->region = $this->em->getRepository('Entities\Region')->findBy(array('code' => (int)$region));
    if (empty($this->region))
      throw new Exception('Table region is empty');

    $this->url = trim($url);

    $this->domain = $this->em->getRepository('Entities\Domain')->findBy(array('name' => $this->url));
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

  public function getWords($request)
  {
    $request = trim($request);

    $word = $this->em->createQuery("select words from Entities\Words words where words.name=:name and words.date>=".$this->time);
    $word = $word->setParameter('name', $request)->getResult();
    if (empty($word))
    {
      $word = $this->em->createQuery("select words from Entities\Words words where words.name=:name");
      $word = $word->setParameter('name', $request)->getResult();
      if (!empty($word[0]))
      {
        $word[0]->setName($request);
        $word[0]->setPrice($this->api->getPrice($request));
        $word[0]->setDate(time());
      }
      else
      {
        $word[0] = new Words();
        $word[0]->setName($request);
        $word[0]->setPrice($this->api->getPrice($request));
        $word[0]->setDate(time());
      }
      $this->em->persist($word[0]);

      $this->em->flush();
    }
    $this->word = $word;

    return $word[0];
  }

  public function getYp($request)
  {
    if (empty($this->word[0]))
      $this->getWords($request);

    $yp = $this->em->createQuery("select yp from Entities\YP yp where yp.domain=".$this->domain[0]->getId()." and yp.region=".$this->region[0]->getId()." and yp.word=".$this->word[0]->getId()." and yp.date>=".$this->time)->getResult();
    if (empty($yp))
    {
      $yp = $this->em->createQuery("select yp from Entities\YP yp where yp.domain=".$this->domain[0]->getId()." and yp.region=".$this->region[0]->getId()." and yp.word=".$this->word[0]->getId())->getResult();
      if (!empty($yp[0]))
      {
        $yp[0]->setDate(time());
        $yp[0]->setRegion($this->region[0]);
        $yp[0]->setWord($this->word[0]);
        $yp[0]->setDomain($this->domain[0]);
        $yp[0]->setPosition($this->api->getYandexPosition($this->word[0]->getName(), $this->region[0]->getCode()));
      }
      else
      {
        $yp[0] = new YP();
        $yp[0]->setDate(time());
        $yp[0]->setRegion($this->region[0]);
        $yp[0]->setWord($this->word[0]);
        $yp[0]->setDomain($this->domain[0]);
        $yp[0]->setPosition($this->api->getYandexPosition($this->word[0]->getName(), $this->region[0]->getCode()));
      }
      $this->em->persist($yp[0]);

      $this->em->flush();
    }

    return $yp[0];
  }

  public function getFrequency($request)
  {
    if (empty($this->word[0]))
      $this->getWords($request);

    $freq = $this->em->createQuery("select freq from Entities\Frequency freq where freq.region=".$this->region[0]->getId()." and freq.word=".$this->word[0]->getId()." and freq.date>=".$this->time)->getResult();
    if (empty($freq))
    {
      $freq = $this->em->createQuery("select freq from Entities\Frequency freq where freq.region=".$this->region[0]->getId()." and freq.word=".$this->word[0]->getId())->getResult();
      if (!empty($freq[0]))
      {
        $freq[0]->setWord($this->word[0]);
        $freq[0]->setRegion($this->region[0]);
        $freq[0]->setDate(time());
        $freq[0]->setFrequency($this->api->getWordStat($this->word[0]->getName(), $this->region[0]->getCode()));
      }
      else
      {
        $freq[0] = new Frequency();
        $freq[0]->setWord($this->word[0]);
        $freq[0]->setRegion($this->region[0]);
        $freq[0]->setDate(time());
        $freq[0]->setFrequency($this->api->getWordStat($this->word[0]->getName(), $this->region[0]->getCode()));
      }
      $this->em->persist($freq[0]);

      $this->em->flush();
    }

    return $freq[0];
  }
}