<?php
/**
 * Created by JetBrains PhpStorm.
 * User: devel
 * Date: 5/1/13
 * Time: 2:27 PM
 * To change this template use File | Settings | File Templates.
 */

namespace Entities;
/**
 * @Entity
 * @Table(name="tbl_frequency")
 */
class Frequency
{
  /**
   * @Id @Column(type="integer")
   * @GeneratedValue(strategy="AUTO")
   */
  private $id;

  /**
   *  @OneToOne(targetEntity="Words")
   *  @JoinColumn(name="word_id", referencedColumnName="id")
   */
  private $word;

  /**
   * @OneToOne(targetEntity="Region")
   *  @JoinColumn(name="region_id", referencedColumnName="id")
   */
  private $region;

  /**
   * @Column(type="string", length=50)
   */
  private $frequency;

  /** @Column(type="integer", length=11) */
  private $date;

  public function getId()
  {
    return $this->id;
  }

  public function getDate()
  {
    return $this->date;
  }

  public function setDate($date)
  {
    $this->date = $date;
  }

  public function getFrequency()
  {
    return $this->frequency;
  }

  public function setFrequency($frequency)
  {
    $this->frequency = $frequency;
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
}
