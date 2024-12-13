<html>
<head> 
<link href="form.css" rel="stylesheet" media="all" type="text/css"> 
<meta charset="UTF-8"> 
</head>
<body>
<?php
require "indexation.php";

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
			$ponctuations = array(".",",",";","!","?");
			$ligne=str_replace($ponctuations," ",$ligne);
			$mots_de_la_ligne=explode(' ',$ligne);
			foreach($mots_de_la_ligne as $mot)
			{
				$mot = rtrim($mot);
				$mot = strtolower(rtrim($mot));
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




//--------------------------------------------
// PROGRAMME PRINCIPAL A COMPLETER

$df=array();
$inverse_index = array();
$documents = array();

// construction du tableau des documents prétraités
construction_tableau_documents($documents);

// construction de l'index inversé et de la table df
construction_index_inverse($documents,$inverse_index,$df);


//test requêtes
echo "********************* RESULTAT REQUETE effet  ******************************\n";
//A COMPLETER
$res=  $inverse_index['effet'];
var_dump($res);

echo "********************* RESULTAT REQUETE effet OU co2  ******************************\n";
//A COMPLETER
$res= $inverse_index['effet'] + $inverse_index['co2']; 
var_dump($res);

?>

</body>
</html>
