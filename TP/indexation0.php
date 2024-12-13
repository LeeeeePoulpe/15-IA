<html>
<head> 
<link href="form.css" rel="stylesheet" media="all" type="text/css"> 
<meta charset="UTF-8"> 
</head>

<body>
<?php
require "indexation.php";





//------------------------------------------
// TEST
// un ensemble de documents
$documents = array(
"doc1"=>array("t1","t3","t1"),
"doc2"=>array("t2","t4","t4"),
"doc3"=>array("t3"),
"doc4"=>array("t1","t3"),
"doc5"=>array("t3"),
"doc6"=>array("t4"),
"doc7"=>array("t1","t4"),
"doc8"=>array("t4"),
"doc9"=>array("t1"));


//------------------------------------------
echo "<br>********************* TABLEAU DOCUMENTS ***********************************\n";
var_dump($documents);
//------------------------------------------

// table des df : les clés sont les termes et les valeurs sont les nombres de documents contenant ces termes
$df=array();
// index inversé : les clés sont les termes et les valeurs sont des tableaux (dont les clés sont des identifiants de documents et dont les valeurs sont les nombres d'occurences tf du terme dans le document)
$inverse_index = array();

construction_index_inverse($documents,$inverse_index,$df);

echo "<br>********************* INDEX INVERSE ***********************************\n";
var_dump($inverse_index);

echo "<br>********************* DF ***********************************\n";
var_dump($df);

echo '<br>******************** RESULTAT REQUETE t1 ET t3  ******************************'."\n";
$res = array_intersect_key($inverse_index['t1'], $inverse_index['t3']);

var_dump($res);


?>

</body>
</html>
