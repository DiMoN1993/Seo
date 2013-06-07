<?php
/**
 * Created by JetBrains PhpStorm.
 * User: devel
 * Date: 6/7/13
 * Time: 1:26 PM
 * To change this template use File | Settings | File Templates.
 */
namespace Entities;
/**
 * @Entity
 * @Table(name="tbl_task")
 */
class Task
{
  /**
   * @Id @Column(type="integer")
   * @GeneratedValue(strategy="AUTO")
   */
  private $id;

  /**
   *  @OneToOne(targetEntity="TaskList")
   *  @JoinColumn(name="list_id", referencedColumnName="id")
   */
  private $list;

  /**
   * @Column(type="string", length=100)
   */
  private $className;

  /**
   * @Column(type="blob")
   */
  private $body;

  public function getId()
  {
    return $this->id;
  }

  public function getList()
  {
    return $this->list;
  }

  public function setList($list)
  {
    $this->list = $list;
  }

  public function getClassName()
  {
    return $this->className;
  }

  public function setClassName($className)
  {
    $this->className = $className;
  }

  public function getBody()
  {
    return $this->date;
  }

  public function setBody($body)
  {
    $this->body = $body;
  }
}
