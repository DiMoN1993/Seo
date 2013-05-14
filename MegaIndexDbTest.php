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


  public function __construct()
  {
    $this->db = new MegaIndexDb();
    $this->db->createNewDb('megaindex2', 'pdo_mysql', 'localhost', 'root', 'root');
    $this->em = $this->db->getEntityManager();
    $this->conn = $this->db->getConnection();

    $this->words = array ('маркер', 'карандашь', 'ластик', 'надувная', 'тетрадь', 'точилка', 'штрих', 'линейка', 'транспортир');
    $this->date = array ("now", "-1 day", "-14 days 2 hours 32 minutes", "-10 days 5 hours 32 minutes",
      "-1 days 2 hours 32 minutes", "+1 days 2 hours 32 minutes", "+2 days 5 hours 32 minutes", "-7 hours", "+4 days 6 hours 1 minute");
    $this->prices = array ('12500', '6700', '14200', '3400', '8800', '10000', '24000', '16340', '20101');
    $this->domains = array ('ru.wikipedia.org', 'lenta.ru', 'vk.com', 'tut.ru', 'youtube.com', 'twitter.com', 'mail.ru', 'whitehouse.com', 'wowcircle.com');
    $this->position = array ('2', '15', '22', '19', '44', '55', '78', '90', '13');
    $this->region = array ('Москва', 'Омск', 'Новосибирск', 'Удмуртия', 'Саратов', 'Сочи', 'Санкт Петербург', 'Гондурас', 'Хохляндия');
    $this->code = array ('1', '55', '59', '13', '77', '44', '23', '98', '65');
    $this->frequency = array ('10000', '6700', '16340', '14200', '12500', '8800', '6700', '3400', '24000');
  }

  public function setUp()
  {
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

  public function testRowExist()
  {
    $word = $this->em->getRepository('Entities\Words')->findBy(array('name' => 'маркер'));
    $this->assertNotEmpty($word);
  }

  public function tearDown()
  {
    $this->db->destroyTables();
  }

  public function __destruct()
  {
    $this->db->destroyDb('megaindex2');
  }
}