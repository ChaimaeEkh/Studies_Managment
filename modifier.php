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
        if (isset($_GET['idex'])) {   
            extract($_GET);
            try {
                $req = $con->prepare("UPDATE stagiaire SET nom=?, prenom=?, dateNaissance=?, photoProfil=?, idFiliere=? WHERE idstagiaire=?");
                $req->execute([$nom, $pre, $date, ".\\image\\".$_FILES['image']['name'], $filiere, $idex]);
                header("Location: accueil.php?msg=Stagiaire bien modifié");
                exit;
            } catch (PDOException $e) {
                echo "Erreur de mise à jour : " . $e->getMessage();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier</title>
    <link rel="stylesheet" href="src/ajouter.css">
</head>
<body>
<?php
        if (isset($_GET['idex'])) {
            try {
                $req = $con->prepare("SELECT * FROM stagiaire WHERE idstagiaire=?");
                $req->execute([$_GET['idex']]);
                $stg = $req->fetch(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                echo "Erreur d'extraction : " . $e->getMessage();
            }
        }
    ?>

    <form method="POST" enctype="multipart/form-data">
        <a href="accueil.php">Retour</a>
        <h1>Modifier un Stagiaire</h1>
        
        <label for="nom">NOM</label><br>
        <input type="text" name="nom" value="<?= $stg["nom"] ?>">
        <?php if (isset($err["nom"])) { echo '<div class="error">' . $err["nom"] . '</div>'; } ?><br>
        
        <label for="pre">PRENOM</label><br>
        <input type="text" name="pre" value="<?= $stg["prenom"] ?>">
        <?php if (isset($err["pre"])) { echo '<div class="error">' . $err["pre"] . '</div>'; } ?><br>
        
        <label for="date">DATE NAISSANCE</label><br>
        <input type="date" name="date" value="<?= $stg["dateNaissance"] ?>">
        <?php if (isset($err["date"])) { echo '<div class="error">' . $err["date"] . '</div>'; } ?><br>
        
        <label for="image">PHOTO PROFIL</label><br>
        <input type="file" name="image"><br>
        <img src='<?= $stg['photoProfil'] ?>' alt="" width="100" height="100">
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
        
        <button type="submit">Modifier</button>
    </form>
</body>
</html>
