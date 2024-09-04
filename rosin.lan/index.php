<?php
include 'Classes/Utilisateur.php';
session_start();

// si l'utilisateur est déjà connecté -> redirection selon son role
if (isset($_SESSION['id_utilisateur'])){
    if (isset($_SESSION['admin'])){
        header('Location: Gestion_Admin.php');
        exit;
    } else {
        header('Location: Gestion_Enseignant.php');
        exit;
    }
}

// si la variable "$_POST" contient des informations alors on les traitres
if (!empty($_POST)){
    extract($_POST);
    $erreurs = [];

    // on se place sur le bon formulaire grâce au "name" de la balise "input" (ici "connexion")
    if (isset($_POST['connexion'])){
        $mail = htmlentities(strtolower(trim($mail)));
        $mdp = htmlentities(strtolower(trim($mdp)));
        

        // verification du mail
        if(empty($mail)){
            $erreurs['email'] = 'Erreur: veuillez entrer votre adresse mail';
        }

        // verification que le mail est dans le bon format
        elseif(!preg_match("/^[a-zA-Z0-9._-]+@[a-zA-Z0-9-]+\.[a-zA-Z.]{2,5}$/i", $mail)){
            $erreurs['email'] = 'Erreur: veuillez entrer une adresse mail valide';
        } 

        // verification du mot de passe
        if(empty($mdp)){
            $erreurs['mdp'] = 'Erreur: veuillez entrer votre mot de passe';
            $mdp = '';
        }

        // si toutes les conditions sont remplies alors on fait le traitement
        if (empty($erreurs)){
            // connection à l'active directory
            $ldapconn = ldap_connect("ldap://192.168.160.1:389") or die("Could not connect to LDAP server.");
            ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3);
            ldap_set_option ($ldapconn, LDAP_OPT_REFERRALS, 0);
            try{
                // si les identifiants de l'utilisateur sont bons :
                if (@ldap_bind($ldapconn, $mail, $mdp)){
                    // deco l'utilisateur
                    ldap_unbind($ldapconn);
                    // reset la connexion
                    $ldapconn = ldap_connect("ldap://192.168.160.1:389") or die("Could not connect to LDAP server.");
                    ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3);
                    ldap_set_option ($ldapconn, LDAP_OPT_REFERRALS, 0);
                    $ldapbind = @ldap_bind($ldapconn, 'Administrator@rosin.lan', 'Protected1');
                    
                    // si la connection admin est réussie :
                    if ($ldapbind){
                        // récuper le role de l'utilisateur :
                        // rechercher l'utilisateur dans l'active directory grâce à son mail
                        $e = explode('@', $mail);
                        $recherche = ldap_search($ldapconn, "dc=rosin,dc=lan", "samaccountname=".$e[0]);
                        // resultats de la recherche
                        $resultats = ldap_get_entries($ldapconn, $recherche);
                        // retrouver le groupe de l'utilisateur
                        $groupes = $resultats[0]['memberof'];
                        // retrouver le role selon le groupe globale de l'utilisateur
                        if ($groupes[0] == 'CN=GG_Enseignant_RW,OU=Enseignant,OU=HEH,DC=rosin,DC=lan'){
                            $role = 'enseignant';
                        } elseif ($groupes[0] == 'CN=GG_Administration_RW,OU=Administration,OU=HEH,DC=rosin,DC=lan'){
                            $_SESSION['admin'] = true;
                            $role = 'admin';
                        }
                        // verifier si l'utilisateur est déjà dans la table utilisateurs
                        $query = "SELECT * FROM utilisateurs WHERE email='$mail'";
                        $result = $DB->prepare($query);
                        $result->execute();
                        if ($result->rowCount() == 0){
                            $result->closeCursor();
                            // si l'utilisateur n'est pas dans la table utilisateurs alors on l'ajoute
                            $user = new Utilisateur($role, $mail);
                            $user->addNewUser($DB);
                            // on met l'id de l'utilisateur en variable de session
                            $_SESSION['id_utilisateur'] = $user->getUserId($DB);
                            // redirection vers les pages de gestion selon le role
                            if ($role == 'admin'){
                                header('Location: Gestion_Admin.php');
                                exit;
                            } elseif ($role == 'enseignant'){
                                header('Location: Gestion_Enseignant.php');
                                exit;
                            }
                        } else{
                            // si l'utilisateur est déjà dans la table utilisateurs alors on récupère son id
                            $row = $result->fetch();
                            $_SESSION['id_utilisateur'] = $row['id_utilisateur'];
                            // si l'utilisateur est enseignant, il est redirigé vers la page de création de demande
                            if ($role == 'admin'){
                                header('Location: Gestion_Admin.php');
                                exit;
                            } elseif ($role == 'enseignant'){
                                header('Location: Gestion_Enseignant.php');
                                exit;
                            } 
                        }
                    } 
                // si les identifiants ne correspondent pas :
                } else{ 
                    $erreurs['mdp'] = 'Erreur: mot de passe incorrect';
                    $mdp = "";
                }
            } catch (Exception $e){ }
        } 
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="author" content=" Guillaume ROSIN, Simon VAN MELLO, Sébastien VIERENDEELS, Anas Hamouchi, Nicolas Kouptchinsky" >
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Page de login du site HEH service imprimerie">
    <meta name="keywords" content="HEH, Imprimerie, se connecter">
    <link rel="stylesheet" href="css/gestioncss.css">
    <link rel="icon" href="img/index.png" />
    <title>Service Impression</title>
</head>
<body class="bodyConne">
    <header class="headerConne">
        <a href="https://www.heh.be" target="_blank" ><img class="logo" src="img/logo_HEH_TEC.png" alt="logo de l'HEH"></a>
    </header>
    <section class="sectionConn">
    <h2 class="h2Connexion">Se connecter</h2>
    <div class="formulaireCone">
    <form method="post" action='index.php'>
        <p <?php if (isset($erreurs['email'])) echo 'class="red" title="'.$erreurs['email'].'"'; ?>> <label for="mail"> Entrez votre email : <br>
        <input type="email" placeholder="Adresse email" name="mail" value="<?php if(isset($mail)){ echo $mail; }?>" required>
        </label>
        </p>
        <p <?php if (isset($erreurs['mdp'])) echo 'class="red" title="'.$erreurs['mdp'].'"'; ?>> <label for="mdp"> Entrez votre mot de passe : <br>
        <input type="password" placeholder="Mot de passe" name="mdp" value="<?php if(isset($mdp)){ echo $mdp; }?>" required><br>
        </label>
        </p>
        <p id="button"><button type="submit" name="connexion">Connexion</button></p>
        </form>
    </div>
    </section>
    </body>
</html>
