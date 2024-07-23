<?php
session_start(); // Assurez-vous que session_start() est appelé

// Détruire toutes les variables de session
$_SESSION = array();

// Si vous utilisez des cookies de session, les supprimer
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
}

// Détruire la session
session_destroy();

// Rediriger vers la page d'accueil ou de connexion
header("Location: login.php");
exit();

