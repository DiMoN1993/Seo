<?php

use Doctrine\ORM\Configuration, Doctrine\Common\ClassLoader, Doctrine\Common\Cache\ApcCache;

require_once 'Doctrine/Common/ClassLoader.php';

class MegaIndexDbConfig
{
  public $_classLoader;
  private $_config;
  public $_cache;
  private $_dbOpt;
  private $_dbName;

  public function setDefaultSettings($path=__DIR__)
  {
    $this->_classLoader = new ClassLoader('Doctrine\ORM', $path);
    $this->_classLoader->register();
    $this->_classLoader = new ClassLoader('Doctrine\DBAL', $path);
    $this->_classLoader->register();
    $this->_classLoader = new ClassLoader('Doctrine\Common', $path);
    $this->_classLoader->register();
    $this->_classLoader = new ClassLoader('Doctrine\Symfony', $path);
    $this->_classLoader->register();
    $this->_classLoader = new ClassLoader('Entities', $path);
    $this->_classLoader->register();
    $this->_classLoader = new ClassLoader('Proxies', $path);
    $this->_classLoader->register();

    $this->_config = new Configuration;
    $this->_cache = new ApcCache;
    $this->_config->setMetadataCacheImpl($this->_cache);
    $driverImpl = $this->_config->newDefaultAnnotationDriver(array(__DIR__."/Entities"));
    $this->_config->setMetadataDriverImpl($driverImpl);
    $this->_config->setQueryCacheImpl($this->_cache);

    $this->_config->setProxyDir(__DIR__.'/Proxies');
    $this->_config->setProxyNamespace('Proxies');
    $this->_config->setMetadataCacheImpl(new \Doctrine\Common\Cache\ArrayCache);
  }

  public function setDbOptions($driver, $host, $user='', $password='')
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

  public function setDbName($name)
  {
    $this->_dbName = $name;
  }

  public function getConfig()
  {
    return $this->_config;
  }

  public function getDbOptions()
  {
    return $this->_dbOpt;
  }

  public function getDbName()
  {
    return $this->_dbName;
  }
}


