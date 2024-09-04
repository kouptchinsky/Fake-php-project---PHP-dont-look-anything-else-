<?php
include 'src/_connexionDB.php';
session_start();
if (!isset($_SESSION['id_utilisateur'])){
	header('Location: index.php');
	exit;
}


if (isset($_GET['id_demande'])){
    // marque le statut de la demande en 'Refusée' si l'admin la supprime depuis sa page de consultation
    if (isset($_SESSION['admin'])){
        $id_demande = $_GET['id_demande'];
        $req = $DB->prepare("UPDATE demandes SET statut = 'Refusée' WHERE id_demande = :id_demande");
           $req->bindParam(':id_demande', $id_demande);
        $req->execute();
        $req->closeCursor();
        header('Location: consultationAdmin.php');
        exit;
    }
    else{
        // si l'utilisateur a demandé à supprimé sa propre demande, elle est réellement supprimée de la db
        $id_demande = $_GET['id_demande'];
        $request = $DB->prepare("DELETE FROM demandes WHERE id_demande = :id_demande");
        $request->bindParam(':id_demande', $id_demande);
        $request->execute();
        $request->closeCursor();
        header('Location: consultationEnseignant.php');
        exit;
    }
}
?>