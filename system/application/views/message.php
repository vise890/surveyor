<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title><?php echo $title; ?></title>
</head>
<body style="font-family: Arial, Helvetica, Geneva, sans-serif;">
    <h1><?php echo $message; ?></h1>
    </br>
    <?php
       echo anchor(@$link['href'].'/'.@$link['var'], @$link['label']);
    ?>
</body>
</html>
