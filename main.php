<?php
use Entities\Domain,
  Entities\Words,
  Entities\Region,
  Entities\YP,
  Entities\Frequency;

require_once("MegaIndexApi.php");
require_once("MegaIndexDb.php");
require_once("MegaIndexConfig.php");

try
{
  if (!empty($_POST['words']) && !empty ($_POST['url']) && !empty ($_POST['region']))
  {
    $time = 3600*24*5;
    $time = time()-$time;
    $db = new MegaIndexDb();
    $config = new MegaIndexConfig();
    //$db->createConnection('megaindex', 'pdo_mysql', 'localhost', 'root', 'root');
    $db->createConnection($config->dbName, $config->dbDriver, $config->dbHost, $config->dbLogin, $config->dbPassword);
    $em = $db->getEntityManager();

    $regionCode = (int)$_POST['region'];
    $url = trim($_POST['url']);
    $requests = explode(',', $_POST['words']);

    //$api = new MegaIndex($url, 'sagdiv@gmail.com', 'VqGPOv');
    $api = new MegaIndexApi($url, $config->apiEmail, $config->apiPassword);
    $domain = $em->getRepository('Entities\Domain')->findBy(array('name' => $url));
    if (empty($domain))
    {
      $domain[0] = new Domain();
      $domain[0]->setName($url);
      $em->persist($domain[0]);

      $em->flush();
    }

    $region = $em->getRepository('Entities\Region')->findBy(array('code' => $regionCode));
    if (empty($region))
      throw new Exception('Table region is empty');
    foreach ($requests as $request)
    {
      $request = trim($request);

      $word = $em->createQuery("select words from Entities\Words words where words.name=:name and words.date>=".$time);
      $word = $word->setParameter('name', $request)->getResult();
      if (empty($word))
      {
        $word = $em->createQuery("select words from Entities\Words words where words.name=:name");
        $word = $word->setParameter('name', $request)->getResult();
        if (!empty($word[0]))
        {
          $word[0]->setName($request);
          $word[0]->setPrice($api->getPrice($request));
          $word[0]->setDate(time());
        }
        else
        {
          $word[0] = new Words();
          $word[0]->setName($request);
          $word[0]->setPrice($api->getPrice($request));
          $word[0]->setDate(time());
        }
        $em->persist($word[0]);

        $em->flush();
      }

      $yp = $em->createQuery("select yp from Entities\YP yp where yp.domain=".$domain[0]->getId()." and yp.region=".$region[0]->getId()." and yp.word=".$word[0]->getId()." and yp.date>=".$time)->getResult();
      if (empty($yp))
      {
        $yp = $em->createQuery("select yp from Entities\YP yp where yp.domain=".$domain[0]->getId()." and yp.region=".$region[0]->getId()." and yp.word=".$word[0]->getId())->getResult();
        if (!empty($yp[0]))
        {
          $yp[0]->setDate(time());
          $yp[0]->setRegion($region[0]);
          $yp[0]->setWord($word[0]);
          $yp[0]->setDomain($domain[0]);
          $yp[0]->setPosition($api->getYandexPosition($word[0]->getName(), $region[0]->getCode()));
        }
        else
        {
          $yp[0] = new YP();
          $yp[0]->setDate(time());
          $yp[0]->setRegion($region[0]);
          $yp[0]->setWord($word[0]);
          $yp[0]->setDomain($domain[0]);
          $yp[0]->setPosition($api->getYandexPosition($word[0]->getName(), $region[0]->getCode()));
        }
        $em->persist($yp[0]);

        $em->flush();
      }

      $freq = $em->createQuery("select freq from Entities\Frequency freq where freq.region=".$region[0]->getId()." and freq.word=".$word[0]->getId()." and freq.date>=".$time)->getResult();
      if (empty($freq))
      {
        $freq = $em->createQuery("select freq from Entities\Frequency freq where freq.region=".$region[0]->getId()." and freq.word=".$word[0]->getId())->getResult();
        if (!empty($freq[0]))
        {
          $freq[0]->setWord($word[0]);
          $freq[0]->setRegion($region[0]);
          $freq[0]->setDate(time());
          $freq[0]->setFrequency($api->getWordStat($word[0]->getName(), $region[0]->getCode()));
        }
        else
        {
          $freq[0] = new Frequency();
          $freq[0]->setWord($word[0]);
          $freq[0]->setRegion($region[0]);
          $freq[0]->setDate(time());
          $freq[0]->setFrequency($api->getWordStat($word[0]->getName(), $region[0]->getCode()));
        }
        $em->persist($freq[0]);

        $em->flush();
      }
      //this arrays contain answers on request
      $domains[] = $domain[0];
      $words[] = $word[0];
      $regions[] = $region[0];
      $yps[] = $yp[0];
      $freqs[] = $freq[0];
    }
  }
  else
  {
    //include_once("error.php");
  }
}
catch (Exception $e)
{
  echo $e->getMessage();
}