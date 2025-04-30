<?php

namespace App\Controllers;

require_once __DIR__ . '/controller.php';
require_once __DIR__ . '/../models/model.php';
require_once __DIR__ . '/../services/validator.service.php';
require_once __DIR__ . '/../services/session.service.php';
require_once __DIR__ . '/../services/file.service.php';
require_once __DIR__ . '/../translate/fr/error.fr.php';
require_once __DIR__ . '/../translate/fr/message.fr.php';
require_once __DIR__ . '/../enums/profile.enum.php';
require_once __DIR__ . '/../enums/status.enum.php';
require_once __DIR__ . '/../enums/messages.enum.php';

use App\Models;
use App\Services;
use App\Translate\fr;
use App\Enums;
use App\Enums\Status;
use App\Enums\Messages;
use App\Enums\Profile;

/**
 * Count total active learners across all promotions
 * @param array $promotions List of all promotions
 * @return int Number of active learners
 */
function count_active_learners($promotions) {
    $count = 0;
    foreach ($promotions as $promotion) {
        if ($promotion['status'] === 'active' && isset($promotion['apprenants'])) {
            $count += count($promotion['apprenants']);
        }
    }
    return $count;
}

// Affichage de la liste des promotions
// Ajoutez/modifiez cette fonction dans votre fichier app/controllers/promotion.controller.php

// Ajoutez/modifiez cette fonction dans votre fichier app/controllers/promotion.controller.php

function list_promotions() {
    global $model, $session_services;
    
    // Vérification si l'utilisateur est connecté
    $user = check_auth();
    
    // Récupérer les paramètres
    $search = isset($_GET['search']) ? $_GET['search'] : '';
    $status_filter = isset($_GET['status_filter']) ? $_GET['status_filter'] : '';
    $ref_filter = isset($_GET['ref_filter']) ? $_GET['ref_filter'] : '';
    $current_page = isset($_GET['page_num']) ? (int)$_GET['page_num'] : 1;
    $items_per_page = 5;
    
    // Récupérer toutes les promotions
    $promotions = $model['get_all_promotions']();
    
    // Récupérer tous les référentiels pour les filtres
    $all_referentiels = $model['get_all_referentiels']();
    $referentiels_map = array();
    foreach ($all_referentiels as $ref) {
        $referentiels_map[$ref['id']] = $ref['name'];
    }
    
    // Filtrer les promotions si un terme de recherche est présent
    if (!empty($search)) {
        $filtered_promotions = array();
        foreach ($promotions as $promotion) {
            if (stripos($promotion['name'], $search) !== false) {
                $filtered_promotions[] = $promotion;
            }
        }
        $promotions = $filtered_promotions;
    }
    
    // Filtrer par statut si nécessaire
    if ($status_filter !== '') {
        $filtered_promotions = array();
        foreach ($promotions as $promotion) {
            if ($promotion['status'] === $status_filter) {
                $filtered_promotions[] = $promotion;
            }
        }
        $promotions = $filtered_promotions;
    }
    
    // Filtrer par référentiel si nécessaire
    if ($ref_filter !== '') {
        $filtered_promotions = array();
        foreach ($promotions as $promotion) {
            if (isset($promotion['referentiels']) && 
                is_array($promotion['referentiels']) && 
                in_array($ref_filter, $promotion['referentiels'])) {
                $filtered_promotions[] = $promotion;
            }
        }
        $promotions = $filtered_promotions;
    }

    // Trier toutes les promotions
    usort($promotions, function($a, $b) {
        // Si l'une est active et l'autre ne l'est pas
        if ($a['status'] === 'active' && $b['status'] !== 'active') {
            return -1; // a vient avant b
        }
        if ($a['status'] !== 'active' && $b['status'] === 'active') {
            return 1;  // b vient avant a
        }
        
        // Si même statut, trier par date
        $date_a = strtotime($a['date_debut']);
        $date_b = strtotime($b['date_debut']);
        if ($date_a === $date_b) {
            return $b['id'] - $a['id'];
        }
        return $date_b - $date_a;
    });

    // Pour éviter de paginer la promotion active
    $active_promotion = null;
    $other_promotions = array();
    
    foreach ($promotions as $promotion) {
        if ($promotion['status'] === 'active') {
            $active_promotion = $promotion;
        } else {
            $other_promotions[] = $promotion;
        }
    }
    
    // Calculer la pagination
    $total_items = count($other_promotions);
    $total_pages = max(1, ceil($total_items / $items_per_page));
    $current_page = max(1, min($current_page, $total_pages));
    
    // Calculer l'offset pour la pagination
    $offset = ($current_page - 1) * $items_per_page;
    $paginated_promotions = array_slice($other_promotions, $offset, $items_per_page);
    
    // Reconstruire le tableau des promotions avec la promotion active en premier
    $display_promotions = array();
    if ($active_promotion) {
        $display_promotions[] = $active_promotion;
    }
    foreach ($paginated_promotions as $promotion) {
        $display_promotions[] = $promotion;
    }

    // Compter les promotions actives pour les stats
    $active_promotions_count = 0;
    foreach ($promotions as $p) {
        if ($p['status'] === 'active') {
            $active_promotions_count++;
        }
    }

    // Compter les référentiels de la promotion active
    $active_promotion_referentials = 0;
    if ($active_promotion && isset($active_promotion['referentiels'])) {
        $active_promotion_referentials = count($active_promotion['referentiels']);
    }

    // Compter le nombre total d'apprenants actifs
    $active_learners = 0;
    foreach ($promotions as $promotion) {
        if ($promotion['status'] === 'active' && isset($promotion['apprenants'])) {
            $active_learners += count($promotion['apprenants']);
        }
    }

    render('admin.layout.php', 'promotion/list.html.php', array(
        'active_menu' => 'promotions',
        'promotions' => $display_promotions,
        'other_promotions' => $paginated_promotions,
        'current_page' => $current_page,
        'total_pages' => $total_pages,
        'view_mode' => isset($_GET['view']) ? $_GET['view'] : 'grid',
        'search' => $search,
        'status_filter' => $status_filter,
        'ref_filter' => $ref_filter,
        'active_promotion' => $active_promotion,
        'referentiels' => $all_referentiels,          // Ajout des référentiels
        'referentiels_map' => $referentiels_map,      // Ajout de la map ID => nom
        'stats' => array(
            'active_learners' => $active_learners,
            'total_referentials' => $active_promotion_referentials,
            'active_promotions' => $active_promotions_count,
            'total_promotions' => count($promotions)
        )
    ));
}

// Affichage du formulaire d'ajout d'une promotion
function add_promotion_form() {
    global $model;
    
    // Vérification des droits d'accès
    check_profile(Enums\ADMIN);
    
    // Initialize variables
    $data = [
        'active_menu' => 'promotions',
        'name' => '',
        'date_debut' => '',
        'date_fin' => '',
        'errors' => [],
        'error_messages' => [
            'form' => [
                'required' => 'Ce champ est requis',
                'invalid_date' => 'Format de date invalide (YYYY-MM-DD)',
                'date_order' => 'La date de fin doit être postérieure à la date de début'
            ]
        ],
        'referentiels' => $model['get_all_referentiels']()
    ];
    
    render('admin.layout.php', 'promotion/add.html.php', $data);
}

// Traitement de l'ajout d'une promotion
function add_promotion_process() {
    global $model, $validator_services, $session_services;
    
    // Vérification des droits d'accès
    check_profile(Enums\ADMIN);
    
    // Initialize errors array
    $errors = [];
    
    // Get form data
    $name = trim($_POST['name'] ?? '');
    $date_debut = trim($_POST['date_debut'] ?? '');
    $date_fin = trim($_POST['date_fin'] ?? '');
    $referentiels = isset($_POST['referentiels']) && is_array($_POST['referentiels']) ? $_POST['referentiels'] : [];
    
    // Validate name (required)
    if (empty($name)) {
        $errors['name'] = 'Le nom de la promotion est requis';
    } elseif ($model['promotion_name_exists']($name)) {
        $errors['name'] = 'Ce nom de promotion existe déjà';
    }
    
    // Variables pour stocker les dates en format YYYY-MM-DD pour la base de données
    $date_debut_db = '';
    $date_fin_db = '';
    
    // Validate date_debut (required + format)
    if (empty($date_debut)) {
        $errors['date_debut'] = 'La date de début est requise';
    } else {
        // Vérifier format JJ-MM-AAAA
        if (!preg_match('/^(\d{2})-(\d{2})-(\d{4})$/', $date_debut, $matches)) {
            $errors['date_debut'] = 'Format de date invalide. Utilisez JJ-MM-AAAA';
        } else {
            $day = $matches[1];
            $month = $matches[2];
            $year = $matches[3];
            
            // Vérifier si la date existe
            if (!checkdate($month, $day, $year)) {
                $errors['date_debut'] = 'Date invalide';
            } else {
                // Convertir au format YYYY-MM-DD pour la base de données
                $date_debut_db = "$year-$month-$day";
            }
        }
    }
    
    // Validate date_fin (required + format)
    if (empty($date_fin)) {
        $errors['date_fin'] = 'La date de fin est requise';
    } else {
        // Vérifier format JJ-MM-AAAA
        if (!preg_match('/^(\d{2})-(\d{2})-(\d{4})$/', $date_fin, $matches)) {
            $errors['date_fin'] = 'Format de date invalide. Utilisez JJ-MM-AAAA';
        } else {
            $day = $matches[1];
            $month = $matches[2];
            $year = $matches[3];
            
            // Vérifier si la date existe
            if (!checkdate($month, $day, $year)) {
                $errors['date_fin'] = 'Date invalide';
            } else {
                // Convertir au format YYYY-MM-DD pour la base de données
                $date_fin_db = "$year-$month-$day";
            }
        }
    }
    
    // Compare dates if both are valid
    if (!empty($date_debut_db) && !empty($date_fin_db)) {
        if (strtotime($date_fin_db) <= strtotime($date_debut_db)) {
            $errors['date_fin'] = 'La date de fin doit être postérieure à la date de début';
        }
    }
    
    // Gestion de l'image de promotion
    $image_path = 'assets/images/uploads/promotions/cloud.jpg'; // Image par défaut
    
    if (!isset($_FILES['image']) || $_FILES['image']['error'] === UPLOAD_ERR_NO_FILE) {
        $errors['image'] = 'L\'image de la promotion est requise';
    } else if ($_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $allowed = ['jpg', 'jpeg', 'png'];
        $max_size = 2 * 1024 * 1024; // 2MB en bytes
        
        $filename = $_FILES['image']['name'];
        $filetype = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $filesize = $_FILES['image']['size'];
        
        // Vérification du format
        if (!in_array($filetype, $allowed)) {
            $errors['image'] = 'Format invalide. Formats acceptés : JPG, PNG';
        }
        
        // Vérification de la taille
        if ($filesize > $max_size) {
            $errors['image'] = 'L\'image ne doit pas dépasser 2MB';
        }
        
        // Upload si pas d'erreur
        if (!isset($errors['image'])) {
            // Utiliser la fonction upload_image du contrôleur
            $image_path = upload_image($_FILES['image'], 'promotions');
            if (!$image_path) {
                $errors['image'] = 'Erreur lors de l\'upload de l\'image';
            }
        }
    } else {
        $errors['image'] = 'Erreur lors du téléchargement de l\'image';
    }
    
    // Validate referentiels selection
    if (empty($referentiels)) {
        $errors['referentiels'] = 'Veuillez sélectionner au moins un référentiel';
    }
    
    // If there are errors, return to form with error messages
    if (!empty($errors)) {
        return render('admin.layout.php', 'promotion/add.html.php', [
            'active_menu' => 'promotions',
            'errors' => $errors,
            'name' => $name,
            'date_debut' => $date_debut,
            'date_fin' => $date_fin,
            'selected_referentiels' => $referentiels,
            'referentiels' => $model['get_all_referentiels']()
        ]);
    }
    
    // Créer la promotion avec les dates converties au format YYYY-MM-DD
    $promotion_data = [
        'name' => $name,
        'date_debut' => $date_debut_db,
        'date_fin' => $date_fin_db,
        'image' => $image_path,
        'status' => 'inactive',
        'apprenants' => []
    ];
    
    // Créer la promotion
    $result = $model['create_promotion']($promotion_data);
    
    if (!$result) {
        $session_services['set_flash_message']('danger', 'Erreur lors de la création de la promotion');
        return render('admin.layout.php', 'promotion/add.html.php', [
            'active_menu' => 'promotions',
            'errors' => ['general' => 'Erreur lors de la création de la promotion'],
            'name' => $name,
            'date_debut' => $date_debut,
            'date_fin' => $date_fin,
            'selected_referentiels' => $referentiels,
            'referentiels' => $model['get_all_referentiels']()
        ]);
    }
    
    // Si on a des référentiels, les associer à la promotion
    if (!empty($referentiels)) {
        $model['assign_referentiels_to_promotion']($result, $referentiels);
    }
    
    // Redirection vers la liste des promotions avec message de succès
    $session_services['set_flash_message']('success', 'Promotion créée avec succès');
    redirect('?page=promotions');
}

// Modification du statut d'une promotion (activation/désactivation)
function toggle_promotion_status() {
    global $model, $session_services;
    
    check_auth();
    $view_mode = $_POST['view'] ?? 'grid';
    
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        redirect('?page=promotions&view=' . urlencode($view_mode));
        return;
    }
    
    $promotion_id = filter_input(INPUT_POST, 'promotion_id', FILTER_VALIDATE_INT);
    if (!$promotion_id) {
        $session_services['set_flash_message']('error', Messages::PROMOTION_ERROR->value);
        redirect('?page=promotions&view=' . urlencode($view_mode));
        return;
    }

    // Récupérer toutes les promotions
    $promotions = $model['get_all_promotions']();
    
    // Trouver la promotion à modifier
    $current_promotion = null;
    foreach ($promotions as $promotion) {
        if ((int)$promotion['id'] === $promotion_id) {
            $current_promotion = $promotion;
            break;
        }
    }

    if (!$current_promotion) {
        $session_services['set_flash_message']('error', Messages::PROMOTION_ERROR->value);
        redirect('?page=promotions&view=' . urlencode($view_mode));
        return;
    }

    // Si la promotion est active, empêcher sa désactivation directe
    if ($current_promotion['status'] === 'active') {
        $session_services['set_flash_message']('error', 'Impossible de désactiver directement une promotion active. Activez une autre promotion pour désactiver celle-ci.');
        redirect('?page=promotions&view=' . urlencode($view_mode));
        return;
    }

    // Si on tente d'activer une promotion, désactiver l'actuelle promotion active
    if ($current_promotion['status'] === 'inactive') {
        // Désactiver l'ancienne promotion active
        $active_promotion = null;
        foreach ($promotions as $promotion) {
            if ($promotion['status'] === 'active') {
                $active_promotion = $promotion;
                break;
            }
        }

        if ($active_promotion) {
            $model['update_promotion_status']($active_promotion['id'], 'inactive');
        }

        // Activer la nouvelle promotion
        $result = $model['update_promotion_status']($promotion_id, 'active');
        
        if ($result) {
            $session_services['set_flash_message']('success', Messages::PROMOTION_ACTIVATED->value);
        } else {
            $session_services['set_flash_message']('error', Messages::PROMOTION_ERROR->value);
        }
    }
    
    redirect('?page=promotions&view=' . urlencode($view_mode));
}

// Ajouter cette fonction helper
function get_view_param() {
    $view = $_GET['view'] ?? '';
    return $view ? '&view=' . htmlspecialchars($view) : '';
}

// Ajout d'une promotion
function add_promotion() {
    global $model, $session_services, $validator_services, $file_services;
    
    // Vérification de l'authentification
    $user = check_auth();
    
    // Vérification de la méthode POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        $session_services['set_flash_message']('error', Messages::INVALID_REQUEST->value);
        redirect('?page=promotions');
        return;
    }
    
    // Validation des données
    $validation = $validator_services['validate_promotion']($_POST, $_FILES);
    
    if (!$validation['valid']) {
        $session_services['set_flash_message']('error', $validation['errors'][0]);
        redirect('?page=promotions');
        return;
    }
    
    // Traitement de l'image avec le service
    $image_path = $file_services['handle_promotion_image']($_FILES['image']);
    if (!$image_path) {
        $session_services['set_flash_message']('error', Messages::IMAGE_UPLOAD_ERROR->value);
        redirect('?page=promotions');
        return;
    }
    
    // Préparation des données
    $promotion_data = [
        'name' => htmlspecialchars($_POST['name']),
        'date_debut' => $_POST['date_debut'],
        'date_fin' => $_POST['date_fin'],
        'image' => $image_path,
        'status' => 'inactive',
        'apprenants' => []
    ];
    
    // Création de la promotion
    $result = $model['create_promotion']($promotion_data);
    
    if (!$result) {
        $session_services['set_flash_message']('error', Messages::PROMOTION_CREATE_ERROR->value);
        redirect('?page=promotions');
        return;
    }

    $session_services['set_flash_message']('success', Messages::PROMOTION_CREATED->value);
    redirect('?page=promotions');
}

// Recherche des référentiels
function search_referentiels() {
    global $model;
    
    // Vérification si l'utilisateur est connecté
    check_auth();
    
    $query = $_GET['q'] ?? '';
    $referentiels = $model['search_referentiels']($query);
    
    // Retourner les résultats en JSON
    header('Content-Type: application/json');
    echo json_encode(array_values($referentiels));
    exit;
}

// Fonctions de débogage pour les chemins d'images (à retirer en production)
