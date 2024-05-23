<?php // <-- Fichier application.php --> 
// Inclusion du fichier de configuration général    
require_once('class/configuration.php'); 
// inclue le fichier de configuration en fonction de l'environnement 
$configuration = new Configuration(); 
$configuration->load('configure'); 

// Inclusion des classes 
require_once('class/Database.php');    
require_once('payplug-php/lib/init.php');
require_once('class/Service/PaymentService.php');    
require_once('class/Controller/PaymentController.php');    

?> 
