<?php

namespace App\Controllers;

require_once __DIR__ . '/../enums/path.enum.php';
require_once __DIR__ . '/../services/session.service.php';

use App\Enums;
use App\Services;

// Fonctions communes à tous les contrôleurs
function render($layout, $view, $data = []) {
    global $session_services;
    
    // Extraction des données pour les rendre disponibles dans la vue
    extract($data);
    
    // Récupération du message flash s'il existe
    $flash = $session_services['get_flash_message']();
    
    // Démarrage de la mise en tampon pour stocker la vue
    ob_start();
    require_once __DIR__ . '/../views/' . $view;
    $content = ob_get_clean();
    
    // Chargement du layout avec le contenu de la vue
    require_once __DIR__ . '/../views/layout/' . $layout;
}

// Redirection vers une autre page
function redirect($url) {
    header("Location: $url");
    exit();
}

// Vérification si l'utilisateur est connecté
function check_auth() {
    global $session_services;
    
    $session_services['start_session']();
    
    if (!$session_services['is_logged_in']()) {
        redirect('?page=login');
    }
    
    return $session_services['get_current_user']();
}

// Vérification si l'utilisateur a le profil requis
function check_profile($required_profiles) {
    $user = check_auth();
    
    // Si un seul profil est passé (string), le convertir en tableau
    if (!is_array($required_profiles)) {
        $required_profiles = [$required_profiles];
    }
    
    // Vérifier si le profil de l'utilisateur est dans la liste des profils autorisés
    if (!in_array($user['profile'], $required_profiles)) {
        redirect('?page=forbidden');
    }
    
    return $user;
}

// Téléchargement d'un fichier image
function upload_image($file, $directory = 'uploads') {
    // Vérifier si le fichier est valide
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        return false;
    }
    
    // Chemin relatif pour stocker dans la base de données
    $db_upload_dir = 'assets/images/uploads/' . $directory;
    
    // Chemin absolu pour la création du dossier et le déplacement du fichier
    $absolute_upload_dir = __DIR__ . '/../../public/' . $db_upload_dir;
    
    // Créer le dossier s'il n'existe pas
    if (!is_dir($absolute_upload_dir)) {
        if (!mkdir($absolute_upload_dir, 0755, true)) {
            return false;
        }
    }
    
    // Nom de fichier sécurisé
    $filename = uniqid() . '_' . preg_replace('/[^a-zA-Z0-9\-\_\.]/', '_', basename($file['name']));
    $absolute_target_path = $absolute_upload_dir . '/' . $filename;
    
    // Déplacer le fichier
    if (move_uploaded_file($file['tmp_name'], $absolute_target_path)) {
        // Retourner le chemin relatif pour l'URL et la base de données
        return $db_upload_dir . '/' . $filename;
    }
    
    return false;
}

function ensure_upload_directory($directory = '') {
    $full_path = __DIR__ . '/../../' . Enums\UPLOAD_PATH . $directory;
    
    if (!file_exists($full_path)) {
        return mkdir($full_path, 0755, true);
    }
    
    return is_dir($full_path) && is_writable($full_path);
}
// Fonction de débogage pour les chemins d'images
function debug_image_path($file_path) {
    // Affichez des informations de débogage sur les chemins d'images
    $debug_info = [
        'chemin_reçu' => $file_path,
        'existe' => file_exists(__DIR__ . '/../../public/' . $file_path),
        'chemin_absolu' => __DIR__ . '/../../public/' . $file_path,
        'est_lisible' => is_readable(__DIR__ . '/../../public/' . $file_path),
    ];
    
    // Affichage formaté pour le débogage
    echo '<pre>';
    print_r($debug_info);
    echo '</pre>';
}

/**
 * Génère un mot de passe aléatoire
 * 
 * @param int $length Longueur du mot de passe
 * @return string Mot de passe généré
 */
function generate_random_password($length = 8) {
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()';
    $password = '';
    
    for ($i = 0; $i < $length; $i++) {
        $password .= $chars[rand(0, strlen($chars) - 1)];
    }
    
    return $password;
}