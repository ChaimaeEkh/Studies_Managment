<?php
setcookie('visite', "", time()-1);
header("Location: accueil.php");
exit;
?>