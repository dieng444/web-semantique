<?php

// Encodage de l'output (UTF-8)
header('Content-Type: text/plain; charset=utf-8');

// Ouverture du fichier en lecture
$handle = fopen("poste.csv", "r");
if (!$handle) { echo "Problème d'ouverture du fichier."; }

// Initialisation du template
$template = "";

// Déclaration des préfixes d'URI
$template .= "@prefix rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#> . \n";
$template .= "@prefix rdfs: <http://www.w3.org/2000/01/rdf-schema#> . \n";
$template .= "@prefix unicaen: <http://www.unicaen.fr#> . \n";

// Création du premier triplet générique
$template .= "unicaen:Point_poste rdf:type rdfs:Class . \n";

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
	if ((strlen($ligne) > 0) && !(preg_match("#identifiant#", $ligne))) {
		list
        (
            $identifiant, 
            $libelle, 
            $caracteristique, 
            $adresse, 
            $complementAdresse, 
            $lieuDit, 
            $codePostal, 
            $localite, 
            $pays, 
            $latitude, 
            $longitude
        ) = explode(";", $ligne);
        
        // Construction des triplets RDF (instances et littéraux)
        if (!empty($libelle)) 
            $template .= 'unicaen:' . str_replace($specialChars, "_", $libelle)  . ' rdf:type unicaen:Point_poste .' . "\n";
        if (!empty($libelle) && !empty($identifiant)) 
            $template .= 'unicaen:' . str_replace($specialChars, "_", $libelle) . ' unicaen:se_situe _:Adresse' . $identifiant . ' .' . "\n";
        if (!empty($libelle) && !empty($identifiant)) 
            $template .= 'unicaen:' . str_replace($specialChars, "_", $libelle) . ' unicaen:identifiant ' . '"' . $identifiant . '" .' . "\n";
        if (!empty($libelle)) 
            $template .= 'unicaen:' . str_replace($specialChars, "_", $libelle) . ' unicaen:libelle ' . '"' . $libelle . '" .' . "\n";
        if (!empty($libelle) && !empty($caracteristique)) 
            $template .= 'unicaen:' . str_replace($specialChars, "_", $libelle) . ' unicaen:caracteristique ' . '"' . $caracteristique . '" .' . "\n";
        if (!empty($identifiant)) 
            $template .= '_:Adresse' . $identifiant . ' rdf:type unicaen:Coordonnees .' . "\n";
        if (!empty($identifiant) && !empty($adresse)) 
            $template .= '_:Adresse' . $identifiant . ' unicaen:intitule ' . '"' . $adresse . '" .' . "\n";
        if (!empty($identifiant) && !empty($codePostal)) 
            $template .= '_:Adresse' . $identifiant . ' unicaen:CP ' . '"' . $codePostal . '" .' . "\n";
        if (!empty($identifiant) && !empty($latitude)) 
            $template .= '_:Adresse' . $identifiant . ' unicaen:latitude ' . '"' . $latitude . '" .' . "\n";
        if (!empty($identifiant) && !empty($longitude)) 
            $template .= '_:Adresse' . $identifiant . ' unicaen:longitude ' . '"' . $longitude . '" .' . "\n";
        if (!empty($identifiant) && !empty($localite)) 
            $template .= '_:Adresse' . $identifiant . ' unicaen:commune ' . '"' . $localite . '" .' . "\n";
    }
}

// Affichage du résultat
echo $template;

// Fermeture du fichier
$close = fclose($handle);
if (!$close) { echo "Problème de fermeture du fichier."; }
