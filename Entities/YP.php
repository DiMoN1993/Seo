<?php
/**
 * Created by JetBrains PhpStorm.
 * User: devel
 * Date: 5/1/13
 * Time: 2:06 PM
 * To change this template use File | Settings | File Templates.
 */

namespace Entities;
/**
 * @Entity
 * @Table(name="tbl_yp")
 */
class YP
{
  /**
   * @Id @Column(type="integer")
   * @GeneratedValue(strategy="AUTO")
   */
  private $id;

  /**
   *  @OneToOne(targetEntity="Domain")
   *  @JoinColumn(name="domain_id", referencedColumnName="id")
   */
  private $domain;

  /**
   *  @OneToOne(targetEntity="Words")
   *  @JoinColumn(name="word_id", referencedColumnName="id")
   */
  private $word;

  /**
   *  @OneToOne(targetEntity="Region")
   *  @JoinColumn(name="region_id", referencedColumnName="id")
   */
  private $region;

  /**
   * @Column(type="string", length=10)
   */
  private $position;

  /** @Column(type="integer", length=11) */
  private $date;

  public function getId()
  {
    return $this->id;
  }

  public function getDomain()
  {
    return $this->domain;
  }

  public function setDomain($domain)
  {
    $this->domain = $domain;
  }

  public function setWord($word)
  {
    $this->word = $word;
  }

  public function getWord()
  {
    return $this->word;
  }

  public function setRegion($region)
  {
    $this->region = $region;
  }

  public function getRegion()
  {
    return $this->region;
  }

  public function getDate()
  {
    return $this->date;
  }

  public function setDate($date)
  {
    $this->date = $date;
  }

  public function getPosition()
  {
    return $this->position;
  }

  public function setPosition($position)
  {
    $this->position = $position;
  }
}