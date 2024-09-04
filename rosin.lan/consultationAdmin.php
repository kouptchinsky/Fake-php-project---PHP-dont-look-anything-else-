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
			$c="checked";
			if(!isset($_POST['tri'])){$_POST['tri']=null;}
		?>
		<div class="profilText">
			<div class="imgBox">
				<a href="Gestion_Admin.php">
					<img class="logo" src="img/logo_HEH_TEC.png" alt="logo HEH">
				</a>
			</div>
			<h1>Consultation des demandes</h1>
			<div class="tab">
				<table>
					<tr>
						<form method="post">
							<td class="tri">Trier par :</td><td class="tri">Nom du fichier<input type="radio" name="tri" value="nomFichier" <?php if($_POST["tri"]=="nomFichier")echo $c; ?>></td><td class="tri">Pages<input type="radio" name="tri" value="pages" <?php if($_POST["tri"]=="pages")echo $c; ?>></td><td class="tri">Copies<input type="radio" name="tri" value="copies" <?php if($_POST["tri"]=="copies")echo $c; ?>></td><td class="tri">Couleur<input type="radio" name="tri" value="couleur" <?php if($_POST["tri"]=="couleur")echo $c; ?>></td><td class="tri">Reliure<input type="radio" name="tri" value="reliure" <?php if($_POST["tri"]=="reliure")echo $c; ?>></td><td class="tri">Page de garde<input type="radio" name="tri" value="pageGarde" <?php if($_POST["tri"]=="pageGarde")echo $c; ?>></td><td class="tri">Date de demande<input type="radio" name="tri" value="dateDemande" <?php if(empty($_POST["tri"])||$_POST=="dateDemande")echo $c; ?>></td><td class="tri"></td><td class="tri"></td><td class="tri"><button type="submit" name="connexion">Trier</button></td>
						</form>
					</tr>
					<tr>
						<th>Email enseignant</th> <th>Fichier</th> <th>Nb de pages</th> <th>Nb de copies</th> <th>Couleur</th> <th>Reliure</th> <th>Page de garde</th> <th>Date de demande</th> <th> Statut</th> <th>Valider</th><th>Rejetter</th>
					</tr>
				<?php
						$fichier="ORDER BY demandes.fichier_nom ASC";
						$pages="ORDER BY demandes.nombre_de_page DESC";
						$copies="ORDER BY demandes.nombre_de_copie DESC";
						$couleur="WHERE demandes.couleur=1 ORDER BY demandes.date DESC";
						$reliure="WHERE demandes.reliure=1 ORDER BY demandes.date DESC";
						$pageGarde="WHERE demandes.page_de_garde=1 ORDER BY demandes.date DESC";
						$alors="ORDER BY demandes.date DESC";
						$reqComplement="ORDER BY demandes.date DESC";
						if(!empty($_POST)){
							switch($_POST["tri"]){
								case "nomFichier": 
									$reqComplement=$fichier; 
									break; 
								case "pages": 
									$reqComplement=$pages; 
									break; 
								case "copies": 
									$reqComplement=$copies; 
									break; 
								case "couleur": 
									$reqComplement=$couleur; 
									break; 
								case "reliure": 
									$reqComplement=$reliure; 
									break; 
								case "pageGarde": 
									$reqComplement=$pageGarde; 
									break; 
								default: 
									$reqComplement=$alors; 
									break;
							}
						}
						$req='SELECT demandes.id_demande as iddem, demandes.fichier_nom as fichier, demandes.nombre_de_page as nbpages, demandes.couleur as couleur, demandes.nombre_de_copie as nbcopie, demandes.reliure as reliure, demandes.page_de_garde as pagegarde, demandes.date as jour, demandes.statut as statut, utilisateurs.email as email FROM demandes JOIN utilisateurs ON utilisateurs.id_utilisateur = demandes.id_utilisateur '.$reqComplement;
						$result=$DB->query($req);
						$result->setFetchMode(PDO::FETCH_OBJ);
						while ($output=$result->fetch() ) {
							// variables pour afficher Oui ou Non selon la valeur booléenne de couleur, reliure et pagegarde
							if ($output->couleur == 1){ $couleur = 'Oui';} else $couleur = 'Non';
							if ($output->reliure == 1){ $reliure = 'Oui';} else $reliure = 'Non';
							if ($output->pagegarde == 1){ $pagegarde = 'Oui';} else $pagegarde = 'Non';
							$condition='<td><a href="valider.php?id_demande='.$output->iddem.'"><img class="consultImg" src="img\V.jpg"></a></td><td><a href="supp_demande.php?id_demande='.$output->iddem.'"><img class="consultImg" src="img\X.jpg"></a></td>';
							echo "<tr> <td>",$output->email,"</td> <td>",$output->fichier,"</td> <td>",$output->nbpages,"</td> <td>",$output->nbcopie,"</td> <td>",$couleur,"</td> <td>",$reliure,"</td> <td>",$pagegarde,"</td> <td>",$output->jour,"</td> <td>",$output->statut,'</td>';
							if($output->statut!="Imprimée"&&$output->statut!="Refusée"){echo $condition;}
							echo "</tr>";
						}
						$result->closeCursor();
					?>
				</table>
			</div>
			<?php
				include 'footerAdmin.html';
			?>

		</div>
	</body>
</html>