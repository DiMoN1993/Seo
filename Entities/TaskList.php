<?php
/**
 * Created by JetBrains PhpStorm.
 * User: devel
 * Date: 6/7/13
 * Time: 1:35 PM
 * To change this template use File | Settings | File Templates.
 */
namespace Entities;
/**
 * @Entity
 * @Table(name="tbl_taskList")
 */
class TaskList
{
  /**
   * @Id @Column(type="integer")
   * @GeneratedValue(strategy="AUTO")
   */
  private $id;

  /**
   * @Column(type="string", length=10)
   */
  private $status;

  public function getId()
  {
    return $this->id;
  }

  public function getStatus()
  {
    return $this->status;
  }

  public function setStatus($status)
  {
    $this->status = $status;
  }
}
