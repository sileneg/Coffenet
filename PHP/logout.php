<?php
session_start();
session_unset();
session_destroy();
header("Location: /CoffeNet.com/index.php");
exit();
?>
