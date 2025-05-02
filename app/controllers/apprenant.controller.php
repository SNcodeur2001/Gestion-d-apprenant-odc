<?php

namespace App\Controllers;

require_once __DIR__ . '/controller.php';
require_once __DIR__ . '/../models/model.php';
require_once __DIR__ . '/../services/validator.service.php';
require_once __DIR__ . '/../services/session.service.php';
require_once __DIR__ . '/../services/mail.service.php';
require_once __DIR__ . '/../services/apprenant/validator.service.php';
require_once __DIR__ . '/../services/apprenant/manager.service.php';
require_once __DIR__ . '/../services/apprenant/export.service.php';
require_once __DIR__ . '/../services/apprenant/import.service.php';
require_once __DIR__ . '/../translate/fr/error.fr.php';
require_once __DIR__ . '/../translate/fr/message.fr.php';
require_once __DIR__ . '/../enums/profile.enum.php';
require_once __DIR__ . '/../enums/status.enum.php';

use App\Models;
use App\Services;
use App\Enums;

/**
 * Affiche la liste des apprenants
 */
function list_apprenants() {
    global $model, $session_services, $apprenant_manager_services;
    
    // Vérification de l'authentification
    $user = check_auth();
    
    // Récupération des paramètres de filtrage et de pagination
    $search = isset($_GET['search']) ? $_GET['search'] : '';
    $classe_filter = isset($_GET['classe_filter']) ? $_GET['classe_filter'] : '';
    $status_filter = isset($_GET['status_filter']) ? $_GET['status_filter'] : '';
    $current_page = isset($_GET['page_num']) ? (int)$_GET['page_num'] : 1;
    $items_per_page = 10; // Nombre d'apprenants par page
    
    // Tab actif (apprenants ou liste d'attente)
    $active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'apprenants';
    
    // Récupération de la promotion active
    $current_promotion = $model['get_current_promotion']();
    
    // Vérifier si une promotion est active
    if (!$current_promotion) {
        $session_services['set_flash_message']('warning', 'Aucune promotion active. Veuillez d\'abord activer une promotion.');
        redirect('?page=promotions');
        return;
    }
    
    // Récupérer tous les apprenants de cette promotion
    $all_apprenants = $model['get_all_apprenants']();
    
    // Filtrer les apprenants de la promotion active
    $promotion_apprenants = array_filter($all_apprenants, function($apprenant) use ($current_promotion) {
        return $apprenant['promotion_id'] == $current_promotion['id'];
    });
    
    // Filtrer les apprenants selon les critères
    $filtered_apprenants = $apprenant_manager_services['filter_apprenants'](
        $promotion_apprenants, 
        $search, 
        $classe_filter, 
        $status_filter
    );
    
    // Récupérer uniquement les référentiels de la promotion active pour le filtre
    $promotion_referentiels = $model['get_referentiels_by_promotion']($current_promotion['id']);
    $referentiels_map = array();
    foreach ($promotion_referentiels as $ref) {
        $referentiels_map[$ref['id']] = $ref['name'];
    }
    
    // Pagination
    $total_apprenants = count($filtered_apprenants);
    $total_pages = max(1, ceil($total_apprenants / $items_per_page));
    $current_page = max(1, min($current_page, $total_pages));
    $offset = ($current_page - 1) * $items_per_page;
    
    // Récupérer les apprenants pour la page courante
    $paginated_apprenants = array_slice($filtered_apprenants, $offset, $items_per_page);
    
    // Récupérer la liste d'attente
    $waiting_list = $session_services['get_session']('waiting_list', []);
    
    // Rendre la vue avec les données
    render('admin.layout.php', 'apprenant/list.html.php', [
        'active_menu' => 'apprenants',
        'apprenants' => $paginated_apprenants,
        'current_page' => $current_page,
        'total_pages' => $total_pages,
        'total_apprenants' => $total_apprenants,
        'search' => $search,
        'classe_filter' => $classe_filter,
        'status_filter' => $status_filter,
        'current_promotion' => $current_promotion,
        'referentiels' => $promotion_referentiels,
        'referentiels_map' => $referentiels_map,
        'referentiel_name' => isset($referentiels_map[$classe_filter]) ? $referentiels_map[$classe_filter] : '',
        'waiting_list' => $waiting_list,
        'active_tab' => $active_tab
    ]);
}

/**
 * Affiche le formulaire d'ajout d'un apprenant
 */
function add_apprenant_form() {
    global $model, $session_services;
    
    // Vérification des droits d'accès
    $user = check_profile([Enums\ADMIN, Enums\ATTACHE]);
    
    // Récupérer la promotion active
    $current_promotion = $model['get_current_promotion']();
    
    // Vérifier si une promotion est active
    if (!$current_promotion) {
        $session_services['set_flash_message']('warning', 'Aucune promotion active. Veuillez d\'abord activer une promotion.');
        redirect('?page=promotions');
        return;
    }
    
    // Récupérer les référentiels de la promotion active
    $referentiels = $model['get_referentiels_by_promotion']($current_promotion['id']);
    
    // Rendre la vue avec les données
    render('admin.layout.php', 'apprenant/add.html.php', [
        'active_menu' => 'apprenants',
        'current_promotion' => $current_promotion,
        'referentiels' => $referentiels
    ]);
}

/**
 * Traite le formulaire d'ajout d'un apprenant
 */
function add_apprenant_process() {
    global $model, $validator_services, $session_services, $mail_services, $apprenant_validator_services, $apprenant_manager_services;
    
    // Vérification des droits d'accès
    $user = check_profile([Enums\ADMIN, Enums\ATTACHE]);
    
    // Récupérer la promotion active
    $current_promotion = $model['get_current_promotion']();
    
    // Vérifier si une promotion est active
    if (!$current_promotion) {
        $session_services['set_flash_message']('warning', 'Aucune promotion active. Veuillez d\'abord activer une promotion.');
        redirect('?page=promotions');
        return;
    }
    
    // Récupérer et valider les données du formulaire
    $data = [
        'nom' => trim($_POST['nom'] ?? ''),
        'prenom' => trim($_POST['prenom'] ?? ''),
        'email' => trim($_POST['email'] ?? ''),
        'telephone' => trim($_POST['telephone'] ?? ''),
        'adresse' => trim($_POST['adresse'] ?? ''),
        'date_naissance' => trim($_POST['date_naissance'] ?? ''),
        'lieu_naissance' => trim($_POST['lieu_naissance'] ?? ''),
        'referentiel_id' => $_POST['referentiel_id'] ?? '',
        'promotion_id' => $current_promotion['id']
    ];
    
    // Validation des données
    $errors = $apprenant_validator_services['validate_apprenant_data']($data, $validator_services, $model);
    
    // Gestion de la photo
    $photo_path = 'assets/images/apprenants/default.jpg'; // Image par défaut
    
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $photo_errors = $apprenant_validator_services['validate_apprenant_photo']($_FILES['photo']);
        
        if (!empty($photo_errors)) {
            $errors = array_merge($errors, $photo_errors);
        } else {
            $photo_path = upload_image($_FILES['photo'], 'apprenants');
            if (!$photo_path) {
                $errors['photo'] = 'Erreur lors de l\'upload de l\'image';
            }
        }
    }
    
    // S'il y a des erreurs, retourner au formulaire
    if (!empty($errors)) {
        // Récupérer les référentiels de la promotion active
        $referentiels = $model['get_referentiels_by_promotion']($current_promotion['id']);
        
        render('admin.layout.php', 'apprenant/add.html.php', [
            'active_menu' => 'apprenants',
            'current_promotion' => $current_promotion,
            'referentiels' => $referentiels,
            'errors' => $errors,
            'nom' => $data['nom'],
            'prenom' => $data['prenom'],
            'email' => $data['email'],
            'telephone' => $data['telephone'],
            'adresse' => $data['adresse'],
            'date_naissance' => $data['date_naissance'],
            'lieu_naissance' => $data['lieu_naissance'],
            'referentiel_id' => $data['referentiel_id']
        ]);
        return;
    }
    
    // Génération du matricule
    $matricule = $model['generate_matricule']();
    
    // Préparation des données de l'apprenant
    $apprenant_data = $apprenant_manager_services['prepare_apprenant_data']($data, $photo_path, $matricule);
    
    // Ajouter l'apprenant
    $result = $model['create_apprenant']($apprenant_data);
    
    if (!$result) {
        $session_services['set_flash_message']('danger', 'Erreur lors de l\'ajout de l\'apprenant');
        redirect('?page=add-apprenant');
        return;
    }
    
    // Récupérer les informations du référentiel pour l'email
    $referentiel = $model['get_referentiel_by_id']($data['referentiel_id']);
    
    // Envoyer l'email de bienvenue
    $email_sent = $mail_services['send_welcome_email']($apprenant_data, $current_promotion, $referentiel);
    
    // Message de succès avec indication de l'envoi d'email
    if ($email_sent) {
        $session_services['set_flash_message']('success', 'Apprenant ajouté avec succès. Un email de bienvenue a été envoyé.');
    } else {
        $session_services['set_flash_message']('success', 'Apprenant ajouté avec succès, mais l\'envoi de l\'email a échoué.');
    }
    
    redirect('?page=apprenants');
}

/**
 * Renvoie l'email de bienvenue à un apprenant
 */
function resend_welcome_email() {
    global $model, $session_services, $mail_services;
    
    // Vérification des droits d'accès
    $user = check_profile([Enums\ADMIN, Enums\ATTACHE]);
    
    // Récupération de l'ID de l'apprenant
    $apprenant_id = $_GET['id'] ?? '';
    
    if (empty($apprenant_id)) {
        $session_services['set_flash_message']('warning', 'Apprenant non spécifié');
        redirect('?page=apprenants');
        return;
    }
    
    // Récupération des données de l'apprenant
    $apprenant = $model['get_apprenant_by_id']($apprenant_id);
    
    if (!$apprenant) {
        $session_services['set_flash_message']('warning', 'Apprenant non trouvé');
        redirect('?page=apprenants');
        return;
    }
    
    // Récupération du référentiel
    $referentiel = $model['get_referentiel_by_id']($apprenant['referentiel_id']);
    
    // Récupération de la promotion
    $promotion = $model['get_promotion_by_id']($apprenant['promotion_id']);
    
    // Envoyer l'email de bienvenue
    $email_sent = $mail_services['send_welcome_email']($apprenant, $promotion, $referentiel);
    
    // Message de succès avec indication de l'envoi d'email
    if ($email_sent) {
        $session_services['set_flash_message']('success', 'Email de bienvenue envoyé avec succès.');
    } else {
        $session_services['set_flash_message']('danger', 'L\'envoi de l\'email a échoué.');
    }
    
    redirect('?page=apprenants');
}

/**
 * Télécharge la liste des apprenants dans différents formats (CSV, Excel, PDF)
 */
function download_apprenants_list() {
    global $model, $session_services, $apprenant_export_services;
    
    // Vérification de l'authentification
    $user = check_auth();
    
    // Récupération du format demandé
    $format = $_GET['format'] ?? 'csv';
    
    // Récupération de la promotion active
    $current_promotion = $model['get_current_promotion']();
    
    // Vérifier si une promotion est active
    if (!$current_promotion) {
        $session_services['set_flash_message']('warning', 'Aucune promotion active.');
        redirect('?page=promotions');
        return;
    }
    
    // Récupérer tous les apprenants de cette promotion
    $all_apprenants = $model['get_all_apprenants']();
    
    // Filtrer les apprenants de la promotion active
    $promotion_apprenants = array_filter($all_apprenants, function($apprenant) use ($current_promotion) {
        return $apprenant['promotion_id'] == $current_promotion['id'];
    });
    
    // Récupérer tous les référentiels pour la conversion des IDs
    $all_referentiels = $model['get_all_referentiels']();
    $referentiels_map = array();
    foreach ($all_referentiels as $ref) {
        $referentiels_map[$ref['id']] = $ref['name'];
    }
    // Traitement selon le format demandé
    switch ($format) {
        case 'excel':
            $apprenant_export_services['export_apprenants_excel']($promotion_apprenants, $referentiels_map, $current_promotion);
            break;
        case 'pdf':
            $apprenant_export_services['export_apprenants_pdf']($promotion_apprenants, $referentiels_map, $current_promotion);
            break;
        case 'csv':
        default:
            $csv_content = $apprenant_export_services['export_apprenants_csv']($promotion_apprenants, $referentiels_map, $current_promotion);
            
            // Configuration des headers pour le téléchargement
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename="apprenants_' . $current_promotion['id'] . '.csv"');
            
            // Output du contenu CSV
            echo $csv_content;
            exit;
    }
}

/**
 * Affiche les détails d'un apprenant
 */
function view_apprenant() {
    global $model, $session_services;
    
    // Vérification de l'authentification
    $user = check_auth();
    
    // Récupération de l'ID de l'apprenant
    $apprenant_id = $_GET['id'] ?? '';
    
    if (empty($apprenant_id)) {
        $session_services['set_flash_message']('warning', 'Apprenant non spécifié');
        redirect('?page=apprenants');
        return;
    }
    
    // Récupération des données de l'apprenant
    $apprenant = $model['get_apprenant_by_id']($apprenant_id);
    
    if (!$apprenant) {
        $session_services['set_flash_message']('warning', 'Apprenant non trouvé');
        redirect('?page=apprenants');
        return;
    }
    
    // Récupération du référentiel
    $referentiel = $model['get_referentiel_by_id']($apprenant['referentiel_id']);
    
    // Récupération de la promotion
    $promotion = $model['get_promotion_by_id']($apprenant['promotion_id']);
    
    // Rendre la vue avec les données
    render('admin.layout.php', 'apprenant/view.html.php', [
        'active_menu' => 'apprenants',
        'apprenant' => $apprenant,
        'referentiel' => $referentiel,
        'promotion' => $promotion
    ]);
}

/**
 * Télécharge un template Excel pour l'import des apprenants
 */
function download_apprenant_template() {
    global $apprenant_export_services;
    
    $apprenant_export_services['generate_apprenant_template']();
}

/**
 * Affiche le formulaire d'import des apprenants
 */
function import_apprenants() {
    global $model, $session_services;
    
    // Vérification de l'authentification
    $user = check_auth();
    
    // Récupérer la promotion active
    $current_promotion = $model['get_current_promotion']();
    
    // Vérifier si une promotion est active
    if (!$current_promotion) {
        $session_services['set_flash_message']('warning', 'Aucune promotion active. L\'importation d\'apprenants n\'est possible que pour une promotion active.');
        redirect('?page=promotions');
        return;
    }
    
    // Récupérer uniquement les référentiels de la promotion active
    $referentiels = $model['get_referentiels_by_promotion']($current_promotion['id']);
    
    // Si aucun référentiel n'est associé à la promotion
    if (empty($referentiels)) {
        $session_services['set_flash_message']('warning', 'Aucun référentiel n\'est associé à la promotion active. Veuillez d\'abord ajouter des référentiels.');
        redirect('?page=referentiels');
        return;
    }
    
    // Afficher la vue
    render('admin.layout.php', 'apprenant/import.html.php', [
        'active_menu' => 'apprenants',
        'referentiels' => $referentiels,
        'promotion_active' => $current_promotion
    ]);
}

/**
 * Traite l'import du fichier Excel des apprenants
 */
function process_import_apprenants() {
    global $model, $session_services, $apprenant_import_services;
    
    // Vérification de l'authentification
    $user = check_auth();
    
    // Récupérer la promotion active
    $current_promotion = $model['get_current_promotion']();
    
    // Vérifier si une promotion est active
    if (!$current_promotion) {
        $session_services['set_flash_message']('warning', 'Aucune promotion active. L\'importation d\'apprenants n\'est possible que pour une promotion active.');
        redirect('?page=promotions');
        return;
    }
    
    // Récupérer les référentiels de la promotion active
    $promotion_referentiels = $model['get_referentiels_by_promotion']($current_promotion['id']);
    $promotion_referentiel_ids = array_column($promotion_referentiels, 'id');
    
    // Récupérer le référentiel par défaut
    $default_referentiel_id = $_POST['referentiel_id'] ?? '';
    if (empty($default_referentiel_id)) {
        $session_services['set_flash_message']('error', 'Veuillez sélectionner un référentiel par défaut');
        redirect('?page=import-apprenants');
        return;
    }
    
    // Traiter le fichier d'import
    $result = $apprenant_import_services['process_import_file'](
        $_FILES['excel_file'] ?? null,
        $default_referentiel_id,
        $promotion_referentiel_ids,
        $model
    );
    
    if (!$result['success']) {
        $session_services['set_flash_message']('error', $result['message']);
        redirect('?page=import-apprenants');
        return;
    }
    
    // Sauvegarder la liste d'attente dans la session
    if (!empty($result['waiting_list'])) {
        $session_services['set_session']('waiting_list', $result['waiting_list']);
    }
    
    // Afficher un message de succès ou d'erreur
    if ($result['success_count'] > 0) {
        $message = "{$result['success_count']} apprenants ont été importés avec succès.";
        if ($result['error_count'] > 0) {
            $message .= " {$result['error_count']} apprenants n'ont pas pu être importés et ont été placés en liste d'attente.";
            $session_services['set_flash_message']('warning', $message);
            redirect('?page=apprenants&tab=waiting');
            return;
        } else {
            $session_services['set_flash_message']('success', $message);
        }
    } else {
        if ($result['error_count'] > 0) {
            $message = "Aucun apprenant n'a pu être importé. {$result['error_count']} apprenants ont été placés en liste d'attente.";
            $session_services['set_flash_message']('warning', $message);
            redirect('?page=apprenants&tab=waiting');
            return;
        } else {
            $session_services['set_flash_message']('error', "Aucun apprenant n'a pu être importé. Vérifiez le format du fichier.");
        }
    }
    
    redirect('?page=apprenants');
}

/**
 * Affiche le formulaire d'édition d'un apprenant
 */
function edit_apprenant_form() {
    global $model, $session_services;
    
    // Vérification des droits d'accès
    $user = check_profile([Enums\ADMIN, Enums\ATTACHE]);
    
    // Récupération de l'ID de l'apprenant
    $apprenant_id = $_GET['id'] ?? '';
    
    if (empty($apprenant_id)) {
        $session_services['set_flash_message']('warning', 'Apprenant non spécifié');
        redirect('?page=apprenants');
        return;
    }
    
    // Récupération des données de l'apprenant
    $apprenant = $model['get_apprenant_by_id']($apprenant_id);
    
    if (!$apprenant) {
        $session_services['set_flash_message']('warning', 'Apprenant non trouvé');
        redirect('?page=apprenants');
        return;
    }
    
    // Récupérer la promotion active
    $current_promotion = $model['get_current_promotion']();
    
    // Récupérer les référentiels de la promotion active
    $referentiels = $model['get_referentiels_by_promotion']($current_promotion['id']);
    
    // Rendre la vue avec les données
    render('admin.layout.php', 'apprenant/edit.html.php', [
        'active_menu' => 'apprenants',
        'apprenant' => $apprenant,
        'current_promotion' => $current_promotion,
        'referentiels' => $referentiels
    ]);
}

/**
 * Traite le formulaire d'édition d'un apprenant
 */
function edit_apprenant_process() {
    global $model, $validator_services, $session_services, $apprenant_validator_services, $apprenant_manager_services;
    
    // Vérification des droits d'accès
    $user = check_profile([Enums\ADMIN, Enums\ATTACHE]);
    
    // Récupération de l'ID de l'apprenant
    $apprenant_id = $_POST['id'] ?? '';
    
    if (empty($apprenant_id)) {
        $session_services['set_flash_message']('warning', 'Apprenant non spécifié');
        redirect('?page=apprenants');
        return;
    }
    
    // Récupération des données de l'apprenant
    $apprenant = $model['get_apprenant_by_id']($apprenant_id);
    
    if (!$apprenant) {
        $session_services['set_flash_message']('warning', 'Apprenant non trouvé');
        redirect('?page=apprenants');
        return;
    }
    
    // Récupérer et valider les données du formulaire
    $data = [
        'nom' => trim($_POST['nom'] ?? ''),
        'prenom' => trim($_POST['prenom'] ?? ''),
        'email' => trim($_POST['email'] ?? ''),
        'telephone' => trim($_POST['telephone'] ?? ''),
        'adresse' => trim($_POST['adresse'] ?? ''),
        'date_naissance' => trim($_POST['date_naissance'] ?? ''),
        'lieu_naissance' => trim($_POST['lieu_naissance'] ?? ''),
        'referentiel_id' => $_POST['referentiel_id'] ?? '',
        'statut' => $_POST['statut'] ?? 'actif'
    ];
    
    // Validation des données
    $errors = $apprenant_validator_services['validate_apprenant_data']($data, $validator_services, $model, $apprenant['email']);
    
    // Gestion de la photo
    $photo_path = $apprenant['photo']; // Conserver l'ancienne photo par défaut
    
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $photo_errors = $apprenant_validator_services['validate_apprenant_photo']($_FILES['photo']);
        
        if (!empty($photo_errors)) {
            $errors = array_merge($errors, $photo_errors);
        } else {
            $photo_path = upload_image($_FILES['photo'], 'apprenants');
            if (!$photo_path) {
                $errors['photo'] = 'Erreur lors de l\'upload de l\'image';
            }
        }
    }
    
    // S'il y a des erreurs, retourner au formulaire
    if (!empty($errors)) {
        // Récupérer la promotion active
        $current_promotion = $model['get_current_promotion']();
        
        // Récupérer les référentiels de la promotion active
        $referentiels = $model['get_referentiels_by_promotion']($current_promotion['id']);
        
        render('admin.layout.php', 'apprenant/edit.html.php', [
            'active_menu' => 'apprenants',
            'apprenant' => $apprenant,
            'current_promotion' => $current_promotion,
            'referentiels' => $referentiels,
            'errors' => $errors,
            'nom' => $data['nom'],
            'prenom' => $data['prenom'],
            'email' => $data['email'],
            'telephone' => $data['telephone'],
            'adresse' => $data['adresse'],
            'date_naissance' => $data['date_naissance'],
            'lieu_naissance' => $data['lieu_naissance'],
            'referentiel_id' => $data['referentiel_id'],
            'statut' => $data['statut']
        ]);
        return;
    }
    
    // Préparation des données de l'apprenant pour la mise à jour
    $apprenant_data = [
        'nom' => $data['nom'],
        'prenom' => $data['prenom'],
        'photo' => $photo_path,
        'adresse' => $data['adresse'],
        'telephone' => $data['telephone'],
        'email' => $data['email'],
        'date_naissance' => $data['date_naissance'],
        'lieu_naissance' => $data['lieu_naissance'],
        'referentiel_id' => $data['referentiel_id'],
        'statut' => $data['statut']
    ];
    
    // Mettre à jour l'apprenant
    $result = $model['update_apprenant']($apprenant_id, $apprenant_data);
    
    if (!$result) {
        $session_services['set_flash_message']('danger', 'Erreur lors de la mise à jour de l\'apprenant');
        redirect('?page=edit-apprenant&id=' . $apprenant_id);
        return;
    }
    
    // Message de succès
    $session_services['set_flash_message']('success', 'Apprenant mis à jour avec succès');
    redirect('?page=apprenants');
}

/**
 * Affiche la liste d'attente des apprenants avec erreurs
 */
function waiting_list() {
    global $model, $session_services;
    
    // Vérification de l'authentification
    $user = check_auth();
    
    // Récupérer la liste d'attente de la session
    $waiting_list = $session_services['get_session']('waiting_list', []);
    
    // Si la liste d'attente est vide, rediriger vers la liste des apprenants
    if (empty($waiting_list)) {
        $session_services['set_flash_message']('info', 'La liste d\'attente est vide.');
        redirect('?page=apprenants');
        return;
    }
    
    // Récupérer les référentiels pour afficher les noms
    $referentiels = $model['get_all_referentiels']();
    $referentiels_map = [];
    foreach ($referentiels as $ref) {
        $referentiels_map[$ref['id']] = $ref['name'];
    }
    
    // Rendre la vue
    render('admin.layout.php', 'apprenant/waiting-list.html.php', [
        'active_menu' => 'apprenants',
        'waiting_list' => $waiting_list,
        'referentiels_map' => $referentiels_map
    ]);
}

/**
 * Affiche le formulaire pour corriger un apprenant en liste d'attente
 */
function correct_waiting_apprenant() {
    global $model, $session_services;
    
    // Vérification de l'authentification
    $user = check_auth();
    
    // Récupérer l'index de l'apprenant dans la liste d'attente
    $apprenant_index = isset($_GET['index']) ? (int)$_GET['index'] : null;
    
    // Récupérer la liste d'attente
    $waiting_list = $session_services['get_session']('waiting_list', []);
    
    // Vérifier si l'index est valide
    if ($apprenant_index === null || !isset($waiting_list[$apprenant_index])) {
        $session_services['set_flash_message']('warning', 'Apprenant non trouvé dans la liste d\'attente');
        redirect('?page=apprenants&tab=waiting');
        return;
    }
    
    // Récupérer l'apprenant en attente
    $apprenant = $waiting_list[$apprenant_index];
    
    // Récupérer la promotion active
    $current_promotion = $model['get_current_promotion']();
    
    // Vérifier si une promotion est active
    if (!$current_promotion) {
        $session_services['set_flash_message']('warning', 'Aucune promotion active. Veuillez d\'abord activer une promotion.');
        redirect('?page=promotions');
        return;
    }
    
    // Récupérer les référentiels de la promotion active
    $referentiels = $model['get_referentiels_by_promotion']($current_promotion['id']);
    
    // Stocker l'index dans la session pour le récupérer lors du traitement
    $session_services['set_session']('correcting_apprenant_index', $apprenant_index);
    
    // Rendre la vue avec les données
    render('admin.layout.php', 'apprenant/add.html.php', [
        'active_menu' => 'apprenants',
        'current_promotion' => $current_promotion,
        'referentiels' => $referentiels,
        'errors' => $apprenant['errors'] ?? [],
        'nom' => $apprenant['nom'] ?? '',
        'prenom' => $apprenant['prenom'] ?? '',
        'email' => $apprenant['email'] ?? '',
        'telephone' => $apprenant['telephone'] ?? '',
        'adresse' => $apprenant['adresse'] ?? '',
        'date_naissance' => $apprenant['date_naissance'] ?? '',
        'lieu_naissance' => $apprenant['lieu_naissance'] ?? '',
        'referentiel_id' => $apprenant['referentiel_id'] ?? '',
        'is_correction' => true // Indiquer qu'il s'agit d'une correction
    ]);
}

/**
 * Traite le formulaire de correction d'un apprenant en liste d'attente
 */
function process_correct_waiting_apprenant() {
    global $model, $session_services, $validator_services, $mail_services, $apprenant_validator_services, $apprenant_manager_services;
    
    // Vérification de l'authentification
    $user = check_auth();
    
    // Récupérer l'index de l'apprenant dans la liste d'attente
    $apprenant_index = isset($_POST['apprenant_index']) ? (int)$_POST['apprenant_index'] : null;
    
    // Récupérer la liste d'attente
    $waiting_list = $session_services['get_session']('waiting_list', []);
    
    // Vérifier si l'index est valide
    if ($apprenant_index === null || !isset($waiting_list[$apprenant_index])) {
        $session_services['set_flash_message']('warning', 'Apprenant non trouvé dans la liste d\'attente');
        redirect('?page=apprenants&tab=waiting');
        return;
    }
    
    // Récupérer les données du formulaire
    $data = [
        'nom' => trim($_POST['nom'] ?? ''),
        'prenom' => trim($_POST['prenom'] ?? ''),
        'email' => trim($_POST['email'] ?? ''),
        'telephone' => trim($_POST['telephone'] ?? ''),
        'adresse' => trim($_POST['adresse'] ?? ''),
        'date_naissance' => trim($_POST['date_naissance'] ?? ''),
        'lieu_naissance' => trim($_POST['lieu_naissance'] ?? ''),
        'referentiel_id' => $_POST['referentiel_id'] ?? ''
    ];
    
    // Récupérer la promotion active
    $current_promotion = $model['get_current_promotion']();
    $data['promotion_id'] = $current_promotion['id'];
    
    // Validation des données
    $errors = $apprenant_validator_services['validate_apprenant_data']($data, $validator_services, $model);
    
    // Si des erreurs persistent, afficher à nouveau le formulaire avec les erreurs
    if (!empty($errors)) {
        // Récupérer les référentiels de la promotion
        $referentiels = $model['get_referentiels_by_promotion']($current_promotion['id']);
        
        render('admin.layout.php', 'apprenant/correct-waiting.html.php', [
            'active_menu' => 'apprenants',
            'apprenant' => array_merge($waiting_list[$apprenant_index], $data),
            'apprenant_index' => $apprenant_index,
            'current_promotion' => $current_promotion,
            'referentiels' => $referentiels,
            'errors' => $errors
        ]);
        return;
    }
    
    // Génération du matricule
    $matricule = $model['generate_matricule']();
    
    // Préparation des données de l'apprenant
    $apprenant_data = $apprenant_manager_services['prepare_apprenant_data']($data, 'assets/images/apprenants/default.jpg', $matricule);
    
    // Ajouter l'apprenant
    $result = $model['create_apprenant']($apprenant_data);
    
    if (!$result) {
        $session_services['set_flash_message']('danger', 'Erreur lors de l\'ajout de l\'apprenant');
        redirect('?page=apprenants&tab=waiting');
        return;
    }
    
    // Récupérer les informations du référentiel pour l'email
    $referentiel = $model['get_referentiel_by_id']($data['referentiel_id']);
    
    // Envoyer l'email de bienvenue
    $email_sent = $mail_services['send_welcome_email']($apprenant_data, $current_promotion, $referentiel);
    
    // Message de succès avec indication de l'envoi d'email
    if ($email_sent) {
        $session_services['set_flash_message']('success', 'Apprenant ajouté avec succès. Un email de bienvenue a été envoyé.');
    } else {
        $session_services['set_flash_message']('success', 'Apprenant ajouté avec succès, mais l\'envoi de l\'email a échoué.');
    }
    
    // Supprimer l'apprenant de la liste d'attente
    unset($waiting_list[$apprenant_index]);
    $waiting_list = array_values($waiting_list); // Réindexer le tableau
    $session_services['set_session']('waiting_list', $waiting_list);
    
    redirect('?page=apprenants&tab=waiting');
}
