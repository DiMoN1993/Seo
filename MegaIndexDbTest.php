<?php
/**
 * Created by JetBrains PhpStorm.
 * User: devel
 * Date: 5/1/13
 * Time: 3:22 PM
 * To change this template use File | Settings | File Templates.
 */
use Entities\Domain,
  Entities\Words,
  Entities\Region,
  Entities\YP,
  Entities\Frequency;

require_once ("MegaIndexDb.php");

class MegaIndexDbTest extends PHPUnit_Framework_TestCase
{
  private $db;
  private $em;
  private $conn;

  private $words;
  private $date;
  private $prices;
  private $domains;
  private $position;
  private $region;
  private $code;
  private $frequency;

  public function setUp()
  {
    $this->db = new MegaIndexDb();

    $this->words = array ('маркер', 'карандашь', 'ластик', 'надувная', 'тетрадь', 'точилка', 'штрих', 'линейка', 'транспортир');
    $this->date = array ("now", "-1 day", "-14 days 2 hours 32 minutes", "-10 days 5 hours 32 minutes",
      "-1 days 2 hours 32 minutes", "+1 days 2 hours 32 minutes", "+2 days 5 hours 32 minutes", "-7 hours", "+4 days 6 hours 1 minute");
    $this->prices = array ('12500', '6700', '14200', '3400', '8800', '10000', '24000', '16340', '20101');
    $this->domains = array ('ru.wikipedia.org', 'lenta.ru', 'vk.com', 'tut.ru', 'youtube.com', 'twitter.com', 'mail.ru', 'whitehouse.com', 'wowcircle.com');
    $this->position = array ('2', '15', '22', '19', '44', '55', '78', '90', '13');
    $this->region = array ('Москва', 'Омск', 'Новосибирск', 'Удмуртия', 'Саратов', 'Сочи', 'Санкт Петербург', 'Гондурас', 'Хохляндия');
    $this->code = array ('1', '55', '59', '13', '77', '44', '23', '98', '65');
    $this->frequency = array ('10000', '6700', '16340', '14200', '12500', '8800', '6700', '3400', '24000');

    $this->db->createNewDb('megaindex3', 'pdo_mysql', 'localhost', 'root', 'root');
    $this->em = $this->db->getEntityManager();
    $this->conn = $this->db->getConnection();

    $this->db->createTables();

    for ($i=0; $i<9; $i++)
    {
      $recWords[$i] = new Words();
      $recDomain[$i] = new Domain();
      $recRegions[$i] = new Region();
      $recYP[$i] = new YP();
      $recFreq[$i] = new Frequency();
      
      //load test words
      $recWords[$i]->setName($this->words[$i]);
      $recWords[$i]->setDate(strtotime($this->date[$i]));
      $recWords[$i]->setPrice($this->prices[$i]);
      $this->em->persist($recWords[$i]);

      //load test domain
      $recDomain[$i]->setName($this->domains[$i]);
      $this->em->persist($recDomain[$i]);

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

      //load test Frequency
      $recFreq[$i]->setWord($recWords[$i]);
      $recFreq[$i]->setDate(strtotime($this->date[$i]));
      $recFreq[$i]->setFrequency($this->frequency[$i]);
      $recFreq[$i]->setRegion($recRegions[$i]);
      $this->em->persist($recFreq[$i]);

      $this->em->flush();
    }
  }

  public function testWordRead()
  {
    $word = $this->em->getRepository('Entities\Words')->findBy(array('name' => $this->words[1]));
    $this->assertNotEmpty($word);

    $word = $this->em->getRepository('Entities\Words')->find(6);
    $this->assertNotEmpty($word);

    $word = $this->em->getRepository('Entities\Words')->findBy(array('name' => array($this->words[1], $this->words[2])));
    $this->assertNotEmpty($word);
  }

  public function testWordUpdate()
  {
    $newWord = 'чебурашка';
    $newWord2 = 'большой адронный коллайдер';

    $word = $this->em->getRepository('Entities\Words')->find(4);
    $this->assertNotEmpty($word);

    $word->setName($newWord);
    $this->em->persist($word);

    $this->em->flush();

    $word = $this->em->getRepository('Entities\Words')->find(4);
    $this->assertEquals($word->getName(), $newWord);

    $word = $this->em->getRepository('Entities\Words')->findBy(array('name' => array($this->words[1], $this->words[2])));

    $this->assertNotEmpty($word);
    $this->assertInternalType("array", $word);

    $word[0]->setName($newWord2);
    foreach ($word as $row)
      $this->em->persist($row);

    $this->em->flush();

    $word = $this->em->getRepository('Entities\Words')->findBy(array('name' => array($newWord2, $this->words[2])));
    $this->assertEquals($word[0]->getName(), $newWord2);
  }

  public function testWordDelete()
  {
    $word = $this->em->getRepository('Entities\Words')->findBy(array('name' => array($this->words[1], $this->words[2])));
    $this->assertNotEmpty($word);

    foreach ($word as $row)
      $this->em->remove($row);

    $this->em->flush();

    $word = $this->em->getRepository('Entities\Words')->findBy(array('name' => array($this->words[1], $this->words[2])));
    $this->assertEmpty($word);
  }

  public function testDomainRead()
  {
    $domain = $this->em->getRepository('Entities\Domain')->find(4);
    $this->assertNotEmpty($domain);
    $this->assertInternalType('object', $domain);

    $domain = $this->em->getRepository('Entities\Domain')->findBy(array('name' => $this->domains[1]));
    $this->assertNotEmpty($domain);
    $this->assertInternalType('array', $domain);

    $domain = $this->em->getRepository('Entities\Domain')->findBy(array('name' => array($this->domains[1], $this->domains[2])));
    $this->assertNotEmpty($domain);
  }

  public function testDomainUpdate()
  {
    $newDomain = 'newdomen.com';
    $newDomain2 = 'newdomen2.com';

    $domain = $this->em->getRepository('Entities\Domain')->find(4);
    $this->assertNotEmpty($domain);

    $domain->setName($newDomain);
    $this->em->persist($domain);

    $this->em->flush();

    $domain = $this->em->getRepository('Entities\Domain')->find(4);

    $this->assertEquals($domain->getName(), $newDomain);

    $domain = $this->em->getRepository('Entities\Domain')->findBy(array('name' => array($this->domains[1], $this->domains[2])));

    $this->assertNotEmpty($domain);
    $this->assertInternalType("array", $domain);

    $domain[1]->setName($newDomain2);
    foreach ($domain as $row)
      $this->em->persist($row);

    $this->em->flush();

    $domain = $this->em->getRepository('Entities\Domain')->findBy(array('name' => array($this->domains[1], $newDomain2)));
    $this->assertEquals($domain[1]->getName(), $newDomain2);
  }

  public function testDomainDelete()
  {
    $domain = $this->em->getRepository('Entities\Domain')->findBy(array('name' => array($this->domains[1], $this->domains[2])));
    $this->assertNotEmpty($domain);

    foreach ($domain as $row)
      $this->em->remove($row);

    $this->em->flush();

    $domain = $this->em->getRepository('Entities\Domain')->findBy(array('name' => $this->domains[2]));
    $this->assertEmpty($domain);
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

    $yp = $this->em->getRepository('Entities\YP')->findBy(array('domain' => $domain[0]->getId(), 'word' => $word->getId()));
    $this->assertNotEmpty($yp);
  }

  public function testYpUpdate()
  {
    $newPosition = '33';
    $newDomain = $this->em->getRepository('Entities\Domain')->find(2);
    $newWord = $this->em->getRepository('Entities\Words')->find(5);

    $domain = $this->em->getRepository('Entities\Domain')->findBy(array ('name' => $this->domains[0]));
    $this->assertInternalType('array', $domain);
    $word = $this->em->getRepository('Entities\Words')->find(1);
    $this->assertInternalType('object', $word);

    $yp = $this->em->getRepository('Entities\YP')->findBy(array('domain' => $domain[0]->getId(), 'word' => $word->getId()));
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