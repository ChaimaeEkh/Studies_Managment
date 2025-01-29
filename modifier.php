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
    <style>
        body{
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            background: rgb(2,0,36);
            background: linear-gradient(90deg, rgba(2,0,36,1) 0%, rgba(9,9,121,1) 35%, rgba(0,212,255,1) 100%); 
        }
        fieldset{
            display: flex;
            flex-direction: column;
            padding: 20px;
            border: none;
            border-radius: 10px;
            margin-top: 30vh;
        }
        form {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px;
            background: rgba( 255, 255, 255, 0.25 );
            box-shadow: 0 8px 32px 0 rgba( 31, 38, 135, 0.37 );
            backdrop-filter: blur( 4px );
            -webkit-backdrop-filter: blur( 4px );
            border-radius: 10px;
            border: 1px solid rgba( 255, 255, 255, 0.18 );
            width: 500px;
            
        }
        fieldset legend{
            font-size: 24px;
            color: darkgrey;
        }
        input{
            width: 100%;
            max-width: 20em;
            display: flex;
            flex-direction: column;
            z-index: 2;
            background-color: #ccd;
            padding: 10px 20px;
            border: none;
            border-radius: 10px;
            box-shadow: 5px 5px 5px black;
            margin-bottom: 10px;
        }
        input:hover{
            background-position: 100% 0;
        }
        input:focus{
            outline: 2px dashed #ad2b89;
            outline-offset: 0.5em;
        }
        input:active{
            background-color: #cdd;
        }
        label{
            padding: 0 0.5em;
            margin-bottom: 0.5em;
            text-transform: uppercase;
            font-size: 0.875em;
            letter-spacing: 0.1em;
            color: #ccd;
            color: rgba(255, 220, 255, 0.6);
            cursor: pointer;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            padding: 10px;
            border-radius: 4px;
        }
        button{
            color: #fff;
            padding: 15px 25px;
            background-color: #38D2D2;
            background-image: radial-gradient(93% 87% at 87% 89%, rgba(0, 0, 0, 0.23) 0%, transparent 86.18%), radial-gradient(66% 66% at 26% 20%, rgba(255, 255, 255, 0.55) 0%, rgba(255, 255, 255, 0) 69.79%, rgba(255, 255, 255, 0) 100%);
            box-shadow: inset -3px -3px 9px rgba(255, 255, 255, 0.25), inset 0px 3px 9px rgba(255, 255, 255, 0.3), inset 0px 1px 1px rgba(255, 255, 255, 0.6), inset 0px -8px 36px rgba(0, 0, 0, 0.3), inset 0px 1px 5px rgba(255, 255, 255, 0.6), 2px 19px 31px rgba(0, 0, 0, 0.2);
            border-radius: 14px;
            font-weight: bold;
            font-size: 16px;
            border: 0;
            user-select: none;
            -webkit-user-select: none;
            touch-action: manipulation;
            cursor: pointer; 
            margin-top: 15px;
        }
        select{
            padding: 10px 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
            background-color: #f5c6cb;
        }
    </style>
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
