<?php // <-- Fichier config/configure.local.php --> 
// <!-- Fichier Configuration --> 
// variables de connexion en fonction du hostname à mettre dans un fichier de configuration 

// Configuration de la base de données 
const DB_HOST = 'localhost';
const DB_USERNAME = 'root';
const DB_PASSWORD = '';
const DB_DATABASE = 'payplug';

// Configuration projet 
define('URL', $_SERVER['REQUEST_URI']); 
const SUPER_ADMIN_ID = 0; 

// Configuration de l'envoi de mail 
const EMAIL_HOST = 'smtp.gmail.com';
const EMAIL_PORT = '587';
const EMAIL_LOGIN = '';
const EMAIL_PASSWORD = '';
const EMAIL_FROM = '';

// Clé secrète de Payplug
const SECRET_KEY_PAYPLUG_TEST = "#key";
const SECRET_KEY_PAYPLUG_LIVE = "#key";

?> 
