<?php
$visites = -1;
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
    <link rel="stylesheet" href="src/accueil.css">
</head>
<body>
<nav class="top-nav">
        <div class="nav-brand">
            <?php
                if(date("H")>="1" && date("H")<=12){
                    echo "<h1>Bonjour, ".$_SESSION['prenom']." " .$_SESSION['nom']." !</h1>";
                }else {
                    echo "<h1>Bonsoir, ".$_SESSION['prenom']." " .$_SESSION['nom']." !</h1>";
                }
            ?>
        </div>
        <div class="nav-actions">
            <p class="visit-count">Visites: <?php echo $visites; ?></p>
            <form method="POST" class="language-form">
                <a href="reinitialiser.php" class="reset-link">Réinitialiser</a>
            </form>
        </div>
    </nav>

    <?php if(isset($msg)) echo "<div class='alert success'>".$msg."</div>";
        elseif(isset($_GET["msgSupp"])) echo "<div class='alert success'>".$_GET["msgSupp"]."</div>";
        elseif(isset($_GET["msg"])) echo "<div class='alert success'>".$_GET["msg"]."</div>" ?>

    <div class="action-buttons">
        <a href="ajouter.php" class="btn add-btn">Ajouter stagiaire</a>
        <a href="deconnexion.php" class="btn logout-btn">Déconnexion</a>
    </div>

    <div class="cards-container">
        <?php
        try {
            $req = $con->prepare("SELECT stagiaire.*, filiere.intitule FROM stagiaire JOIN filiere ON stagiaire.idFiliere=filiere.idFiliere");
            $req->execute();
            $tab = $req->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            echo "<div class='alert error'>Erreur d'extraction des informations : " . $e->getMessage() . "</div>";
        }
        
        if (empty($tab)) {
            echo "<div class='empty-state'>Aucun stagiaire enregistré</div>";
        } else {
            foreach ($tab as $stg) { ?>
                <div class="card">
                    <div class="card-image">
                        <img src="<?php echo $stg['photoProfil']; ?>" alt="Photo de <?php echo $stg['prenom']; ?>">
                    </div>
                    <div class="card-content">
                        <h3><?php echo $stg['prenom'] . ' ' . $stg['nom']; ?></h3>
                        <div class="card-info">
                            <p><strong>ID:</strong> <?php echo $stg['idStagiaire']; ?></p>
                            <p><strong>Date de naissance:</strong> <?php echo $stg['dateNaissance']; ?></p>
                            <p><strong>Filière:</strong> <?php echo $stg['intitule']; ?></p>
                        </div>
                        <div class="card-actions">
                            <a href="modifier.php?idex=<?php echo $stg['idStagiaire']; ?>" class="btn edit-btn">Modifier</a>
                            <a href="supprimer.php?idex=<?php echo $stg['idStagiaire']; ?>" 
                               onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce stagiaire ?');" 
                               class="btn delete-btn">Supprimer</a>
                        </div>
                    </div>
                </div>
            <?php }
        } ?>
    </div>
</body>
</html>