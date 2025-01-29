<?php
$visites = 1;
if(isset($_COOKIE['visite'])) {
    $visites = $_COOKIE['visite'] + 1;
    setcookie('visite', $visites);
} else {
    setcookie('visite', $visites);
    header("Location: accueil.php");
    exit;
    if (!preg_match('/^06\s\d{8}$/', $tel)) {
        $errors[] = "Numéro de téléphone doit être comme suit : 06 00000000";
}
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['enregistrer'])) {
        if (isset($_POST['langue'])) {
            $langue = $_POST['langue'];
            setcookie('optL', $langue); 
            $_COOKIE['optL'] = $langue;
        }
    }
}

session_start();
if(!isset($_SESSION) || empty($_SESSION)){
    header("Location: login.php");
    exit;
}
include("cone.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            color: #333;
            margin: 0;
            padding: 0;
        }
        
        .container {
            width: 80%;
            margin: 0 auto;
            padding: 20px;
            background: rgba(255, 255, 255, 0.8);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            backdrop-filter: blur(10px);
        }
        
        h1 {
            text-align: center;
            color: #333;
        }
        .lien{
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        a {
            display: inline-block;
            margin: 10px;
            padding: 10px 20px;
            background-color: #333;
            color: #fff;
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.3s ease;
        }
        .lien button{
            display: inline-block;
            margin: 10px;
            padding: 10px 20px;
            background-color: #fff;
            color: #333;
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.3s ease;
        }
        
        a:hover {
            background-color: #fff;
            color: #333;
            box-shadow: 8px 8px 8px;
        }
        .lien button:hover{
            background-color: #333;
            color: #fff;
            box-shadow: 8px 8px 8px white;
        }
        table {
            margin: 10px;
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }
        
        th {
            background-color: #333;
            color: #fff;
        }
        
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .succ {
            color: green;
            margin: 20px;
            padding: 10px;
            border: 1px solid green;
            border-radius: 4px;
            background-color: #e0ffe0;
        }
    </style>
</head>
<body>
<?php if(isset($msg)) echo $msg;
    elseif(isset($_GET["msgSupp"])) echo "<div class='succ'>".$_GET["msgSupp"]."</div>"?>
<?php if(isset($msg)) echo $msg;
    elseif(isset($_GET["msg"])) echo "<div class='succ'>".$_GET["msg"]."</div>"?>

        
        <div class="lien">
            <a href="ajouter.php">Ajouter stagiaire</a>
            <a href="deconnexion.php">Déconnexion</a>
        </div>
        <p>Vous avez visité cette page <?php echo $visites; ?> fois.</p>
        <form method="POST">
            <select name="langue">
                <option value="ar" <?php if (isset($_COOKIE['optL']) && $_COOKIE['optL'] == 'ar') echo "selected"; ?>>Arabe</option>
                <option value="fr" <?php if (isset($_COOKIE['optL']) && $_COOKIE['optL'] == 'fr') echo "selected"; ?>>Français</option>
                <option value="en" <?php if (isset($_COOKIE['optL']) && $_COOKIE['optL'] == 'en') echo "selected"; ?>>Anglais</option>
            </select>
            <input type="submit" name="enregistrer">
            <a href="reinitialiser.php">réinitialiser</a>
        </form>
            <h1>
                <?php 
                    if(date("H")>="1" && date("H")<=12){
                        echo "Bonjour !".$_SESSION['nom']." " .$_SESSION['prenom'];
                    }else {
                        echo "Bonsoir !".$_SESSION['nom']." " .$_SESSION['prenom'];
                    }
                ?>
            </h1>
    
    <table>
        <tr>
            <th>idstagiaire</th>
            <th>nom</th>
            <th>prenom</th>
            <th>DateNaissance</th>
            <th>PhotoProfil</th>
            <th>Filiere</th>
            <th>Action</th>
        </tr>
        <?php 
        try {
            $req = $con-> prepare("SELECT stagiaire.*, filiere.intitule FROM stagiaire JOIN filiere ON stagiaire.idFiliere=filiere.idFiliere");
            $req->execute();
            $tab = $req->fetchAll(PDO::FETCH_ASSOC);
        }catch(PDOException $e){
            echo "Erreur d'extraction des informations : " . $e->getMessage();
        }
        if (empty($tab)) {
            echo "<tr><td colspan='8'>Pas de stagiaire</td></tr>";
        } else {
            foreach ($tab as $stg) {
                echo "<tr>";
                echo "<td>".$stg['idstagiaire']."</td>";
                echo "<td>".$stg['nom']."</td>";
                echo "<td>".$stg['prenom']."</td>";
                echo "<td>".$stg['dateNaissance']."</td>";
                echo "<td><img src='".$stg['photoProfil']."' width='100' height='100'></td>";
                echo "<td>".$stg['intitule']."</td>";  
                echo "<td><a href='modifier.php?idex=".$stg['idstagiaire']."'>Modifier</a> | <a href='supprimer.php?idex=".$stg['idstagiaire']."' onclick=\"return confirm('Êtes-vous sûr de vouloir supprimer ce stagiaire ?');\">Supprimer</a></td>";
                echo "</tr>";
            }
        }
        ?>
    </table>
</body>
</html>