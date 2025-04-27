    <?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// Chargement de l'autoloader de Composer
require_once 'vendor/autoload.php';

// Chargement des contrôleurs
require_once 'controllers/Controller.php';
require_once 'controllers/AuthController.php';
require_once 'controllers/TicketController.php';
require_once 'controllers/HomeController.php';
require_once 'controllers/AdminController.php';
require_once 'controllers/ProfileController.php';
require_once 'controllers/TechnicienController.php';

// Chargement des modèles
require_once 'models/Model.php';
require_once 'models/UserModel.php';
require_once 'models/TicketModel.php';
require_once 'models/RoleModel.php';
require_once 'models/AdminModel.php';

use Controllers\AuthController;
use Controllers\TicketController;
use Controllers\HomeController;
use Controllers\AdminController;
use Controllers\ProfileController;
use Controllers\TechnicienController;

// Configuration de Twig
$twig = require_once 'database/twig.php';

// Configuration du chemin de base
$basePath = '';
$twig->addGlobal('basePath', $basePath);
$twig->addGlobal('session', $_SESSION);

// Récupération de l'URL demandée
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$requestUri = str_replace($basePath, '', $requestUri);
$url = trim($requestUri, '/');

// Debug du routage
error_log('=== DEBUG ROUTAGE ===');
error_log('Request URI: ' . $_SERVER['REQUEST_URI']);
error_log('Base Path: ' . $basePath);
error_log('URL après traitement: ' . $url);

// Routes de l'application
$routes = [
    // Route d'accueil
    '' => ['HomeController', 'index'],

    // Routes d'authentification
    'login' => ['AuthController', 'showLoginForm'],
    'login/submit' => ['AuthController', 'login'],
    'register' => ['AuthController', 'showRegisterForm'],
    'register/submit' => ['AuthController', 'register'],
    'logout' => ['AuthController', 'logout'],

    // Routes des tickets
    'tickets' => ['TicketController', 'index'],
    'tickets/my' => ['TicketController', 'myTickets'],
    'tickets/create' => ['TicketController', 'showCreateForm'],
    'tickets/create/submit' => ['TicketController', 'create'],
    'tickets/view/{id}' => ['TicketController', 'view'],
    'tickets/edit/{id}' => ['TicketController', 'showEditForm'],
    'tickets/edit/{id}/submit' => ['TicketController', 'edit'],
    'tickets/comment/{id}' => ['TicketController', 'addComment'],

    // Routes du profil utilisateur
    'profile' => ['ProfileController', 'profile'],
    'profile/edit' => ['ProfileController', 'update'],

    // Routes d'administration
    'admin' => ['AdminController', 'index'],
    'admin/users' => ['AdminController', 'listUsers'],
    'admin/tickets' => ['AdminController', 'tickets'],
    'admin/tickets/assigned' => ['AdminController', 'assignedTickets'],
    'admin/tickets/{id}/edit' => ['AdminController', 'editTicket'],
    'admin/tickets/{id}/status' => ['AdminController', 'updateTicketStatus'],
    'admin/tickets/{id}/assign' => ['AdminController', 'assignTicket'],
    'admin/tickets/{id}/delete' => ['AdminController', 'deleteTicket'],
    'admin/tickets/{id}' => ['AdminController', 'viewTicket'],
    'admin/users/new' => ['AdminController', 'createUser'],
    'admin/users/{id}/edit' => ['AdminController', 'editUser'],
    'admin/users/{id}/delete' => ['AdminController', 'deleteUser'],
    'admin/settings' => ['AdminController', 'settings'],

    // Routes des techniciens
    'technicien/tickets' => ['TechnicienController', 'tickets'],
    'technicien/tickets/{id}/status' => ['TechnicienController', 'updateStatus'],
    'technicien/tickets/{id}/assign' => ['TechnicienController', 'assignToMe']
];

// Recherche de la route correspondante
$matchedRoute = null;
$params = [];

foreach ($routes as $pattern => $handler) {
    $pattern = str_replace('/', '\/', $pattern);
    $pattern = preg_replace('/\{(\w+)\}/', '(\d+)', $pattern);
    
    if (preg_match("/^$pattern$/", $url, $matches)) {
        $matchedRoute = $handler;
        array_shift($matches);
        $params = $matches;
        break;
    }
}

// Exécution du contrôleur approprié
if ($matchedRoute) {
    [$controllerName, $methodName] = $matchedRoute;
    $controllerName = "Controllers\\" . $controllerName;
    $controller = new $controllerName();
    call_user_func_array([$controller, $methodName], $params);
} else {
    // Page 404
    http_response_code(404);
    echo $twig->render('error/404.twig');
}
