<?php

use Doctrine\ORM\Configuration, Doctrine\Common\ClassLoader, Doctrine\Common\Cache\ApcCache;

require_once 'Doctrine/Common/ClassLoader.php';

class MegaIndexDbConfig
{
  private $_dbOpt;
  private $_dbName;
  private $config;

  protected function setDefaultSettings($path=__DIR__)
  {
    $classLoader = new ClassLoader('Doctrine\ORM', $path);
    $classLoader->register();
    $classLoader = new ClassLoader('Doctrine\DBAL', $path);
    $classLoader->register();
    $classLoader = new ClassLoader('Doctrine\Common', $path);
    $classLoader->register();
    $classLoader = new ClassLoader('Doctrine\Symfony', $path);
    $classLoader->register();
    $classLoader = new ClassLoader('Entities', $path);
    $classLoader->register();
    $classLoader = new ClassLoader('Proxies', $path);
    $classLoader->register();

    $this->config = new Configuration;
    $cache = new ApcCache;
    $this->config->setMetadataCacheImpl($cache);
    $driverImpl = $this->config->newDefaultAnnotationDriver(array(__DIR__."/Entities"));
    $this->config->setMetadataDriverImpl($driverImpl);
    $this->config->setQueryCacheImpl($cache);

    $this->config->setProxyDir(__DIR__.'/Proxies');
    $this->config->setProxyNamespace('Proxies');
    $this->config->setMetadataCacheImpl(new \Doctrine\Common\Cache\ArrayCache);
  }

  protected function setDbOptions($driver, $host, $user='', $password='')
  {
      $this->_dbOpt = array(
        'driver'   => $driver,
        'host'     => $host,
        'user'     => $user,
        'password' => $password,
        'driverOptions' => array(1002 => 'SET NAMES utf8'));
      if (!empty($this->_dbName))
        $this->_dbOpt['dbname'] = $this->_dbName;
  }

  protected function setDbName($name)
  {
    $this->_dbName = $name;
  }

  protected function getDbOptions()
  {
    return $this->_dbOpt;
  }

  protected function getDbName()
  {
    return $this->_dbName;
  }

  protected function getConfig()
  {
    return $this->config;
  }
}


