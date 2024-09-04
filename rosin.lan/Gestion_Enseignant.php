<?php
session_start();
if (!isset($_SESSION['id_utilisateur'])){
	header('Location: index.php');
	exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
	<head>
		<meta name ="viewport" content="width=device-width, initial-scale1.0">
		<meta name="author" content="G10 Les Genies">
		<meta name="description" content="Site de gestion impression">
		<meta name="keywords" content="Impression, imprimerie, HEH">
		<title>Service Impression | Enseignant</title>
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
			<h1>Page enseignants</h1>
			<h2>Que souhaitez-vous faire ?<br>
				<nav id="navTop">
					<a href="nouvelle_demande.php">Remplir un formulaire de demande d'impression</a>
					<a href="consultationEnseignant.php">Consulter mes demandes</a>
				</nav>
			</h2>
		</div>
	</body>
</html>
