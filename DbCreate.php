<?php
/**
 * Created by JetBrains PhpStorm.
 * User: devel
 * Date: 5/2/13
 * Time: 7:04 PM
 * To change this template use File | Settings | File Templates.
 */

use Doctrine\Common\ClassLoader,
  Doctrine\ORM\Configuration,
  Doctrine\ORM\EntityManager,
  Doctrine\Common\Cache\ApcCache;

require_once 'Doctrine/Common/ClassLoader.php';

$classLoader = new ClassLoader('Doctrine\ORM', __DIR__);
$classLoader->register();
$classLoader = new ClassLoader('Doctrine\DBAL', __DIR__);
$classLoader->register();
$classLoader = new ClassLoader('Doctrine\Common', __DIR__);
$classLoader->register();
$classLoader = new ClassLoader('Doctrine\Symfony', __DIR__);
$classLoader->register();
$classLoader = new ClassLoader('Entities', __DIR__);
$classLoader->register();
$classLoader = new ClassLoader('Proxies', __DIR__);
$classLoader->register();

// Set up caches
$config = new Configuration;
$cache = new ApcCache;
$config->setMetadataCacheImpl($cache);
$driverImpl = $config->newDefaultAnnotationDriver(array(__DIR__."/Entities"));
$config->setMetadataDriverImpl($driverImpl);
$config->setQueryCacheImpl($cache);

// Proxy configuration
$config->setProxyDir(__DIR__ . '/Proxies');
$config->setProxyNamespace('Proxies');
$config->setMetadataCacheImpl(new \Doctrine\Common\Cache\ArrayCache);

// Database connection information
$connectionOptions = array(
  'driver'   => 'pdo_mysql',
  'host'     => 'localhost',
  'dbname'   => 'megaindex',
  'user'     => 'root',
  'password' => 'root',
  'driverOptions' => array(1002 => 'SET NAMES utf8')
);

// Create EntityManager
$em = EntityManager::create($connectionOptions, $config);
$conn = $em->getConnection();

$toSchema = new Doctrine\DBAL\Schema\Schema();

$domain = $toSchema->createTable("tbl_domain");
$domain->addColumn("id", "integer", array("length" => 11));
$domain->addColumn("name", "string", array("length" => 255));
$domain->setPrimaryKey(array("id"));
$domain->addUniqueIndex(array("name"));

$words = $toSchema->createTable("tbl_words");
$words->addColumn("id", "integer", array("length" => 11));
$words->addColumn("name", "string", array("length" => 255));
$words->addColumn("price", "string", array("length" => 255));
$words->addColumn("date", "integer");
$words->setPrimaryKey(array("id"));
//$myTable->addUniqueIndex(array("name"));

$regions = $toSchema->createTable("tbl_regions");
$regions->addColumn("id", "integer", array("length" => 11));
$regions->addColumn("name", "string", array("length" => 255));
$regions->addColumn("code", "string", array("length" => 10));
$regions->setPrimaryKey(array("id"));

$yp = $toSchema->createTable("tbl_yp");
$yp->addColumn("id", "integer", array("length" => 11));
$yp->addColumn("domain_id", "integer", array("length" => 11));
$yp->addColumn("word_id", "integer", array("length" => 11));
$yp->addColumn("region_id", "integer", array("length" => 11));
$yp->addColumn("position", "string", array("length" => 10));
$yp->addColumn("date", "integer");
$yp->setPrimaryKey(array("id"));
$yp->addForeignKeyConstraint($domain, array("domain_id"), array("id"), array("onUpdate" => "CASCADE"));
$yp->addForeignKeyConstraint($words, array("word_id"), array("id"), array("onUpdate" => "CASCADE"));
$yp->addForeignKeyConstraint($regions, array("region_id"), array("id"), array("onUpdate" => "CASCADE"));

$freq = $toSchema->createTable("tbl_frequency");
$freq->addColumn("id", "integer", array("length" => 11));
$freq->addColumn("word_id", "integer", array("length" => 11));
$freq->addColumn("region_id", "integer", array("length" => 11));
$freq->addColumn("frequency", "string", array("length" => 50));
$freq->addColumn("date", "integer");
$freq->setPrimaryKey(array("id"));
$freq->addForeignKeyConstraint($words, array("word_id"), array("id"), array("onUpdate" => "CASCADE"));
$freq->addForeignKeyConstraint($regions, array("region_id"), array("id"), array("onUpdate" => "CASCADE"));

$createSql = $toSchema->toSql($conn->getDatabasePlatform());
$dropSql = $toSchema->toDropSql($conn->getDatabasePlatform());

$conn->beginTransaction();
try
{
  foreach ($createSql as $query)
    $conn->executeQuery($query);
  $conn->commit();
}
catch (Exception $e)
{
  $conn->rollback();
  echo $e->getMessage();
}