<?php
	session_start();
	// si l'utilisateur n'est pas admin, on le redirige vers la page de connexion
	if (!isset($_SESSION['admin'])){
	    header('Location: index.php');
	    exit;
	}

	include 'src/_connexionDB.php';
	if(isset($_GET['id_demande'])){
		$id_demande=$_GET['id_demande'];
		$req = $DB->prepare("UPDATE demandes SET statut = 'Imprimée' WHERE id_demande = :id_demande");
   		$req->bindParam(':id_demande', $id_demande);
    	$req->execute();
		$req->closeCursor();
    	header('Location: consultationAdmin.php');
    	exit;
	}
?>