<?php
/**
 * Created by JetBrains PhpStorm.
 * User: devel
 * Date: 5/1/13
 * Time: 1:53 PM
 * To change this template use File | Settings | File Templates.
 */

namespace Entities;
/**
 * @Entity
 * @Table(name="tbl_region")
 */
class Region
{
  /**
   * @Id @Column(type="integer")
   * @GeneratedValue(strategy="AUTO")
   */
  private $id;

  /** @Column(type="string", length=255) */
  private $name;

  /** @Column(type="string", length=10) */
  private $code;

  public function getId()
  {
    return $this->id;
  }

  public function getName()
  {
    return $this->name;
  }

  public function setName($name)
  {
    $this->name = $name;
  }

  public function setCode($code)
  {
    $this->code = $code;
  }

  public function getCode()
  {
    return $this->code;
  }
}