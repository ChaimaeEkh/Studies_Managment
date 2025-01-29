<?php
session_start();
if(!isset($_SESSION)|| empty($_SESSION)){
    header("Location: login.php");
    exit;
}
include("cone.php");


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    extract($_POST);
    $err = [];
    
    if (!isset($nom) || empty($nom)) {
        $err["nom"] = "Le nom est vide";
    }
    
    if (!isset($pre) || empty($pre)) {
        $err["pre"] = "Le prénom est vide";
    }
    
    if (!isset($date) || empty($date)) {
        $err["date"] = "La date de naissance est vide";
    }
    
    if (!isset($filiere) || empty($filiere)) {
        $err["filiere"] = "La filière est vide";
    }

    if (!isset($_FILES['image']) || $_FILES['image']['error'] != 0) {
        $err["image"] = "Erreur de chargement de l'image";
    } else {
        $tab_exts = ["image/jpeg", "image/jpg", "image/svg+xml", "image/png", "image/tiff"];
        if (!in_array($_FILES['image']['type'], $tab_exts)) {
            $err["image"] = "Veuillez entrer une image";
        }
        if ($_FILES['image']['size'] > 4000000) {
            $err["image"] = "La taille ne doit pas dépasser 4Mo";
        }
    }

    if (empty($err)) {
        move_uploaded_file($_FILES["image"]["tmp_name"],".\\image\\".$_FILES['image']['name']);
        try {
            $req = $con->prepare("INSERT INTO stagiaire (nom, prenom, dateNaissance, photoProfil, idFiliere) VALUES (?, ?, ?, ?, ?)");
            $req->execute([$nom, $pre, $date,  ".\\image\\".$_FILES['image']['name'],$filiere]);
            header("Location: accueil.php");
            exit;
        } catch (PDOException $e) {
            echo "Erreur d'insertion : " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter</title>
    <link rel="stylesheet" href="src/ajouter.css">
</head>
<body>
    <form method="POST" enctype="multipart/form-data">
        <a href="accueil.php">Retour</a>
        <h1>Ajouter un Stagiaire</h1>
        
        <label for="nom">NOM</label><br>
        <input type="text" name="nom" >
        <?php if (isset($err["nom"])) { echo '<div class="error">' . $err["nom"] . '</div>'; } ?><br>
        
        <label for="pre">PRENOM</label><br>
        <input type="text" name="pre">
        <?php if (isset($err["pre"])) { echo '<div class="error">' . $err["pre"] . '</div>'; } ?><br>
        
        <label for="date">DATE NAISSANCE</label><br>
        <input type="date" name="date">
        <?php if (isset($err["date"])) { echo '<div class="error">' . $err["date"] . '</div>'; } ?><br>
        
        <label for="image">PHOTO PROFIL</label><br>
        <input type="file" name="image"><br>
        <?php if (isset($err["image"])) { echo '<div class="error">' . $err["image"] . '</div>'; } ?><br>
        
        <label for="filiere">FILIERE</label><br>
        <select name="filiere">
            <?php 
            try {
                $req = $con->prepare("SELECT * FROM filiere");
                $req->execute();
                $filieres = $req->fetchAll(PDO::FETCH_ASSOC);
                foreach ($filieres as $f) {
                    $fil = $f['intitule'];
                    $id = $f['idFiliere'];
                    echo "<option value='$id'>$fil</option>";
                }
            } catch (PDOException $e) {
                echo "Erreur d'extraction des filières : " . $e->getMessage();
            }
            ?>
        </select>
        <?php if (isset($err["filiere"])) { echo '<div class="error">' . $err["filiere"] . '</div>'; } ?><br>
        
        <button type="submit">Ajouter</button>
    </form>
</body>
</html>
