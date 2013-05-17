  <?php
/**
 * Created by JetBrains PhpStorm.
 * User: devel
 * Date: 5/1/13
 * Time: 3:22 PM
 * To change this template use File | Settings | File Templates.
 */
use Entities\Words;

require_once ("MegaIndexDb.php");

class MegaIndexDbTest extends PHPUnit_Framework_TestCase
{
  private $db;
  private $em;
  private $conn;

  private $words;
  private $date;
  private $prices;
  //private $region;
  //private $code;
  //private $frequency;

  public function setUp()
  {
    $this->db = new MegaIndexDb();

    $this->words = array ('маркер', 'карандашь', 'ластик', 'надувная', 'тетрадь', 'точилка', 'штрих', 'линейка', 'транспортир');
    $this->date = array ("now", "-1 day", "-14 days 2 hours 32 minutes", "-10 days 5 hours 32 minutes",
      "-1 days 2 hours 32 minutes", "+1 days 2 hours 32 minutes", "+2 days 5 hours 32 minutes", "-7 hours", "+4 days 6 hours 1 minute");
    $this->prices = array ('12500', '6700', '14200', '3400', '8800', '10000', '24000', '16340', '20101');
    //$this->region = array ('Москва', 'Омск', 'Новосибирск', 'Удмуртия', 'Саратов', 'Сочи', 'Санкт Петербург', 'Гондурас', 'Хохляндия');
    //$this->code = array ('1', '55', '59', '13', '77', '44', '23', '98', '65');
    //$this->frequency = array ('10000', '6700', '16340', '14200', '12500', '8800', '6700', '3400', '24000');

    $this->db->createNewDb('megaindex3', 'pdo_mysql', 'localhost', 'root', 'root');
    $this->em = $this->db->getEntityManager();
    $this->conn = $this->db->getConnection();

    $this->db->createTables();

    for ($i=0; $i<9; $i++)
    {
      $recWords[$i] = new Words();
      //$recRegions[$i] = new Region();
      //$recFreq[$i] = new Frequency();
      
      //load test words
      $recWords[$i]->setName($this->words[$i]);
      $recWords[$i]->setDate(strtotime($this->date[$i]));
      $recWords[$i]->setPrice($this->prices[$i]);
      $this->em->persist($recWords[$i]);
/*
      //load test regions
      $recRegions[$i]->setName($this->region[$i]);
      $recRegions[$i]->setCode($this->code[$i]);
      $this->em->persist($recRegions[$i]);

      //load test Frequency
      $recFreq[$i]->setWord($recWords[$i]);
      $recFreq[$i]->setDate(strtotime($this->date[$i]));
      $recFreq[$i]->setFrequency($this->frequency[$i]);
      $recFreq[$i]->setRegion($recRegions[$i]);
      $this->em->persist($recFreq[$i]);
*/
      $this->em->flush();
    }
  }

  public function testWordRead()
  {
    $word = $this->em->getRepository('Entities\Words')->findBy(array('name' => $this->words[1]));
    $this->assertNotEmpty($word);

    $word = $this->em->createQuery("select words from Entities\Words words where words.date>=".strtotime($this->date[1])." and words.date>0")->getResult();
    $this->assertNotEmpty($word);

    $word = $this->em->createQuery("select words from Entities\Words words where words.name=:name and words.date>=".strtotime($this->date[1]))->setParameter('name', $this->words[1])->getResult();
    $this->assertInternalType('array', $word);
    $this->assertInternalType('object', $word[0]);
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

  public function tearDown()
  {
    $this->db->destroyTables();
    $this->db->destroyDb('megaindex3');
    $this->conn->close();
  }
}