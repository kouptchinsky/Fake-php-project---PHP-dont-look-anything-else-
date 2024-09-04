<?php
include 'src/_connexionDB.php';
session_start();
// si l'utilisateur n'est pas connecté, on le redirige vers la page de connexion
if (!isset($_SESSION['id_utilisateur'])){
    header('Location: index.php');
    exit;
} else {
    // sinon on incrémente son compteur d'impression
    $request = $DB->prepare("UPDATE utilisateurs SET nombre_impression=nombre_impression+1 WHERE id_utilisateur=:id_utilisateur");
    $request->execute(array(
        'id_utilisateur' => $_SESSION['id_utilisateur']
    ));
    $request->closeCursor();
    header('Location: consultationEnseignant.php');
    exit;
}
?>