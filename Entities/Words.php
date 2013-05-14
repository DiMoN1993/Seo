<?php
/**
 * Created by JetBrains PhpStorm.
 * User: devel
 * Date: 5/1/13
 * Time: 1:51 PM
 * To change this template use File | Settings | File Templates.
 */

namespace Entities;
/**
 * @Entity
 * @Table(name="tbl_words")
 */
class Words
{
  /**
   * @Id @Column(type="integer")
   * @GeneratedValue(strategy="AUTO")
   */
  private $id;

  /** @Column(type="string", length=255) */
  private $name;

  /** @Column(type="string", length=255) */
  private $price;

  /** @Column(type="integer", length=11) */
  private $date;

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

  public function getPrice()
  {
    return $this->price;
  }

  public function setPrice($price)
  {
    $this->price = $price;
  }

  public function getDate()
  {
    return $this->date;
  }

  public function setDate($date)
  {
    $this->date = $date;
  }
}
