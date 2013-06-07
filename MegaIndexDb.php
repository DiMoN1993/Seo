<?php
/**
 * Created by JetBrains PhpStorm.
 * User: devel
 * Date: 5/2/13
 * Time: 7:04 PM
 * To change this template use File | Settings | File Templates.
 */

use Doctrine\ORM\EntityManager, Entities\Region;
require_once 'MegaIndexDbConfig.php';

class MegaIndexDb extends MegaIndexDbConfig
{
  const DEFAULT_PREFIX = "tbl_";
  const DOMAIN = 'domain';
  const REGION = 'region';
  const YP = 'yp';
  const WORDS = 'words';
  const FREQUENCY = 'frequency';
  const TASK_LIST = 'taskList';
  const TASK = 'task';

  private static $_em;
  private $_conn;
  private $_sm;

  public function __construct()
  {
    $this->setDefaultSettings();
  }

  public function createNewDb($dbName, $driver, $host, $user, $password)
  {
    $this->setDbOptions($driver, $host, $user, $password);
    self::$_em = EntityManager::create($this->getDbOptions(), $this->getConfig());
    $this->_conn = self::$_em->getConnection();
    $this->_sm = $this->_conn->getSchemaManager();
    $this->_sm->createDatabase($dbName);
    $this->_conn->close();

    $this->createConnection($dbName, $driver, $host, $user, $password);
  }

  public function createConnection($dbName, $driver, $host, $user, $password)
  {
    $this->setDbName($dbName);
    $this->setDbOptions($driver, $host, $user, $password);
    self::$_em  = EntityManager::create($this->getDbOptions(), $this->getConfig());
    $this->_conn = self::$_em->getConnection();
  }

  public function createTables()
  {
    $toSchema = new Doctrine\DBAL\Schema\Schema();
    $primaryKey = "id";

    $domain = $toSchema->createTable(self::DEFAULT_PREFIX.self::DOMAIN);
    $domain->addColumn($primaryKey, "integer", array("length" => 11, "auto_increment" => true));
    $domain->addColumn("name", "string", array("length" => 255));
    $domain->setPrimaryKey(array($primaryKey));
    $domain->addUniqueIndex(array("name"));
    $domain->getColumn($primaryKey)->setAutoincrement(true);

    $words = $toSchema->createTable(self::DEFAULT_PREFIX.self::WORDS);
    $words->addColumn($primaryKey, "integer", array("length" => 11, "auto_increment" => true));
    $words->addColumn("name", "string", array("length" => 255));
    $words->addColumn("price", "string", array("length" => 255));
    $words->addColumn("date", "integer");
    $words->addUniqueIndex(array("name"));
    $words->setPrimaryKey(array($primaryKey));
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
    $yp->setPrimaryKey(array($primaryKey));
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
    $freq->setPrimaryKey(array($primaryKey));
    $freq->getColumn($primaryKey)->setAutoincrement(true);
    $freq->addIndex(array("region_id"));
    $freq->addIndex(array("word_id"));
    $freq->addForeignKeyConstraint($words, array("word_id"), array("id"), array("onUpdate" => "CASCADE", "onDelete" => "CASCADE"));
    $freq->addForeignKeyConstraint($regions, array("region_id"), array("id"), array("onUpdate" => "CASCADE", "onDelete" => "CASCADE"));

    $taskList = $toSchema->createTable(self::DEFAULT_PREFIX.self::TASK_LIST);
    $taskList->addColumn($primaryKey,  "integer", array("length" => 11, "auto_increment" => true));
    $taskList->addColumn("status", "string", array("length" => 10));
    $taskList->setPrimaryKey(array($primaryKey));
    $taskList->getColumn($primaryKey)->setAutoincrement(true);

    $task = $toSchema->createTable(self::DEFAULT_PREFIX.self::TASK);
    $task->addColumn($primaryKey,  "integer", array("length" => 11, "auto_increment" => true));
    $task->addColumn("className", "string", array("length" => 100));
    $task->addColumn("body","blob");
    $task->addColumn("list_id", "integer", array("length" => 11));
    $task->setPrimaryKey(array($primaryKey));
    $task->getColumn($primaryKey)->setAutoincrement(true);
    $task->addIndex(array("list_id"));
    $task->addForeignKeyConstraint($taskList, array("list_id"), array("id"), array("onUpdate" => "CASCADE", "onDelete" => "CASCADE"));


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
      self::$_em->persist($recRegions[$i]);

      self::$_em->flush();
    }
  }

  public function destroyDb($dbName)
  {
    $this->_sm->dropDatabase($dbName);
  }

  public function getConnection()
  {
    return $this->_conn;
  }

  public static function getEntityManager()
  {
    if (empty(self::$_em))
    {
      throw new Exception('Void Entity Manager. Need to establish connection with data base.');
    }
    else
    {
      return self::$_em;
    }
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
