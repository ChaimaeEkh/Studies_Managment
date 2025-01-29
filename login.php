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
            $req = $con->prepare("SELECT * FROM compteadministrateur WHERE loginAdmin = ? AND motPasse = ?");
            $req->execute([$login, $mdp]);
            $user = $req->fetch(PDO::FETCH_ASSOC);
            if (!empty($user)) {
                #creation de la cookie si l'utilisateur a cocher se souvenir de moi
                if(isset($rbm)){#lutilisateur a cocher la case
                    setcookie("login",$login,time()+3600*24*12*4);
                    setcookie("motpasse",$mdp,time()+3600*24*12*4);
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
            animation: animate 03s ease infinite;
        }
        @keyframes animate{
            0%{
                transform: translateY(0px);
                }
                50%{
                    transform: translateY(-10px);
                    }
                    100%{
                        transform: translateY(0px);
                        }
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
        .btn{
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
            <input type="password" name="mdp" placeholder="Mot de passe" value="<?php if(isset($_COOKIE["motpasse"])) echo $_COOKIE["motpasse"] ?>"><br>
            <input type="checkbox" name="rbm" > Se souvenir de moi
            <?php if (isset($err["connexion"])) { echo '<div class="error">' . $err["connexion"] . '</div>'; } ?>
            <input type="submit" name="conct" value="Se connecter" class="btn">
        </form>
    </fieldset>
</body>
</html>
