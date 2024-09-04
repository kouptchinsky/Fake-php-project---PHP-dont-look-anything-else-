<?php
include 'src/_connexionDB.php';
session_start();
// si l'utilisateur n'est pas admin, il est redirigé vers la page de connexion
if (!isset($_SESSION['admin'])){
	header('Location: index.php');
	exit;
}

// vérifier s'il y a des demandes en attente
$request = $DB->prepare('SELECT * FROM demandes WHERE statut LIKE "En attente"');
$request->execute();
$nbrattente = $request->rowCount();
$request->closeCursor();
if ($nbrattente == 1){
	$notif = "($nbrattente demande en attente)";
} elseif ($nbrattente > 1){
	$notif = "($nbrattente demandes en attente)";
}
?>
<!DOCTYPE html>
<html lang="fr">
	<head>
		<meta name ="viewport" content="width=device-width, initial-scale1.0">
		<meta name="author" content="G10 Les Genies">
		<meta name="description" content="Site de gestion impression">
		<meta name="keywords" content="Impression, imprimerie, HEH">
		<title>Service Impression | Admin</title>
		<link rel="stylesheet" href="css/gestioncss.css" media="all" />
		<link rel="icon" href="img/index.png" />
	</head>
	
	<body>
		<?php
			include("header.php");
		?>	
		<div class="profilText">
			<div class="imgBox">
				<img class="logo" src="img/logo_HEH_TEC.png" alt="logo HEH">
			</div>
			<h1>Page administrateurs</h1>
			<h2>Que souhaitez-vous faire ?<br>
				<nav id="navTop">
					<a href="consultationAdmin.php">Consulter les demandes <?php if (isset($notif)) echo $notif; ?></a>
					<a href="consomable.php">Consulter et gérer les consommables</a>
					<a href="statistiques.php">Consulter les statistiques d'impression</a>
				</nav>
			</h2>
		</div>
	</body>
</html>
