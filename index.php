<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
</head>
<body>
        <?php
            use Entities\Domain,
              Entities\Words,
              Entities\Region,
              Entities\YP,
              Entities\Frequency;

            function __autoload($className)
            {
              include_once($className.".php");
            }

            $action = 'getstat';
            switch ($_GET['action'])
            {
              case $action:
                  getStat();
                break;
              default:
                include_once ('main.php');
            }

            function getStat()
            {
              try
              {
                if (!empty($_POST['words']) && !empty ($_POST['url']) && !empty ($_POST['region']))
                {
                  $time = 3600*24*5;
                  $time = time()-$time;
                  $db = new MegaIndexDb();
                  $db->createConnection('megaindex', 'pdo_mysql', 'localhost', 'root', 'root');
                  $em = $db->getEntityManager();
                  //$conn = $db->getConnection();

                  $region = (int)$_POST['region'];
                  $url = trim($_POST['url']);
                  $requests = explode(',', $_POST['words']);

                  $api = new MegaIndex($url);

                  foreach ($requests as $request)
                  {
                    $request = trim($request);
                    $word = $em->createQuery("select words from Entities\Words words where words.name=:name and words.date>=".$time);
                    $word = $word->setParameter('name', $request)->getResult();
                    if (empty($word[0]->getPrice()))
                    {
                      $word[0] = new Words();
                      $word[0]->setName($request);
                      $word[0]->setPrice($api->getPrice($request));
                      $word[0]->setDate(time());
                      $em->persist($word[0]);

                      $em->flush();
                    }

                    $domain = $em->getRepository('Entities\Domain')->findBy(array('name' => $url));
                    if (empty($domain[0]->getName()))
                    {
                      $domain[0] = new Domain();
                      $domain[0]->setName($url);
                      $em->persist($domain[0]);

                      $em->flush();
                    }

                    $region = $em->getRepository('Entities\Region')->findBy(array('code' => $region));

                    $yp = $em->createQuery("select yp from Entities\YP yp where yp.domain=".$domain[0]->getId()." and yp.region=".$region[0]->getId()." and yp.word=".$word[0]->getId()." and yp.date>=".$time)->getResult();
                    if (empty($yp[0]->getPosition()))
                    {
                      $yp[0] = new YP();
                      $yp[0]->setDate(time());
                      $yp[0]->setRegion($region[0]);
                      $yp[0]->setWord($word[0]);
                      $yp[0]->setDomain($domain[0]);
                      $yp[0]->setPosition($api->getYandexPosition($word[0]->getName(), $region[0]->getCode()));

                      $em->persist($yp[0]);

                      $em->flush();
                    }

                    $freq = $em->createQuery("select freq from Entities\YP freq where freq.region=".$region[0]->getId()." and freq.word=".$word[0]->getId()." and freq.date>=".$time)->getResult();
                    if (empty($freq[0]->getFrequency()))
                    {
                      $freq[0] = new Frequency();
                      $freq[0]->setWord($word[0]);
                      $freq[0]->setRegion($region[0]);
                      $freq[0]->setDate(time());
                      $freq[0]->setFrequency($api->getWordStat($word[0]->getName(), $region[0]->getCode()));

                      $em->persist($freq[0]);

                      $em->flush();
                    }

                    $domain[] = $domain[0];
                    $word[] = $word[0];
                    $region[] = $region[0];
                    $yp[] = $yp[0];
                    $freq[] = $freq[0];
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
            }
        ?>
</body>
</html>
