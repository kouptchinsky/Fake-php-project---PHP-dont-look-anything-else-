<?php
require 'Classes/Utilisateur.php';
require 'Classes/Demande.php';
session_start();

// si l'utilisateur n'est pas connecté, on le redirige vers la page de connexion
if (!isset($_SESSION['id_utilisateur'])){
    header('Location: index.php');
    exit;
} else {
    // sinon on récupère son email sur base de son id
    $request = $DB->prepare("SELECT * FROM utilisateurs WHERE id_utilisateur=:id_utilisateur");
    $request->execute(array(
        'id_utilisateur' => $_SESSION['id_utilisateur']
    ));
    $row = $request->fetch();
    $request->closeCursor();
    $email = $row['email'];
}


if(isset($_POST['demande'])){
    if (isset($_FILES['file'])){
        $file = $_FILES['file'];
        $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        // s'il y a une erreur lors de l'upload du fichier :
        if ($_FILES['file']['error'] != 0){
            $erreurs['file'] = 'Erreur: problème d\'upload du fichier';
        }
        // ou si le fichier fait plus de 16 Mo :
        elseif ($_FILES['file']['size'] > 16777216){
            $erreurs['file'] = 'Erreur: le fichier dépasse 16 Mo';
        }
        // ou si l'extension du fichier n'est pas autorisée :
        elseif ($file_extension != 'txt' && $file_extension != 'doc' && $file_extension != 'docx' && $file_extension != 'pdf'){
            $erreurs['file'] = 'Erreur: extension de fichier non autorisée';
        }
    }

    if (empty($_POST['pages'])){
        $erreurs['pages'] = 'Erreur: champ vide';
    } elseif ($_POST['pages'] < 1){
        $erreurs['pages'] = 'Erreur: nombre de pages invalide';
    }

    if (isset($_POST['couleur']	)){
        if (($_POST['couleur'] != false) && ($_POST['couleur'] != true)){
            $erreurs['couleur'] = 'Erreur: valeur invalide';
        }
    }

    if (empty($_POST['copies'])){
        $erreurs['copies'] = 'Erreur: champ vide';
    } elseif ($_POST['copies'] < 1){
        $erreurs['copies'] = 'Erreur: nombre de copies invalide';
    }

    if (isset($_POST['reliure'])){
        if (($_POST['reliure'] != false) && ($_POST['reliure'] != true)){
            $erreurs['reliure'] = 'Erreur: valeur invalide';
        }
    }

    if (isset($_POST['page_de_garde'])){
        if (($_POST['page_de_garde'] != false) && ($_POST['page_de_garde'] != true)){
            $erreurs['page_de_garde'] = 'Erreur: valeur invalide';
        }
    }
    
    // s'il n'y a pas d'erreur dans les inputs :
    if (empty($erreurs)){
        $id_utilisateur = $_SESSION['id_utilisateur'];
        $fichier = $_FILES['file'];
        $fichier_nom = $fichier['name'];
        $fichier_type = $fichier['type'];
        $fichier_contenu = file_get_contents($fichier['tmp_name']);
        $nombre_de_page = $_POST['pages'];
        if (isset($_POST['couleur'])){
            $couleur = 1;
        } else {
            $couleur = 0;
        }
        $nombre_de_copies = $_POST['copies'];
        if (isset($_POST['reliure'])){
            $reliure = 1;
        } else {
            $reliure = 0;
        }
        if (isset($_POST['page_de_garde'])){
            $page_de_garde = 1;
        } else {
            $page_de_garde = 0;
        }
        $date = date('Y-m-d');
        $demande = new Demande($id_utilisateur, $fichier_nom, $nombre_de_page, $couleur, $nombre_de_copies, $reliure, $page_de_garde, $date, 'En attente', $fichier_type, $fichier_contenu);
        // enregistre la nouvelle demande dans la db
        $demande->saveToDB($DB);
        // redirige vers un script qui va incrémenter le nombre d'impressions de l'utilisateur
        header('Location: nombre_impression++.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/gestioncss.css" media="all" />
    <meta name="description" content="Site de gestion impression">
    <meta name="keywords" content="Impression, imprimerie, HEH">
    <meta name="author" content="G10 Les Genies">
    <link rel="icon" href="img/index.png" />
    <title>Service Impression | Enseignant</title>
</head>
<body>
    <?php
        include("header.php");
    ?>
    <div class="profilText">
        <div class="imgBox">
            <a href="Gestion_Enseignant.php">
                <img class="logo" src="img/logo_HEH_TEC.png" alt="logo HEH">
            </a>
        </div>
        <section class="formulaire">
            <form action="nouvelle_demande.php" method="post" enctype="multipart/form-data">
                <div class="container">
                    <label for="file" <?php if (isset($erreurs['file']))echo 'class="red" title="'.$erreurs['file'].'"';  ?>>Fichier à imprimer:</label><br>
                    <input type="file" id="file" name="file" maxlength="16777216" required accept=".txt, .doc, .docx, .pdf"><br>
                </div>
                <label for="pages" <?php if (isset($erreurs['pages'])) echo 'class="red" title="'.$erreurs['pages'].'"'; ?>>Nombre de pages:</label><br>
                <input type="number" id="pages" name="pages" min="1" required><br>
                <label for="couleur" <?php if (isset($erreurs['couleur'])) echo 'class="red" title="'.$erreurs['couleur'].'"'; ?>>En couleur:</label><br>
                <input type="checkbox" id="couleur" name="couleur"><br>
                <label for="copies" <?php if (isset($erreurs['copies'])) echo 'class="red" title="'.$erreurs['copies'].'"'; ?>>Nombre de copies:<label><br>
                <input type="number" if="copies" name="copies" min="1" required><br>
                <label for="reliure" <?php if (isset($erreurs['reliure'])) echo 'class="red" title="'.$erreurs['reliure'].'"'; ?>>Avec reliure:</label><br>
                <input type="checkbox" id="reliure" name="reliure"><br>
                <label for="page_de_garde" <?php if (isset($erreurs['page_de_garde'])) echo 'class="red" title="'.$erreurs['page_de_garde'].'"'; ?>>Avec page de garde:</label><br>
                <input type="checkbox" id="page_de_garde" name="page_de_garde"><br>
                <input type="submit" value="Envoyer" name="demande">
            </form>
        </section>
    </div>
    <?php include 'footer.html'; ?>
</body>
</html>