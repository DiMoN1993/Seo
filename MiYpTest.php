<?php
/**
 * Created by JetBrains PhpStorm.
 * User: devel
 * Date: 5/17/13
 * Time: 2:35 PM
 * To change this template use File | Settings | File Templates.
 */
use Entities\Domain,
   Entities\Words,
   Entities\Region,
   Entities\YP;

require_once("MegaIndexDb.php");

class MiYpTest extends PHPUnit_Framework_TestCase
{
  private $db;
  private $em;
  private $conn;

  private $words;
  private $date;
  private $position;
  private $prices;
  private $region;
  private $code;
  private $domains;

  public function setUp()
  {
    $this->db = new MegaIndexDb();

    $this->words = array ('маркер', 'карандашь', 'ластик', 'надувная', 'тетрадь', 'точилка', 'штрих', 'линейка', 'транспортир');
    $this->date = array ("now", "-1 day", "-14 days 2 hours 32 minutes", "-10 days 5 hours 32 minutes",
      "-1 days 2 hours 32 minutes", "+1 days 2 hours 32 minutes", "+2 days 5 hours 32 minutes", "-7 hours", "+4 days 6 hours 1 minute");
    $this->position = array ('2', '15', '22', '19', '44', '55', '78', '90', '13');
    $this->region = array ('Москва', 'Омск', 'Новосибирск', 'Удмуртия', 'Саратов', 'Сочи', 'Санкт Петербург', 'Гондурас', 'Хохляндия');
    $this->code = array ('1', '55', '59', '13', '77', '44', '23', '98', '65');
    $this->domains = array ('ru.wikipedia.org', 'lenta.ru', 'vk.com', 'tut.ru', 'youtube.com', 'twitter.com', 'mail.ru', 'whitehouse.com', 'wowcircle.com');
    $this->prices = array ('12500', '6700', '14200', '3400', '8800', '10000', '24000', '16340', '20101');

    $this->db->createNewDb('megaindex3', 'pdo_mysql', 'localhost', 'root', 'root');
    $this->em = $this->db->getEntityManager();
    $this->conn = $this->db->getConnection();

    $this->db->createTables();

    for ($i=0; $i<9; $i++)
    {
      $recDomain[$i] = new Domain();
      $recWords[$i] = new Words();
      $recRegions[$i] = new Region();
      $recYP[$i] = new YP();

      //load test domain
      $recDomain[$i]->setName($this->domains[$i]);
      $this->em->persist($recDomain[$i]);

      //load test words
      $recWords[$i]->setName($this->words[$i]);
      $recWords[$i]->setDate(strtotime($this->date[$i]));
      $recWords[$i]->setPrice($this->prices[$i]);
      $this->em->persist($recWords[$i]);

      //load test regions
      $recRegions[$i]->setName($this->region[$i]);
      $recRegions[$i]->setCode($this->code[$i]);
      $this->em->persist($recRegions[$i]);

      //load test Yandex Position
      $recYP[$i]->setDate(strtotime($this->date[$i]));
      $recYP[$i]->setDomain($recDomain[$i]);
      $recYP[$i]->setPosition($this->position[$i]);
      $recYP[$i]->setRegion($recRegions[$i]);
      $recYP[$i]->setWord($recWords[$i]);
      $this->em->persist($recYP[$i]);

      $this->em->flush();
    }
  }

  public function testYpRead()
  {
    $yp = $this->em->getRepository('Entities\YP')->find(4);
    $this->assertNotEmpty($yp);
    $this->assertInternalType('object', $yp->getDomain());

    //It's very important request for us, but he doesn't work. We need something equal.
    //$yp = $this->em->getRepository('Entities\YP')->findBy(array('domain.name' => $this->domains[0], 'word.name' => $this->words[0]));
    //$this->assertNotEmpty($yp);

    $yp = $this->em->getRepository('Entities\YP')->findBy(array('position' => $this->position[1]));
    $this->assertNotEmpty($yp);

    $yp = $this->em->getRepository('Entities\YP')->findBy(array('position' => array($this->position[1], $this->position[2])));
    $this->assertNotEmpty($yp);

    $domain = $this->em->getRepository('Entities\Domain')->findBy(array ('name' => $this->domains[0]));
    $this->assertInternalType('array', $domain);
    $word = $this->em->getRepository('Entities\Words')->find(1);
    $this->assertInternalType('object', $word);

    $yp = $this->em->createQuery("select yp from Entities\YP yp where yp.domain=".$domain[0]->getId()." and yp.word=".$word->getId())->getResult();
    $this->assertNotEmpty($yp);

    $yp = $this->em->getRepository('Entities\YP')->findBy(array('domain' => $domain[0]->getId(), 'word' => $word->getId()));
    $this->assertNotEmpty($yp);
  }

  public function testYpUpdate()
  {
    $newPosition = '33';
    $newDomain = $this->em->getRepository('Entities\Domain')->find(2);
    $newWord = $this->em->getRepository('Entities\Words')->find(5);

    $domain = $this->em->getRepository('Entities\Domain')->find(1);
    $this->assertInternalType('object', $domain);
    $word = $this->em->getRepository('Entities\Words')->find(1);
    $this->assertInternalType('object', $word);

    $yp = $this->em->getRepository('Entities\YP')->findBy(array('domain' => $domain->getId(), 'word' => $word->getId()));
    $this->assertNotEmpty($yp);

    $yp[0]->setPosition($newPosition);
    $yp[0]->setDomain($newDomain);
    $yp[0]->setWord($newWord);
    foreach ($yp as $row)
      $this->em->persist($row);

    $this->em->flush();

    $yp = $this->em->getRepository('Entities\YP')->findBy(array('domain' => $newDomain->getId(), 'word' => $newWord->getId()));
    $this->assertEquals($yp[0]->getDomain(), $newDomain);
    $this->assertEquals($yp[0]->getWord(), $newWord);
    $this->assertEquals($yp[0]->getPosition(), $newPosition);
  }

  public function testYpDelete()
  {
    $domain = $this->em->getRepository('Entities\Domain')->findBy(array ('name' => $this->domains[0]));
    $this->assertInternalType('array', $domain);
    $word = $this->em->getRepository('Entities\Words')->find(1);
    $this->assertInternalType('object', $word);

    $yp = $this->em->getRepository('Entities\YP')->findBy(array('domain' => $domain[0]->getId(), 'word' => $word->getId()));
    $this->assertNotEmpty($yp);

    foreach ($yp as $row)
      $this->em->remove($row);

    $this->em->flush();

    $yp = $this->em->getRepository('Entities\YP')->findBy(array('domain' => $domain[0]->getId(), 'word' => $word->getId()));
    $this->assertEmpty($yp);
  }

  public function tearDown()
  {
    $this->db->destroyTables();
    $this->db->destroyDb('megaindex3');
    $this->conn->close();
  }
}
