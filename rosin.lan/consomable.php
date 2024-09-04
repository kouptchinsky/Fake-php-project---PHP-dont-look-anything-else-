<?php
session_start();
include 'src/_connexionDB.php';
if (!isset($_SESSION['admin'])){
	header('Location: index.php');
	exit;
}

// si le formulaire a été rempli :
if(isset($_POST)){
	// ajoute une nouvelle imprimante dans la db
	if (isset($_POST['ajoutImp'])) {
		$request = $DB->prepare("INSERT INTO consomable (cartouche, feuille) VALUES (?,?)");
		$request->execute([0,0]);
		$request->closeCursor();
	}
	// supprime une imprimante de la db
	if(isset($_POST['suppImp'])){
		$req='DELETE FROM consomable WHERE imprimante = '.$_POST['imprimanteDel'];
		$request = $DB->prepare($req);
		$request->execute();
		$request->closeCursor();
	}
	// ajoute des consommables dans la db
	if(isset($_POST['ajoutConso'])){
		$req='SELECT cartouche, feuille FROM consomable WHERE imprimante='.$_POST['imprimanteAjoutConso'];
		$reqB=$DB->query($req);
		$reqB->setFetchMode(PDO::FETCH_OBJ);
		$result=$reqB->fetch();
		if ($_POST['cartouchesAjoutConso']=="") {
			$_POST['cartouchesAjoutConso']=0;
		} if ($_POST['feuillesAjoutConso']=="") {
			$_POST['feuillesAjoutConso']=0;
		}
		$cart=$result->cartouche+$_POST['cartouchesAjoutConso'];
		$feuil=$result->feuille+$_POST['feuillesAjoutConso'];
		$req='UPDATE consomable SET cartouche='.$cart.' , feuille='.$feuil.' WHERE imprimante='.$_POST['imprimanteAjoutConso'];
		$request = $DB->prepare($req);
		$request->execute();
		$request->closeCursor();
		$reqB->closeCursor();
	}
	// retire des concommables de la db
	if(isset($_POST['consomeConso'])){
		$erreur="";
		$req='SELECT cartouche, feuille FROM consomable WHERE imprimante='.$_POST['imprimanteConsomeConso'];
		$reqB=$DB->query($req);
		$reqB->setFetchMode(PDO::FETCH_OBJ);
		$result=$reqB->fetch();
		if ($_POST['cartouchesConsomeConso']=="") {
			$_POST['cartouchesConsomeConso']=0;
		} if ($_POST['feuillesConsomeConso']=="") {
			$_POST['feuillesConsomeConso']=0;
		}
		$cart=$result->cartouche-$_POST['cartouchesConsomeConso'];
		$feuil=$result->feuille-$_POST['feuillesConsomeConso'];
		if($cart>=0&&$feuil>=0){
		$req='UPDATE consomable SET cartouche='.$cart.' , feuille='.$feuil.' WHERE imprimante='.$_POST['imprimanteConsomeConso'];
		$request = $DB->prepare($req);
		$request->execute();
		$request->closeCursor();
		$reqB->closeCursor();
		$contenuA = file_get_contents('feuilleData.txt');
	    $contenuB = file_get_contents('cartoucheData.txt');
	    $contenuA=$contenuA+$_POST['feuillesConsomeConso'];
	    $contenuB=$contenuB+$_POST['cartouchesConsomeConso'];
	    $fichierA= fopen('feuilleData.txt','w+');
	    $fichierB= fopen('cartoucheData.txt','w+');
	    fputs($fichierA,$contenuA);
	    fputs($fichierB,$contenuB);
	    fclose($fichierA);
	    fclose($fichierB);
	    }else{
	    	$erreur="!!! Pas assez de consommables !!!";
	    }
	}
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
			<h1>Page des consommables</h1>
			<section>
				<h2>Infos sur les consomables :</h2>
				<table>
					<tr>
						<td>N° Imprimante</td><td>Cartouches</td><td>Feuilles</td>
					</tr>
					<?php
						$request = $DB->query("SELECT cartouche, feuille, imprimante FROM consomable");
			    		$request->setFetchMode(PDO::FETCH_OBJ);
			    		while ($result=$request->fetch() ) {
							echo "<tr><td>",$result->imprimante,"</td><td>",$result->cartouche,"</td><td>",$result->feuille,"</td></tr>";
						}
						$request->closeCursor();
		    		?>
	    		</table>
	    		<?php
	    			$contenuA = file_get_contents('feuilleData.txt');
	    			$contenuB = file_get_contents('cartoucheData.txt');
					echo '<h3 class="hCons">Nous avons utilisé un total de <u>',$contenuA,' feuilles</u> et de <u>',$contenuB,' cartouches</u>.</h3>';
	    		?>
	    		<h2>Gestion des consommables :</h2>
	    		<div class="englobe">
	    		<div <?php if(!empty($erreur)){echo 'class="redFORMCONSO" title="',$erreur,'"';}else{echo 'class="formConso"';} ?>>
		    		<h3>Qu'avez-vous consommé ?</h3>
		    		<form method="post">
		    			<p>
		    				<label for="imprimante">Numéro de l'imprimante:</label><br>
		    				<select name="imprimanteConsomeConso">
		    					<?php
		    						$request = $DB->query("SELECT cartouche, feuille, imprimante FROM consomable");
		    						$request->setFetchMode(PDO::FETCH_OBJ);
		    						$i=0;
		    						$select="selected";
				    				while ($result=$request->fetch() ) {
				    					if($i==1)$select="";
										echo '<option value="',$result->imprimante,'" ',$select,'>Imprimante ',$result->imprimante,'</option>';
										$i=1;
									}
									$request->closeCursor();
		    					?>
							</select>
		    			</p>
		    			<p<?php if(!empty($erreur)){echo 'title="',$erreur,'"';} ?>>
		    				<label for="feuilles">Nombre de feuilles: </label><br>
	               			<input type="number" id="feuilles" name="feuillesConsomeConso" min="0">
		    			</p>
		    			<p<?php if(!empty($erreur)){echo 'title="',$erreur,'"';} ?>>
		    				<label for="cartouches">Nombre de cartouches: </label><br>
	               			<input type="number" id="cartouches" name="cartouchesConsomeConso" min="0">
		    			</p>
		    			<button type="submit" name="consomeConso">Confirmer</button>
		    		</form>
		    		</div>
		    		<div class="formConso">
		    			<h3>Vous en avez racheter ?</h3>
		    		<form method="post">
		    			<p>
		    				<label for="imprimante">Numéro de l'imprimante:</label><br>
		    				<select name="imprimanteAjoutConso">
		    					<?php
		    						$request = $DB->query("SELECT cartouche, feuille, imprimante FROM consomable");
		    						$request->setFetchMode(PDO::FETCH_OBJ);
		    						$i=0;
		    						$select="selected";
				    				while ($result=$request->fetch() ) {
				    					if($i==1)$select="";
										echo '<option value="',$result->imprimante,'" ',$select,'>Imprimante ',$result->imprimante,'</option>';
										$i=1;
									}
									$request->closeCursor();
		    					?>
							</select>
		    			</p>
		    			<p>
		    				<label for="feuilles">Nombre de feuilles: </label><br>
	               			<input type="number" id="feuilles" name="feuillesAjoutConso" min="0">
		    			</p>
		    			<p>
		    				<label for="cartouches">Nombre de cartouches: </label><br>
	               			<input type="number" id="cartouches" name="cartouchesAjoutConso" min="0">
		    			</p>
		    			<button type="submit" name="ajoutConso">Confirmer</button>
		    		</form>
		    		</div>
		    		<div class="formConso">
		    			<h3>Un nouveau venu ?</h3>
		    			<form method="post">
		    			<p>
		    				<button type="submit" name="ajoutImp">Boum !</button><br>
		    				<label for="ajoutImp">Elle prendra le N° le plus grand !</label>
		    			</p>
		    			</form>
		    		</div>
		    		<div class="formConso">
		    			<h3>Un départ précipité ;-; ?</h3>
		    			<form method="post">
		    			<p>
		    				<label for="imprimante">Numéro de l'imprimante à supprimer:</label><br>
		    				<select name="imprimanteDel">
		    					<?php
		    						$request = $DB->query("SELECT cartouche, feuille, imprimante FROM consomable");
		    						$request->setFetchMode(PDO::FETCH_OBJ);
		    						$i=0;
		    						$select="selected";
				    				while ($result=$request->fetch() ) {
				    					if($i==1)$select="";
										echo '<option value="',$result->imprimante,'" ',$select,'>Imprimante ',$result->imprimante,'</option>';
										$i=1;
									}
									$request->closeCursor();
		    					?>
							</select>
		    			</p>
		    			<p>
		    				<button type="submit" name="suppImp">Supprimer l'imprimante</button><br>
		    			</p>
		    			</form>
		    		</div>
		    		</div>
			</section>
			<?php
				include 'footerAdmin.html';
			?>
		</div>
	</body>
</html>
