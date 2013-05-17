<?php
/**
 * Created by JetBrains PhpStorm.
 * User: devel
 * Date: 5/2/13
 * Time: 7:04 PM
 * To change this template use File | Settings | File Templates.
 */

use Doctrine\ORM\EntityManager, Entities\Region;

require_once 'MegaIndexConfig.php';

class MegaIndexDb
{
  const DEFAULT_PREFIX = "tbl_";
  const DOMAIN = 'domain';
  const REGION = 'region';
  const YP = 'yp';
  const WORDS = 'words';
  const FREQUENCY = 'frequency';

  private $_config;
  private $_em;
  private $_conn;
  private $_sm;

  public function __construct()
  {
    $this->_config = new MegaIndexConfig();
    $this->_config->setDefaultSettings();
  }

  public function createNewDb($dbName, $driver, $host, $user, $password)
  {
    $this->_config->setDbOptions($driver, $host, $user, $password);
    $this->_em = EntityManager::create($this->_config->getDbOptions(), $this->_config->getConfig());
    $this->_conn = $this->_em->getConnection();
    $this->_sm = $this->_conn->getSchemaManager();
    $this->_sm->createDatabase($dbName);
    $this->_conn->close();

    $this->createConnection($dbName, $driver, $host, $user, $password);
  }

  public function createConnection($dbName, $driver, $host, $user, $password)
  {
    $this->_config->setDbName($dbName);
    $this->_config->setDbOptions($driver, $host, $user, $password);
    $this->_em = EntityManager::create($this->_config->getDbOptions(), $this->_config->getConfig());
    $this->_conn = $this->_em->getConnection();
  }

  public function createTables()
  {
    $toSchema = new Doctrine\DBAL\Schema\Schema();
    $primaryKey = "id";

    $domain = $toSchema->createTable(self::DEFAULT_PREFIX.self::DOMAIN);
    $domain->addColumn($primaryKey, "integer", array("length" => 11, "auto_increment" => true));
    $domain->addColumn("name", "string", array("length" => 255));
    $domain->setPrimaryKey(array("id"));
    $domain->addUniqueIndex(array("name"));
    $domain->getColumn($primaryKey)->setAutoincrement(true);

    $words = $toSchema->createTable(self::DEFAULT_PREFIX.self::WORDS);
    $words->addColumn($primaryKey, "integer", array("length" => 11, "auto_increment" => true));
    $words->addColumn("name", "string", array("length" => 255));
    $words->addColumn("price", "string", array("length" => 255));
    $words->addColumn("date", "integer");
    $words->addUniqueIndex(array("name"));
    $words->setPrimaryKey(array("id"));
    $words->getColumn($primaryKey)->setAutoincrement(true);
    //$myTable->addUniqueIndex(array("name"));

    $regions = $toSchema->createTable(self::DEFAULT_PREFIX.self::REGION);
    $regions->addColumn($primaryKey, "integer", array("length" => 11, "auto_increment" => true));
    $regions->addColumn("name", "string", array("length" => 255));
    $regions->addColumn("code", "string", array("length" => 10));
    $regions->setPrimaryKey(array("id"));
    $regions->getColumn($primaryKey)->setAutoincrement(true);

    $yp = $toSchema->createTable(self::DEFAULT_PREFIX.self::YP);
    $yp->addColumn($primaryKey, "integer", array("length" => 11, "auto_increment" => true));
    $yp->addColumn("domain_id", "integer", array("length" => 11));
    $yp->addColumn("word_id", "integer", array("length" => 11));
    $yp->addColumn("region_id", "integer", array("length" => 11));
    $yp->addColumn("position", "string", array("length" => 10));
    $yp->addColumn("date", "integer");
    $yp->setPrimaryKey(array("id"));
    $yp->getColumn($primaryKey)->setAutoincrement(true);
    $yp->addIndex(array("word_id"));
    $yp->addIndex(array("domain_id"));
    $yp->addIndex(array("region_id"));
    $yp->addForeignKeyConstraint($domain, array("domain_id"), array("id"), array("onUpdate" => "CASCADE", "onDelete" => "CASCADE"));
    $yp->addForeignKeyConstraint($words, array("word_id"), array("id"), array("onUpdate" => "CASCADE", "onDelete" => "CASCADE"));
    $yp->addForeignKeyConstraint($regions, array("region_id"), array("id"), array("onUpdate" => "CASCADE", "onDelete" => "CASCADE"));

    $freq = $toSchema->createTable(self::DEFAULT_PREFIX.self::FREQUENCY);
    $freq->addColumn($primaryKey, "integer", array("length" => 11, "auto_increment" => true));
    $freq->addColumn("word_id", "integer", array("length" => 11));
    $freq->addColumn("region_id", "integer", array("length" => 11));
    $freq->addColumn("frequency", "string", array("length" => 50));
    $freq->addColumn("date", "integer");
    $freq->setPrimaryKey(array("id"));
    $freq->getColumn($primaryKey)->setAutoincrement(true);
    $freq->addIndex(array("region_id"));
    $freq->addIndex(array("word_id"));
    $freq->addForeignKeyConstraint($words, array("word_id"), array("id"), array("onUpdate" => "CASCADE", "onDelete" => "CASCADE"));
    $freq->addForeignKeyConstraint($regions, array("region_id"), array("id"), array("onUpdate" => "CASCADE", "onDelete" => "CASCADE"));

    $createSql = $toSchema->toSql($this->_conn->getDatabasePlatform());
    $this->transaction($createSql);
  }

  public function destroyTables()
  {
    $toSchema = new Doctrine\DBAL\Schema\Schema();
    $dropSql = $toSchema->toDropSql($this->_conn->getDatabasePlatform());
    $this->transaction($dropSql);
  }

  public function setRegions()
  {
    $regions = array("Москва");
    $code = array("213");
    for ($i=0; $i<1; $i++)
    {
      $recRegions[$i] = new Region();

      $recRegions[$i]->setName($regions[$i]);
      $recRegions[$i]->setCode($code[$i]);
      $this->_em->persist($recRegions[$i]);

      $this->_em->flush();
    }
  }

  public function destroyLiteTables()
  {
    $queries = array(
      "DROP TABLE ".self::DEFAULT_PREFIX.self::YP,
      "DROP TABLE ".self::DEFAULT_PREFIX.self::FREQUENCY,
      "DROP TABLE ".self::DEFAULT_PREFIX.self::REGION,
      "DROP TABLE ".self::DEFAULT_PREFIX.self::WORDS,
      "DROP TABLE ".self::DEFAULT_PREFIX.self::DOMAIN,
    );
    $this->transaction($queries);
  }

  public function destroyDb($dbName)
  {
      $this->_sm->dropDatabase($dbName);
  }

  public function getConnection()
  {
    return $this->_conn;
  }

  public function getEntityManager()
  {
    return $this->_em;
  }

  public function getSchemaManager()
  {
    return $this->_sm;
  }

  private function transaction($queries)
  {
    $this->_conn->beginTransaction();
    try
    {
      foreach ($queries as $query)
        $this->_conn->executeQuery($query);
      $this->_conn->commit();
    }
    catch(Exception $e)
    {
      $this->_conn->rollback();
      throw $e;
    }
  }
}
