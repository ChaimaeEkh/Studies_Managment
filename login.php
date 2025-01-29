<?php
include("cone.php");
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    extract($_POST);
    $err = [];
    
    if (!isset($login) || empty($login)) {
        $err["login"] = "Le nom d'utilisateur est vide";
    }
    if (!isset($mdp) || empty($mdp)) {
        $err["mdp"] = "Le mot de passe est vide";
    }
    if(empty($err)){
        try {
            $req = $con->prepare("SELECT * FROM compteadministrateur WHERE prenom = ? AND motDePasse = ?");
            $req->execute([$login, $mdp]);
            $user = $req->fetch(PDO::FETCH_ASSOC);
            if (!empty($user)) {
                #creation de la cookie si l'utilisateur a cocher se souvenir de moi
                if(isset($rbm)){#lutilisateur a cocher la case
                    setcookie("login",$login,time()+3600*24*12*4);
                    setcookie("motDePasse",$mdp,time()+3600*24*12*4);
                    setcookie("login","",time()-1); #suppression de cookie
                }
                #ouvrir la session
                session_start();
                $_SESSION['login'] = $login;
                $_SESSION['nom'] = $user['nom'];
                $_SESSION['prenom'] = $user['prenom'];
                header("Location: accueil.php");
                exit;
            } else {
                $err["connexion"] = "Login ou mot de passe erroné";
            }
        } catch (PDOException $e) {
            echo "Erreur d'authentification : " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="src/login.css">
</head>
<body>
    <fieldset>
        <legend>Page de connexion</legend>
        <form method="POST">
        <?php if(isset($msg)) echo $msg;
            elseif(isset($_GET["msg"])) echo "<div class='succ'>".$_GET["msg"]."</div>"?>  
            <label for="login">Login</label><br>
            <?php if (isset($err["login"])) { echo '<div class="error">' . $err["login"] . '</div>'; } ?>
            <input type="text" name="login" placeholder="Nom d'utilisateur" value="<?php if(isset($_COOKIE["login"])) echo $_COOKIE["login"] ?>"><br><br>

            <label for="mdp">Mot de passe</label><br>
            <?php if (isset($err["mdp"])) { echo '<div class="error">' . $err["mdp"] . '</div>'; } ?>
            <input type="password" name="mdp" placeholder="Mot de passe" value="<?php if(isset($_COOKIE["motDePasse"])) echo $_COOKIE["motDePasse"] ?>"><br>
            <input type="checkbox" name="rbm" > Se souvenir de moi
            <?php if (isset($err["connexion"])) { echo '<div class="error">' . $err["connexion"] . '</div>'; } ?>
            <input type="submit" name="conct" value="Se connecter" class="btn">
        </form>
    </fieldset>
</body>
</html>
