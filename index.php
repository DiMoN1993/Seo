<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
</head>
<body>
        <?php
            function __autoload($className)
            {
              include_once($className.".php");
            }

            switch ($_GET['action'])
            {
              case 'getstat':
                  getStat();
                break;
              default:
                include_once ('main.php');
            }

            function getStat()
            {
              try
              {
                if (!empty($_POST['words']) && !empty ($_POST['url']) && !empty ($_POST['region']))
                {
                  $region = (int) $_POST['region'];
                  $url = trim ($_POST['url']);
                  $requests = explode(',',$_POST['words']);

                  /*$db = new DbConnection();
                  $answer = $db->getRecords($requests, $region, $url);*/

                  //if (empty($answer['non-exist']))
                  //{
                    $api = new MegaIndex($url, 'sagdiv@gmail.com', 'VqGPOv');
                    //var_dump($api->getPrice($requests));
                    foreach ($requests as $value)
                    {
                      $value = trim($value);
                      echo 'цена '.$api->getPrice($value);
                      echo '<br>';
                      echo 'вордстат '.$api->getWordStat($value, $region);
                      echo '<br>';
                      echo 'позиция '.$api->getYandexPosition($value, $region);
                      echo '<br>';
                    }
                  /*}
                  else
                  {
                    
                  }*/
                }
              }
              catch (Exception $e)
              {
                echo $e->getMessage();
              }
            }
        ?>
</body>
</html>
