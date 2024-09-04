<?php
include 'src/_connexionDB.php';
session_start();
if (!isset($_SESSION['admin'])){
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
				<a href="Gestion_Admin.php">
					<img class="logo" src="img/logo_HEH_TEC.png" alt="logo HEH">
				</a>
			</div>
			<h1>Page statistiques</h1>
			<section>
			<?php
				$request = $DB->query("SELECT id_demande FROM demandes");
    			echo '<h2>Il y a ',$request->rowCount(),' demandes d\'impression enregistrées sur le site :</h2>';
    			$request->closeCursor();
    		?>
    		<div class="tabStat">
	    		<table>
	    			<tr>
	    				<th>Email</th><th>Nb Commande</th>
	    			</tr>
	    		<?php
	    			$request = $DB->query("SELECT email,id_utilisateur FROM utilisateurs");
	    			$request->setFetchMode(PDO::FETCH_OBJ);
					while ($result=$request->fetch() ) {
						$requestDm=$DB->query("SELECT id_demande FROM demandes WHERE id_utilisateur=$result->id_utilisateur");
						echo '<tr><td>',$result->email,'</td><td>',$requestDm->rowCount(),'</td></tr>';
						$requestDm->closeCursor();
					}
					$request->closeCursor();
				?>
				</table>
			</div>
			<br>
			<h2>Nombre de demandes d'impression par année :</h2>
			<div class="tabStat">
				<table>
				<?php
					$request=$DB->query('SELECT date FROM demandes ORDER BY date ASC');
					$request->setFetchMode(PDO::FETCH_OBJ);
					$lb[]="o";
					while ($result=$request->fetch()) {
						if(in_array(substr($result->date,0,4), $lb)){
							$i=$i+1 ;
						}else{
							if (isset($i)) {
								echo '<td>',$i,'</td></tr>';
								$i=1;
								$lb[]=substr($result->date,0,4);
								echo '<tr><td>',substr($result->date,0,4),'</td>';
							}else{
							$i=1;
							$lb[]=substr($result->date,0,4);
							echo '<tr><td>',substr($result->date,0,4),'</td>';
							}
						}
					}
					echo '<td>',$i,'</td></tr>';
				?>
				</table>
			</div>
			</section>
			<?php
				include 'footerAdmin.html';
			?>
		</div>
	</body>
</html>
