<?php
/**
 * Created by JetBrains PhpStorm.
 * User: devel
 * Date: 6/3/13
 * Time: 5:10 PM
 * To change this template use File | Settings | File Templates.
 */
require_once('MegaIndex.php');

$mi = new MegaIndex($_POST['region'], $_POST['url']);
$array = explode(',', $_POST['words']);

foreach($array as $value)
{
  $words[] = trim($value);
}

$list = $mi->createNewList();
$mi->checkWords($words);
$mi->taskPrice($words, $list);
foreach ($words as $oneWord)
{
  $mi->taskFrequency($oneWord, $list);
  $mi->taskYp($oneWord, $list);
}
//http_redirect('');