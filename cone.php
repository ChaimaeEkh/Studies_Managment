<?php
try{
    $con = new PDO("mysql:host=localhost;dbname=gestionstagiaire_v1;charset=UTF8","root","");
}
catch(PDOException $e){
    echo "Erreur de connexion avec la base de données :". $e->getMessage();
}
?>