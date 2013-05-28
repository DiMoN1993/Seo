<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
</head>
<body>
        <?php
            $action = 'getstat';
            switch ($_GET['action'])
            {
              case $action:
                  include_once('main.php');
                break;
              default:
                include_once('form.php');
            }
        ?>
</body>
</html>
