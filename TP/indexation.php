<?php 

// A COMPLETER
// fonction de mise à jour 
// - de l'index inversé $index
// - et du tableau $df 
// compte-tenu de la présence du terme $term dans le document $iddoc
function maj(&$index,&$df,$term,$iddoc)
{
	if (isset($index[$term])) // on a déjà rencontré ce terme dans la collection
	{
		if (isset($index[$term][$iddoc])) // on a déjà rencontré ce terme dans ce document
		{
			$index[$term][$iddoc]++;
		}
		else // première fois que l'on rencontre ce terme dans le document
		{
			$index[$term][$iddoc]=1;
			$df[$term]++;
		}
	}
	else // première fois que l'on rencontre ce terme
	{
			$index[$term]=array($iddoc=>1);
			$df[$term]=1;
	}
}

// A COMPLETER
// fonction de construction de l'index inversé 	
function construction_index_inverse($docs,&$index,&$df)
{
	//double boucle à écrire appelant la fonction maj
	foreach ($docs as $iddoc => $doc)
	{
		foreach ($doc as $term)
		{
			maj($index,$df,$term,$iddoc);
		}
	}
}
?>