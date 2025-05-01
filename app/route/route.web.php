<?php

namespace App\Route;

// Chargement du bootstrap des modèles
require_once __DIR__ . '/../models/bootstrap.model.php';

// Chargement des contrôleurs
require_once __DIR__ . '/../controllers/auth.controller.php';
require_once __DIR__ . '/../controllers/promotion.controller.php';
require_once __DIR__ . '/../controllers/referentiel.controller.php';
require_once __DIR__ . '/../controllers/apprenant.controller.php';
require_once __DIR__ . '/../controllers/dashboard.controller.php';

use App\Controllers;
use App\Models;

// Rendre les modèles disponibles globalement
global $model;

// Définition de toutes les routes disponibles avec leur fonction controller associée
$routes = [
    // Routes pour l'authentification
    'login' => 'App\Controllers\login_page',
    'login-process' => 'App\Controllers\login_process',
    'logout' => 'App\Controllers\logout',
    'change-password' => 'App\Controllers\change_password_page',
    'change-password-process' => 'App\Controllers\change_password_process',
    'forgot-password' => 'App\Controllers\forgot_password_page',
    'forgot-password-process' => 'App\Controllers\forgot_password_process',
    'reset-password' => 'App\Controllers\reset_password_page',
    'reset-password-process' => 'App\Controllers\reset_password_process',
    
    // Routes pour les promotions
    'promotions' => 'App\Controllers\list_promotions',
    'add-promotion' => 'App\Controllers\add_promotion_form',
    'add-promotion-process' => 'App\Controllers\add_promotion_process',
    'toggle-promotion-status' => 'App\Controllers\toggle_promotion_status',
    'promotion' => 'App\Controllers\promotion_page',
    'add_promotion_form' => 'App\Controllers\add_promotion_form',
    
    // Routes pour les référentiels
    'referentiels' => 'App\Controllers\list_referentiels',
    'all-referentiels' => 'App\Controllers\list_all_referentiels',
    'add-referentiel' => 'App\Controllers\add_referentiel_form',
    'add-referentiel-process' => 'App\Controllers\add_referentiel_process',
    'assign-referentiels' => 'App\Controllers\assign_referentiels_form',
    'assign-referentiels-process' => 'App\Controllers\assign_referentiels_process',
    'unassign-referentiel' => 'App\Controllers\unassign_referentiel_process',
    
    // Route par défaut pour le tableau de bord
    'dashboard' => 'App\Controllers\dashboard',
    
    // Route pour les erreurs
    'forbidden' => 'App\Controllers\forbidden',
    
    // Route par défaut (page non trouvée)
    '404' => 'App\Controllers\not_found',

    // Routes pour les apprenants
    'apprenants' => 'App\Controllers\list_apprenants',
    'add-apprenant' => 'App\Controllers\add_apprenant_form',
    'add-apprenant-process' => 'App\Controllers\add_apprenant_process',
    'view-apprenant' => 'App\Controllers\view_apprenant',
    'edit-apprenant' => 'App\Controllers\edit_apprenant_form',
    'edit-apprenant-process' => 'App\Controllers\edit_apprenant_process',
    'replace-apprenant' => 'App\Controllers\replace_apprenant_form',
    'replace-apprenant-process' => 'App\Controllers\replace_apprenant_process',
    'download-apprenants-list' => 'App\Controllers\download_apprenants_list',
    'import-apprenants' => 'App\Controllers\import_apprenants',
    'process-import-apprenants' => 'App\Controllers\process_import_apprenants',
    'download-apprenant-template' => 'App\Controllers\download_apprenant_template',
    'resend-welcome-email' => 'App\Controllers\resend_welcome_email',
    'waiting-list' => 'App\Controllers\waiting_list',
    'correct-waiting-apprenant' => 'App\Controllers\correct_waiting_apprenant',
    'process-correct-waiting-apprenant' => 'App\Controllers\process_correct_waiting_apprenant',
];

/**
 * Fonction de routage qui exécute le contrôleur correspondant à la page demandée
 *
 * @param string $page La page demandée
 * @return mixed Le résultat de la fonction contrôleur
 */
function route($page) {
    global $routes;
    
    // Cas spécial pour all-referentiels
    if ($page === 'all-referentiels') {
        \App\Controllers\list_all_referentiels();
        return;
    }
    
    // Vérifie si la route demandée existe
    $route_exists = array_key_exists($page, $routes);
    
    // Obtient la fonction à exécuter (route demandée ou 404 si non trouvée)
    $controller_function = $route_exists ? $routes[$page] : $routes['404'];
    
    // Exécute la fonction contrôleur
    return call_user_func($controller_function);
}