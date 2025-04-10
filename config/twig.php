<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../models/RoleModel.php';

$loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/../views');
$twig = new \Twig\Environment($loader, [
    'cache' => false,
    'debug' => true,
    'auto_reload' => true
]);

// Ajout de la fonction asset pour gérer les chemins des ressources
$twig->addFunction(new \Twig\TwigFunction('asset', function ($path) {
    return '/gestion_ticket_php/' . ltrim($path, '/');
}));

// Ajout de la fonction path pour gérer les URLs
$twig->addFunction(new \Twig\TwigFunction('path', function ($path) {
    // Debug de la génération d'URL
    error_log('=== DEBUG PATH ===');
    error_log('Path demandé: ' . $path);
    
    $path = trim($path, '/');
    $url = $path ? '/gestion_ticket_php/' . $path : '/gestion_ticket_php';
    
    error_log('URL générée: ' . $url);
    return $url;
}));

// Ajout de la fonction session pour accéder aux variables de session
$twig->addFunction(new \Twig\TwigFunction('session', function ($key) {
    return $_SESSION[$key] ?? null;
}));

// Ajout de la fonction is_granted pour vérifier les rôles
$twig->addFunction(new \Twig\TwigFunction('is_granted', function ($role) {
    error_log('=== VERIFICATION DES ROLES ===');
    error_log('Session complète: ' . print_r($_SESSION, true));
    
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
        error_log('Session non définie: user_id=' . isset($_SESSION['user_id']) . ', role=' . isset($_SESSION['role']));
        return false;
    }

    $userRole = (int)$_SESSION['role'];
    error_log('Role utilisateur: ' . $userRole . ' pour le test de rôle: ' . $role);
    error_log('Type de userRole: ' . gettype($userRole) . ', Valeur: ' . $userRole);
    error_log('Role original (avant cast): ' . $_SESSION['role'] . ' (' . gettype($_SESSION['role']) . ')');

    switch ($role) {
        case 'admin':
            $result = ($userRole == 2); // Comparaison avec 2 (admin)
            error_log('Test admin: userRole(' . $userRole . ') == 2 = ' . ($result ? 'true' : 'false'));
            return $result;
        case 'tech':
            $result = ($userRole == 1 || $userRole == 2); // Tech ou Admin
            error_log('Test tech: ' . ($result ? 'true' : 'false'));
            return $result;
        case 'user':
            return true;
        default:
            return false;
    }
}));

// Ajout de l'extension Debug
$twig->addExtension(new \Twig\Extension\DebugExtension());

return $twig;
