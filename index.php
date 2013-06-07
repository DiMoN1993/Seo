<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
</head>
<body>
        <?php
            switch ($_GET['action'])
            {
              case 'tasks':
                  include_once('tasks.php');
                break;
              case 'getstat':
                  include_once('table2.php');
                break;
              case 'process':
                  include_once('process.php');
                break;
              default:
                include_once('form.php');
            }
        ?>
</body>
</html>
