<?php
/**
 * Created by JetBrains PhpStorm.
 * User: devel
 * Date: 5/17/13
 * Time: 2:18 PM
 * To change this template use File | Settings | File Templates.
 */
use Entities\Domain;

require_once("MegaIndexDb.php");
require_once("MegaIndexConfig.php");

class MiDomainTest extends PHPUnit_Framework_TestCase
{
  private $db;
  private $em;
  private $conn;
  private $dbName;

  private $domains;

  public function setUp()
  {
    $this->db = new MegaIndexDb();
    $config = new MegaIndexConfig();

    $this->domains = array ('ru.wikipedia.org', 'lenta.ru', 'vk.com', 'tut.ru', 'youtube.com', 'twitter.com', 'mail.ru', 'whitehouse.com', 'wowcircle.com');
    $this->dbName = $config->testDbName;

    $this->db->createNewDb($config->testDbName, $config->dbDriver, $config->dbHost, $config->dbLogin, $config->dbPassword);
    $this->em = $this->db->getEntityManager();
    $this->conn = $this->db->getConnection();

    $this->db->createTables();

    for ($i=0; $i<9; $i++)
    {
      $recDomain[$i] = new Domain();

      //load test domain
      $recDomain[$i]->setName($this->domains[$i]);
      $this->em->persist($recDomain[$i]);

      $this->em->flush();
    }
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

  public function tearDown()
  {
    $this->db->destroyTables();
    $this->db->destroyDb($this->dbName);
    $this->conn->close();
  }
}
