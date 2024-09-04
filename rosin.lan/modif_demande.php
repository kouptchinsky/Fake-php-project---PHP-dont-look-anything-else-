<?php
require 'Classes/Utilisateur.php';
require 'Classes/Demande.php';
session_start();

// si l'utilisateur n'est pas connecté, on le redirige vers la page de connexion
if (!isset($_SESSION['id_utilisateur'])){
    header('Location: index.php');
    exit;
} else {
    $request = $DB->prepare("SELECT * FROM utilisateurs WHERE id_utilisateur=:id_utilisateur");
    $request->execute(array(
        'id_utilisateur' => $_SESSION['id_utilisateur']
    ));
    $row = $request->fetch();
    $request->closeCursor();
    $email = $row['email'];
}

// on vérifie que l'id de la demande est bien dans l'url pour la récupérer en $_GET
if (isset($_GET['id_demande'])){
    $id_demande = $_GET['id_demande'];
    $request = $DB->prepare("SELECT * FROM demandes WHERE id_demande = :id_demande");
    $request->bindParam(':id_demande', $id_demande);
    $request->execute();
    // si l'id n'est pas dans la base de données, on redirige vers la page de consultation des demandes
    if($request->rowCount() == 0){
        $request->closeCursor();
        header('Location: consultationEnseignant.php');
        exit;
    }  
    // vérifier si la demande appartient bien à l'utilisateur connecté
    $request_verif = 'SELECT id_utilisateur FROM demandes WHERE id_demande = :id_demande';
    $request_verif = $DB->prepare($request_verif);
    $request_verif->bindParam(':id_demande', $id_demande);
    $result  = $request_verif->execute();
    $row_verif = $request_verif->fetch();
    $request_verif->closeCursor();
    if ($row_verif['id_utilisateur'] != $_SESSION['id_utilisateur']){
        header('Location: consultationEnseignant.php');
        exit;
    }
    else{
        $row = $request->fetch();
        $request->closeCursor();
        // si la demande correspondante à l'id est déjà traitée, on redirige vers la page de gestion des enseignants
        if (($row['statut'] == "Validée") || ($row['statut'] == 'Refusée')){
            header('Location: consultationEnseignant.php');
            exit;
        }
        $fichier_nom = $row['fichier_nom'];
        $fichier_type = $row['fichier_type'];
        $nombre_de_page = $row['nombre_de_page'];
        $fichier_contenu = $row['fichier_contenu'];
        $couleur = $row['couleur'];
        $reliure = $row['reliure'];
        $page_de_garde = $row['page_de_garde'];
        $nombre_de_copies = $row['nombre_de_copie'];
        $date = $row['date'];
        $statut = $row['statut'];    
    }
} else{
    // si l'id de la demande n'est pas dans l'url ($_GET)
    header('Location: Gestion_Enseignant.php');
    exit;
}


if(isset($_POST['modifier'])){
    if ($_FILES['file']['name'] != ''){
        $file = $_FILES['file'];
        $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        // s'il y a une erreur lors de l'upload du fichier :
        if ($_FILES['file']['error'] != 0){
            $erreurs['file'] = 'Erreur: problème d\'upload du fichier, code: '.$_FILES['file']['error'];
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

    
    if (empty($erreurs)){
        $id_utilisateur = $_SESSION['id_utilisateur'];
        if ($_FILES['file']['name'] != ''){
            $fichier = $_FILES['file'];
            $fichier_nom = $fichier['name'];
            $fichier_type = $fichier['type'];
            $fichier_contenu = file_get_contents($fichier['tmp_name']);
        }
        if (isset($_POST['couleur'])){
            $couleur = 1;
        } else {
            $couleur = 0;
        }
        $nombre_de_page = $_POST['pages'];
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
        $query = 'UPDATE demandes SET
        fichier_nom = :fichier_nom,
        fichier_type = :fichier_type,
        fichier_contenu = :fichier_contenu,
        couleur = :couleur,
        nombre_de_page = :nombre_de_page,
        nombre_de_copie = :nombre_de_copies,
        reliure = :reliure,
        page_de_garde = :page_de_garde,
        date = :date
        WHERE id_demande = :id_demande';

        $request = $DB->prepare($query);
        $request->bindParam(':fichier_nom', $fichier_nom);
        $request->bindParam(':fichier_type', $fichier_type);
        $request->bindParam(':fichier_contenu', $fichier_contenu);
        $request->bindParam(':couleur', $couleur);
        $request->bindParam(':nombre_de_page', $nombre_de_page);
        $request->bindParam(':nombre_de_copies', $nombre_de_copies);
        $request->bindParam(':reliure', $reliure);
        $request->bindParam(':page_de_garde', $page_de_garde);
        $request->bindParam(':date', date('Y-m-d'));
        $request->bindParam(':id_demande', $id_demande);    
        $request->execute();
        $request->closeCursor();
        header('Location: consultationEnseignant.php');
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
            <form action="modif_demande.php?id_demande=<?= $id_demande ?>" method="post"  enctype="multipart/form-data">
                <div class="container">
                    <label for="file" <?php if (isset($erreurs['file']))echo 'class="red" title="'.$erreurs['file'].'"';  ?>>Fichier à modifier:</label><br>
                    <input type="file" id="file" name="file" maxlength="16777216" accept=".txt, .doc, .docx, .pdf"><br>
                </div>
                <label for="pages" <?php if (isset($erreurs['pages'])) echo 'class="red" title="'.$erreurs['pages'].'"'; ?>>Nombre de pages:</label><br>
                <input type="number" id="pages" name="pages" min="1" required <?= "value='$nombre_de_page'"; ?>><br>
                <label for="couleur" <?php if (isset($erreurs['couleur'])) echo 'class="red" title="'.$erreurs['couleur'].'"'; ?>>En couleur:</label><br>
                <input type="checkbox" id="couleur" name="couleur" <?php if ($couleur == 1) echo 'checked'; ?>><br>
                <label for="copies" <?php if (isset($erreurs['copies'])) echo 'class="red" title="'.$erreurs['copies'].'"'; ?>>Nombre de copies:</label><br>
                <input type="number" id="copies" name="copies" min="1" required <?= "value='$nombre_de_copies'"; ?>><br>
                <label for="reliure" <?php if (isset($erreurs['reliure'])) echo 'class="red" title="'.$erreurs['reliure'].'"'; ?>>Avec reliure:</label><br>
                <input type="checkbox" id="reliure" name="reliure" <?php if ($reliure == 1) echo 'checked'; ?>><br>
                <label for="page_de_garde" <?php if (isset($erreurs['page_de_garde'])) echo 'class="red" title="'.$erreurs['page_de_garde'].'"'; ?>>Avec page de garde:</label><br>
                <input type="checkbox" id="page_de_garde" name="page_de_garde" <?php if ($page_de_garde == 1) echo 'checked'; ?>><br>
                <br><br>
                <input type="submit" value="Modifier" name="modifier">
            </form>
        </section>
    </div>
    <?php include 'footer.html'; ?>
</body>
</html>