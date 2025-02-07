<?php
session_start();    
$bdd = new PDO('mysql:host=localhost;dbname=site-restauration', 'root', '');


if(isset($_POST['envoi'])){
    if(isset($_POST['username']) and !empty($_POST['password'])){
        $nom_utilisateur = htmlspecialchars($_POST['username']);
        $recup_user = $bdd->prepare('SELECT * FROM utilisateur WHERE nom_utilisateur = ?');
        $recup_user->execute(array($nom_utilisateur));

        if($recup_user->rowCount() > 0){
            $user = $recup_user->fetch();
            if(password_verify($_POST['password'], $user['mdp'])){
                $_SESSION['username'] = $nom_utilisateur;
                $_SESSION['id'] = $user['id'];
                header('Location: creation.php');
            } else {
                $mdp_incorect = "Nom d'utilisateur ou mot de passe incorrect";
            }
        } else {
           $mdp_incorect = "Nom d'utilisateur ou mot de passe incorrect";
        }
    } else {
        $champ_remplie = "Veuillez remplir tous les champs";
    }
}
// Sign up
if(isset($_POST['inscription'])){
    if(!empty($_POST['username']) && !empty($_POST['password'])){
        $nom_utilisateur = htmlspecialchars($_POST['username']);
        $mot_de_passe = password_hash($_POST['password'], PASSWORD_DEFAULT);

        // Check the number of existing users
        $check_users = $bdd->query('SELECT COUNT(*) as user_count FROM utilisateur');
        $user_count = $check_users->fetch()['user_count'];

        if($user_count < 1) {
            $insert_user = $bdd->prepare('INSERT INTO utilisateur (nom_utilisateur, mdp) VALUES (?, ?)');
            $insert_user->execute(array($nom_utilisateur, $mot_de_passe));

            $inscription_ok = "Inscription réussie !";
        } else {
            $inscription_ok = "Le nombre maximum d'inscriptions est atteint.";
        }
    } else {
        $champ_remplie = "Veuillez remplir tous les champs";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Connexion</title>
</head>
<header>
<h2 class="text-header">Bienvenue</h2>

</header>
<body>
    <div class="form-index">
        <div class="formulaire-connection">
            <h2>Connexion</h2>
            <form action="" method="post" align="center" class="form-connection">
                <label for="username" class="text-index">Nom d'utilisateur</label>
                <br>
                <input type="text" name="username" autocomplete="off" placeholder="Nom d'utilisateur" required class="champ-index">
                <br>
                <label for="password" class="text-index">Mot de passe</label>
                <br>
                <input type="password" name="password" autocomplete="off" placeholder="Mot de passe" required class="champ-index">
                <br><br>
                <button type="submit" name="envoi">Se connecter</button>
            </form>
            <?php
            if(isset($champ_remplie)) {
                    echo $$champ_remplie;
                    }
            if(isset($mdp_incorect)) {
                echo $mdp_incorect;
                }
            ?>
        </div>
        <div class="formulaire-connection">
            <h2>Inscription</h2>
            <form method="POST" action="" class="form-connection">
                <label for="username" class="text-index">Nom d'utilisateur</label>
                <br>
                <input type="text" name="username" autocomplete="off" placeholder="Nom d'utilisateur" required class="champ-index">
                <br>
                <label for="password" class="text-index">Mot de passe</label>
                <br>
                <input type="password" name="password" autocomplete="off" placeholder="Mot de passe" required class="champ-index">
                <br><br>
                <button type="submit" name="inscription">S'inscrire</button>
            </form>
            <?php if(isset($inscription_ok)) {
                echo $inscription_ok;
                }
                if(isset($champ_remplie)) {
                    echo $$champ_remplie;
                    }
            ?>
        </div>
    </div>
</body>
<footer>
    <p class="text-footer1">© Touts droits réservé ©</p>
    <p class="text-footer2">Site de restauration</p>
</footer>
</html>