<?php
require_once __DIR__ . '/vendor/autoload.php';

use Core\Security;

// Démarrer la session si nécessaire
if (!isset($_SESSION)) session_start();

// Vérifier le token CSRF dans l'URL pour sécuriser la déconnexion
if (isset($_GET['token']) && Security::checkCSRFToken($_GET['token'])) {
    
    // Détruire toutes les variables de session
    $_SESSION = [];
    
    // Supprimer le cookie de session si nécessaire
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    
    // Détruire la session
    session_destroy();
}

// Rediriger vers la page de connexion
header('Location: login.php');
exit();
