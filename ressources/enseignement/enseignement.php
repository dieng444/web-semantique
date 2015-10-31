<?php

// Encodage de l'output (UTF-8)
header('Content-Type: text/plain; charset=utf-8');

// Ouverture du fichier en lecture
$handle = fopen("enseignement.csv", "r");
if (!$handle) { echo "Problème d'ouverture du fichier."; }

// Initialisation du template
$template = "";

// Déclaration des préfixes d'URI
$template .= "@prefix rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#> . \n";
$template .= "@prefix rdfs: <http://www.w3.org/2000/01/rdf-schema#> . \n";
$template .= "@prefix unicaen: <http://www.unicaen.fr#> . \n";

// Création du premier triplet générique
$template .= "unicaen:Etablissement_enseignement rdf:type rdfs:Class . \n";

// Caractères spéciaux à ne pas mettre dans les triplets
$specialChars = array
                (
                    " ", 
                    "'", 
                    "\"", 
                    ",", 
                    ";", 
                    ".", 
                    ":", 
                    "?", 
                    "!", 
                    "&", 
                    "#", 
                    "\\", 
                    "/", 
                    "*", 
                    "(", 
                    ")", 
                    "[", 
                    "]", 
                    "{", 
                    "}", 
                    "~", 
                    "@", 
                    "€", 
                    "$"
                );

// Parcours du fichier
while (!feof($handle)) {
	$ligne = trim(fgets($handle));
	if ((strlen($ligne) > 0) && !(preg_match("#Code UAI#", $ligne))) {
		list
        (
            $codeUai, 
            $typeEtablissement, 
            $nom, 
            $sigle, 
            $statut, 
            $tutelle, 
            $universite, 
            $adresse, 
            $codePostal, 
            $commune, 
            $departement, 
            $academie, 
            $region, 
            $longitude, 
            $latitude, 
            $region
        ) = explode(";", $ligne);
        
        // Construction des triplets RDF (instances et littéraux)
        if (!empty($typeEtablissement)) 
            $template .= 'unicaen:' . str_replace($specialChars, "_", $typeEtablissement)  . ' rdfs:subClassOf unicaen:Etablissement_enseignement .' . "\n";
        if (!empty($tutelle)) 
            $template .= 'unicaen:' . str_replace($specialChars, "_", $tutelle)  . ' rdf:type unicaen:Tutelle .' . "\n";
        if (!empty($nom) && !empty($typeEtablissement)) 
            $template .= 'unicaen:' . str_replace($specialChars, "_", $nom) . ' rdf:type unicaen:' . str_replace($specialChars, "_", $typeEtablissement) . ' .' . "\n";
        if (!empty($nom) && !empty($codeUai)) 
            $template .= 'unicaen:' . str_replace($specialChars, "_", $nom) . ' unicaen:possede _:Adresse' . $codeUai . ' .' . "\n";
        if (!empty($nom) && !empty($tutelle)) 
            $template .= 'unicaen:' . str_replace($specialChars, "_", $nom) . ' unicaen:depend unicaen:' . str_replace($specialChars, "_", $tutelle) . ' .' . "\n";
        if (!empty($nom) && !empty($academie)) 
            $template .= 'unicaen:' . str_replace($specialChars, "_", $nom) . ' unicaen:academie ' . '"' . $academie . '" .' . "\n";
        if (!empty($nom) && !empty($statut)) 
            $template .= 'unicaen:' . str_replace($specialChars, "_", $nom) . ' unicaen:statut ' . '"' . $statut . '" .' . "\n";
        if (!empty($nom) && !empty($codeUai)) 
            $template .= 'unicaen:' . str_replace($specialChars, "_", $nom) . ' unicaen:code ' . '"' . $codeUai . '" .' . "\n";
        if (!empty($nom)) 
            $template .= 'unicaen:' . str_replace($specialChars, "_", $nom) . ' unicaen:nom ' . '"' . $nom . '" .' . "\n";
        if (!empty($nom) && !empty($sigle)) 
            $template .= 'unicaen:' . str_replace($specialChars, "_", $nom) . ' unicaen:sigle ' . '"' . $sigle . '" .' . "\n";
        if (!empty($nom) && !empty($typeEtablissement)) 
            $template .= 'unicaen:' . str_replace($specialChars, "_", $nom) . ' unicaen:type ' . '"' . $typeEtablissement . '" .' . "\n";
        if (!empty($codeUai)) 
            $template .= '_:Adresse' . $codeUai . ' rdf:type unicaen:Coordonnees .' . "\n";
        if (!empty($codeUai) && !empty($adresse)) 
            $template .= '_:Adresse' . $codeUai . ' unicaen:intitule ' . '"' . $adresse . '" .' . "\n";
        if (!empty($codeUai) && !empty($codePostal)) 
            $template .= '_:Adresse' . $codeUai . ' unicaen:CP ' . '"' . $codePostal . '" .' . "\n";
        if (!empty($codeUai) && !empty($latitude)) 
            $template .= '_:Adresse' . $codeUai . ' unicaen:latitude ' . '"' . $latitude . '" .' . "\n";
        if (!empty($codeUai) && !empty($longitude)) 
            $template .= '_:Adresse' . $codeUai . ' unicaen:longitude ' . '"' . $longitude . '" .' . "\n";
        if (!empty($codeUai) && !empty($departement)) 
            $template .= '_:Adresse' . $codeUai . ' unicaen:departement ' . '"' . $departement . '" .' . "\n";
        if (!empty($codeUai) && !empty($region)) 
            $template .= '_:Adresse' . $codeUai . ' unicaen:region ' . '"' . $region . '" .' . "\n";
        if (!empty($codeUai) && !empty($commune)) 
            $template .= '_:Adresse' . $codeUai . ' unicaen:commune ' . '"' . strtoupper($commune) . '" .' . "\n";
    }
}

// Affichage du résultat
echo $template;

// Fermeture du fichier
$close = fclose($handle);
if (!$close) { echo "Problème de fermeture du fichier."; }
