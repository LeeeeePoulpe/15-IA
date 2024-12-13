<html>
<head> 
<link href="form.css" rel="stylesheet" media="all" type="text/css"> 
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
</head>
<body>

<img src="loupe.png" width=200 height=100/>

<form action="search.php" method="get">
    <div>
        <label for="q">search :</label>
		<?php if (isset($_GET['q'])) {
		$requete = $_GET['q'];
		$requete = mb_strtolower($requete);
		echo '<input type="text" id="q" name="q" value="'.$requete.'">';}
		else {echo '<input type="text" id="q" name="q">';}
		?>
    </div>
</form>

<?php
require "indexation.php";

//---------------------------------------------------------
// fonction de construction du tableau des documents (sans pré-traitement)
// à partir de depeches-base.txt
// utile pour l'affichage des nouvelles dans leur état originel (sans pré-traitement)
function construction_tableau_documents_base(&$documents)
{
	$fic=fopen("depeches-base.txt", "r");
	while(!feof($fic))
	{
		//lecture d'une ligne
		$ligne= fgets($fic,1024);
		
		// lecture id du document
		$iddoc =substr($ligne,2,-1);
		$iddoc = intval($iddoc);
		$documents[$iddoc]=array();

		// lecture date
		$ligne= fgets($fic,1024);
		// lecture categorie
		$ligne= fgets($fic,1024);
	
		// lecture texte
		$ligne= fgets($fic,1024);
		$ligne = substr($ligne,2);
		while(!feof($fic) && (strlen($ligne)!=1))
		{
			$mots_de_la_ligne=explode(' ',$ligne);
			foreach($mots_de_la_ligne as $mot)
			{
			if (($mot!=""))
			{
				if (($mot!=""))
				{
					$documents[$iddoc][]=$mot;
				}
			}
			}
			$ligne= fgets($fic,1024);
		}
	}
	fclose($fic) ; 
}
//---------------------------------------------------------


//---------------------------------------------------------
// fonction de construction du tableau des documents (avec pré-traitement)
// à partir de depeches-base.txt
function construction_tableau_documents(&$documents)
{
	$mots_vides=array("un","une","au","à");//a compléter
	$fic=fopen("depeches.txt", "r");
	$i=1 ;//Compteur de ligne
	while(!feof($fic))
	{
		//lecture d'une ligne
		$ligne= fgets($fic,1024);
	
		// lecture id du document
		$iddoc =substr($ligne,2,-1);
		$iddoc = intval($iddoc);
		$documents[$iddoc]=array();

		// lecture date
		$ligne= fgets($fic,1024);
	
		// lecture categorie
		$ligne= fgets($fic,1024);
	
		// lecture texte
		$ligne= fgets($fic,1024);
		$ligne = substr($ligne,2);
		while(!feof($fic) && (strlen($ligne)!=1))
		{
			$mots_de_la_ligne=explode(' ',$ligne);
			foreach($mots_de_la_ligne as $mot)
			{
				$mot = rtrim($mot);
				$mot = mb_strtolower(rtrim($mot));
				if (($mot!=""))
				{
					if ((strpos($mot,"s'")===0) || (strpos($mot,"l'")===0) || (strpos($mot,"d'")===0))
					{
						$mot=substr($mot,2);
					}
					if (($mot!="") && !(in_array($mot,$mots_vides)))
					{
						$documents[$iddoc][]=$mot;
					}
				}
			}
		$ligne= fgets($fic,1024);
		}
	}
	fclose($fic) ; 
}
//---------------------------------------------------------

//---------------------------------------------------------
// fonction d'affichage d'une nouvelle d'identifiant $id 
// dont le contenu est rangé dans $doc
function affichage($id,$doc)
{
	echo "<i>nouvelle n°".$id."</i><BR/>";
	foreach($doc as $mot)
	{
		echo $mot." ";
	}
	echo "<br/><br/>";

}
//---------------------------------------------------------


//---------------------------------------------------------
// fonction construisant la liste des résultats et calculant le score en même temps
// on propose pour le calcul du score de faire une simple somme des tf*idf des termes
// présents dans la requête ET le document pour l'idf du terme on prendra log(N/df(t))
// Cette fonction modifie le tableau $res dont les clés sont les identifiants des documents
// et les valeurs le score des documents
//---------------------------------------------------------
function ajoute(&$res, $iimot, $mot)
{
    global $df;
    global $N;
    
    // Calcul de l'idf du mot
    $idf = log($N / $df[$mot]);
    
    foreach($iimot as $doc => $nbocc)
    {
        if (isset($res[$doc])) // ce document est déjà dans la liste des résultats
        {
            // Sommation du tf*idf du terme au score existant
            $res[$doc] += $nbocc * $idf;
        }
        else // ce document n'est pas encore dans la liste des résultats
        {
            // Initialisation du score avec le tf*idf du terme
            $res[$doc] = $nbocc * $idf;
        }
    }
}//--------------------------------------------

//--------------------------------------------
// PROGRAMME PRINCIPAL
if (isset($_GET['q']))
{
	$debut =microtime(true);
	$tabmots = explode(' ',$requete);
	$df=array();
	$inverse_index = array();
	$documents_base = array();
	$documents = array();

	// construction du tableau des documents sans prétraitement 

	construction_tableau_documents_base($documents_base);
	
	// construction du tableau des documents prétraités
	construction_tableau_documents($documents);

	// construction de l'index inversé et de la table df
	construction_index_inverse($documents,$inverse_index,$df);

	$N = count($documents);

	// $res est le tableau des résultats dont les clefs sont les identifiants des documents et dont les valeurs sont les scores des documents
	$res=array(); 
	
	
	if ($tabmots[0]!='')
	{
		// parcours des mots de la requête
		foreach($tabmots as $mot)
		{
			if (isset($inverse_index[$mot]))
			{
				// ajout des documents et/ou mise à jour de leur score
				ajoute($res,$inverse_index[$mot],$mot);
			}
		}

		
		$nbr=count($res);
		arsort($res); // tri de $res sur les scores (ordre décroissant)
		
		$fin =microtime(true);
		$delai = $fin - $debut;

		echo "<br/><br/>$nbr resultats trouvés en $delai ms<br/><br/><br/>";
		
		foreach($res as $id=>$nocc)
		{
			affichage($id,$documents_base[$id]);
		}
	}
}
//--------------------------------------------

?>
</body>
</html>
