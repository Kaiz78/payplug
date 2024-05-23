<?php // <-- Fichier class/configuration.php --> 
class Configuration{ 
    public function load($filename) 
	{ 
		if ($_SERVER['HTTP_HOST'] == 'localhost') { 
			require_once __DIR__ . "/../config/$filename.local.php"; 
		} 
		else if ($_SERVER['HTTP_HOST'] == 'preprod') { 
			require_once __DIR__ . "/../config/$filename.preprod.php"; 
		} 
		else { 
			require_once __DIR__ . "/../config/$filename.prod.php"; 
		} 
	} 
} 
?> 
