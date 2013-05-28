<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <link href="style.css" type="text/css" rel="stylesheet">
</head>
<body>
  <?php
    /**
     * Created by JetBrains PhpStorm.
     * User: devel
     * Date: 5/28/13
     * Time: 3:08 PM
     * To change this template use File | Settings | File Templates.
     * @variable freqs
     * @variable domains
     * @variable words
     * @variable yps
     * @variable regions
     */
    $megaIndex = new MegaIndex($_POST['region'], $_POST['url']);
    echo $megaIndex->getDomain()->getName();
  ?>
  <br>
    <?php echo $megaIndex->getRegion()->getName(); ?>
  <br>
<div class="tableWrap">
  <div class="tableRow header">
    <div class="tableCell header">
      Запрос
    </div>
    <div class="tableCell header">
      Дата
    </div>
    <div class="tableCell header">
      Цена
    </div>
    <div class="tableCell header">
      Позиция в яндексе
    </div>
    <div class="tableCell header">
      Частота
    </div>
  </div>
  <?php
    $words = explode(',', $_POST['words']);
    $countWords = count($words);
    for ($i=0; $i<$countWords; $i++):
  ?>
    <div class="tableRow">
      <div class="tableCell">
        <?php
          $word = $megaIndex->getWords($words[$i]);
          echo $word->getName();
        ?>
      </div>
      <div class="tableCell">
        <?php echo date('r', $word->getDate()); ?>
      </div>
      <div class="tableCell">
        <?php echo $word->getPrice(); ?>
      </div>
      <div class="tableCell">
        <?php echo $megaIndex->getYp($words[$i])->getPosition(); ?>
      </div>
      <div class="tableCell">
        <?php echo $megaIndex->getFrequency($words[$i])->getFrequency(); ?>
      </div>
    </div>
    <?php
      endfor;
    ?>
</div>
</body>
</html>
