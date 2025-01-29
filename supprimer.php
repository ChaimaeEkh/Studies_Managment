<?php
session_start();
if(!isset($_SESSION)|| empty($_SESSION)){
    header("Location: login.php");
    exit;
}
include('cone.php');
if(isset($_GET["idex"])){
    extract($_GET);
    try{
        $req = $con ->prepare("DELETE FROM stagiaire WHERE idstagiaire =?");
        $req ->execute([$idex]);
        header("location: accueil.php?msgSupp=Stagiaire bien supprimé");
        exit;
    }
    catch(PDOException $e){
        echo "Erreur de suppression: " . $e->getMessage();
    }
}
?>