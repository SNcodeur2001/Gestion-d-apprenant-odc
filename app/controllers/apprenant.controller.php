<?php

namespace App\Controllers;

require_once __DIR__ . '/controller.php';
require_once __DIR__ . '/../models/model.php';
require_once __DIR__ . '/../services/validator.service.php';
require_once __DIR__ . '/../services/session.service.php';
require_once __DIR__ . '/../services/mail.service.php';
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
    global $model, $session_services;
    
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
    
    // Filtrer par recherche
    if (!empty($search)) {
        $filtered_apprenants = array_filter($promotion_apprenants, function($apprenant) use ($search) {
            return (
                stripos($apprenant['nom'], $search) !== false ||
                stripos($apprenant['prenom'], $search) !== false ||
                stripos($apprenant['matricule'], $search) !== false ||
                stripos($apprenant['email'], $search) !== false
            );
        });
    } else {
        $filtered_apprenants = $promotion_apprenants;
    }
    
    // Filtrer par référentiel/classe
    if (!empty($classe_filter)) {
        $filtered_apprenants = array_filter($filtered_apprenants, function($apprenant) use ($classe_filter) {
            return $apprenant['referentiel_id'] == $classe_filter;
        });
        
        // Récupérer le nom du référentiel pour l'afficher dans la page
        $referentiel_name = '';
        $referentiels = $model['get_referentiels_by_promotion']($current_promotion['id']);
        foreach ($referentiels as $ref) {
            if ($ref['id'] == $classe_filter) {
                $referentiel_name = $ref['name'];
                break;
            }
        }
    }
    
    // Filtrer par statut
    if (!empty($status_filter)) {
        $filtered_apprenants = array_filter($filtered_apprenants, function($apprenant) use ($status_filter) {
            return $apprenant['statut'] == $status_filter;
        });
    }
    
    // Récupérer uniquement les référentiels de la promotion active pour le filtre
    $promotion_referentiels = $model['get_referentiels_by_promotion']($current_promotion['id']);
    $referentiels_map = array();
    foreach ($promotion_referentiels as $ref) {
        $referentiels_map[$ref['id']] = $ref['name'];
    }
    
    // Convertir le tableau associatif en tableau indexé pour la pagination
    $filtered_apprenants = array_values($filtered_apprenants);
    
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
        'referentiel_name' => $referentiel_name ?? '',
        'waiting_list' => $waiting_list, // Ajouter la liste d'attente
        'active_tab' => $active_tab // Ajouter l'onglet actif
    ]);
}

/**
 * Affiche le formulaire d'ajout d'un apprenant
 */
function add_apprenant_form() {
    global $model;
    
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
    global $model, $validator_services, $session_services, $mail_services;
    
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
    $nom = trim($_POST['nom'] ?? '');
    $prenom = trim($_POST['prenom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telephone = trim($_POST['telephone'] ?? '');
    $adresse = trim($_POST['adresse'] ?? '');
    $date_naissance = trim($_POST['date_naissance'] ?? '');
    $lieu_naissance = trim($_POST['lieu_naissance'] ?? '');
    $referentiel_id = $_POST['referentiel_id'] ?? '';
    
    // Informations du tuteur (optionnelles)
    $tuteur_nom = trim($_POST['tuteur_nom'] ?? '');
    $lien_parente = trim($_POST['lien_parente'] ?? '');
    $tuteur_adresse = trim($_POST['tuteur_adresse'] ?? '');
    $tuteur_telephone = trim($_POST['tuteur_telephone'] ?? '');
    
    // Validation des données
    $errors = [];
    
    // Validation des champs obligatoires
    if (empty($nom)) {
        $errors['nom'] = 'Le nom est requis';
    } elseif (strlen($nom) > 50) {
        $errors['nom'] = 'Le nom ne doit pas dépasser 50 caractères';
    }
    
    if (empty($prenom)) {
        $errors['prenom'] = 'Le prénom est requis';
    }
    
    if (empty($email)) {
        $errors['email'] = 'L\'email est requis';
    } elseif (!$validator_services['is_email']($email)) {
        $errors['email'] = 'Format d\'email invalide';
    } elseif ($model['email_exists']($email)) {
        $errors['email'] = 'Cet email est déjà utilisé';
    }
    
    if (empty($telephone)) {
        $errors['telephone'] = 'Le téléphone est requis';
    }
    
    if (empty($adresse)) {
        $errors['adresse'] = 'L\'adresse est requise';
    }
    
    if (empty($date_naissance)) {
        $errors['date_naissance'] = 'La date de naissance est requise';
    }
    
    if (empty($lieu_naissance)) {
        $errors['lieu_naissance'] = 'Le lieu de naissance est requis';
    }
    
    if (empty($referentiel_id)) {
        $errors['referentiel_id'] = 'Le référentiel est requis';
    }
    
    // Gestion de la photo
    $photo_path = 'assets/images/apprenants/default.jpg'; // Image par défaut
    
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $allowed = ['jpg', 'jpeg', 'png'];
        $max_size = 2 * 1024 * 1024; // 2MB en bytes
        
        $filename = $_FILES['photo']['name'];
        $filetype = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $filesize = $_FILES['photo']['size'];
        
        // Vérification du format
        if (!in_array($filetype, $allowed)) {
            $errors['photo'] = 'Format invalide. Formats acceptés : JPG, PNG';
        }
        
        // Vérification de la taille
        if ($filesize > $max_size) {
            $errors['photo'] = 'L\'image ne doit pas dépasser 2MB';
        }
        
        // Upload si pas d'erreur
        if (!isset($errors['photo'])) {
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
            'nom' => $nom,
            'prenom' => $prenom,
            'email' => $email,
            'telephone' => $telephone,
            'adresse' => $adresse,
            'date_naissance' => $date_naissance,
            'lieu_naissance' => $lieu_naissance,
            'referentiel_id' => $referentiel_id
        ]);
        return;
    }
    
    // Génération du matricule
    $matricule = $model['generate_matricule']();
    
    // Préparation des données de l'apprenant
    $apprenant_data = [
        'id' => uniqid(),
        'matricule' => $matricule,
        'nom' => $nom,
        'prenom' => $prenom,
        'photo' => $photo_path,
        'adresse' => $adresse,
        'telephone' => $telephone,
        'email' => $email,
        'date_naissance' => $date_naissance,
        'lieu_naissance' => $lieu_naissance,
        'promotion_id' => $current_promotion['id'],
        'referentiel_id' => $referentiel_id,
        'statut' => 'actif',
        'date_inscription' => date('Y-m-d')
    ];
    
    // Ajouter l'apprenant
    $result = $model['create_apprenant']($apprenant_data);
    
    if (!$result) {
        $session_services['set_flash_message']('danger', 'Erreur lors de l\'ajout de l\'apprenant');
        redirect('?page=add-apprenant');
        return;
    }
    
    // Récupérer les informations du référentiel pour l'email
    $referentiel = $model['get_referentiel_by_id']($referentiel_id);
    
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
        $session_services['set_flash_message']('success', 'L\'envoi de l\'email a échoué.');
    }
    
    redirect('?page=apprenants');
}

/**
 * Télécharge la liste des apprenants dans différents formats (CSV, Excel, PDF)
 */
function download_apprenants_list() {
    global $model, $session_services;
    
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
            download_apprenants_excel($promotion_apprenants, $referentiels_map, $current_promotion);
            break;
        case 'pdf':
            download_apprenants_pdf($promotion_apprenants, $referentiels_map, $current_promotion);
            break;
        case 'csv':
        default:
            download_apprenants_csv($promotion_apprenants, $referentiels_map, $current_promotion);
            break;
    }
}

/**
 * Télécharge la liste des apprenants au format CSV
 */
function download_apprenants_csv($promotion_apprenants, $referentiels_map, $current_promotion) {
    // Création du contenu CSV
    $csv_content = "Matricule,Nom,Prénom,Email,Téléphone,Adresse,Référentiel,Statut\n";
    
    foreach ($promotion_apprenants as $apprenant) {
        $referentiel_name = isset($referentiels_map[$apprenant['referentiel_id']]) 
            ? $referentiels_map[$apprenant['referentiel_id']] 
            : $apprenant['referentiel_id'];
        
        $csv_content .= sprintf(
            "%s,%s,%s,%s,%s,%s,%s,%s\n",
            $apprenant['matricule'],
            str_replace(',', ' ', $apprenant['nom']),
            str_replace(',', ' ', $apprenant['prenom']),
            $apprenant['email'],
            $apprenant['telephone'],
            str_replace(',', ' ', $apprenant['adresse']),
            str_replace(',', ' ', $referentiel_name),
            $apprenant['statut']
        );
    }
    
    // Configuration des headers pour le téléchargement
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="apprenants_' . $current_promotion['id'] . '.csv"');
    
    // Output du contenu CSV
    echo $csv_content;
    exit;
}

/**
 * Télécharge la liste des apprenants au format Excel
 */
function download_apprenants_excel($promotion_apprenants, $referentiels_map, $current_promotion) {
    // Charger la bibliothèque PhpSpreadsheet
    require_once __DIR__ . '/../../vendor/autoload.php';
    
    // Créer un nouveau document Excel
    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    
    // Définir les en-têtes
    $sheet->setCellValue('A1', 'Matricule');
    $sheet->setCellValue('B1', 'Nom');
    $sheet->setCellValue('C1', 'Prénom');
    $sheet->setCellValue('D1', 'Email');
    $sheet->setCellValue('E1', 'Téléphone');
    $sheet->setCellValue('F1', 'Adresse');
    $sheet->setCellValue('G1', 'Référentiel');
    $sheet->setCellValue('H1', 'Statut');
    
    // Style pour les en-têtes
    $headerStyle = [
        'font' => [
            'bold' => true,
            'color' => ['rgb' => 'FFFFFF'],
        ],
        'fill' => [
            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
            'startColor' => ['rgb' => '4F81BD'],
        ],
    ];
    $sheet->getStyle('A1:H1')->applyFromArray($headerStyle);
    
    // Remplir les données
    $row = 2;
    foreach ($promotion_apprenants as $apprenant) {
        $referentiel_name = isset($referentiels_map[$apprenant['referentiel_id']]) 
            ? $referentiels_map[$apprenant['referentiel_id']] 
            : $apprenant['referentiel_id'];
        
        $sheet->setCellValue('A' . $row, $apprenant['matricule']);
        $sheet->setCellValue('B' . $row, $apprenant['nom']);
        $sheet->setCellValue('C' . $row, $apprenant['prenom']);
        $sheet->setCellValue('D' . $row, $apprenant['email']);
        $sheet->setCellValue('E' . $row, $apprenant['telephone']);
        $sheet->setCellValue('F' . $row, $apprenant['adresse']);
        $sheet->setCellValue('G' . $row, $referentiel_name);
        $sheet->setCellValue('H' . $row, $apprenant['statut']);
        
        $row++;
    }
    
    // Ajuster la largeur des colonnes automatiquement
    foreach (range('A', 'H') as $col) {
        $sheet->getColumnDimension($col)->setAutoSize(true);
    }
    
    // Créer le writer Excel
    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
    
    // Configuration des headers pour le téléchargement
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="apprenants_' . $current_promotion['id'] . '.xlsx"');
    header('Cache-Control: max-age=0');
    
    // Output du fichier Excel
    $writer->save('php://output');
    exit;
}

/**
 * Télécharge la liste des apprenants au format PDF
 */
function download_apprenants_pdf($promotion_apprenants, $referentiels_map, $current_promotion) {
    // Charger la bibliothèque TCPDF
    require_once __DIR__ . '/../../vendor/autoload.php';
    
    // Créer un nouveau document PDF
    $pdf = new \TCPDF('L', 'mm', 'A4', true, 'UTF-8');
    
    // Définir les informations du document
    $pdf->SetCreator('Ges-Apprenant');
    $pdf->SetAuthor('Sonatel Academy');
    $pdf->SetTitle('Liste des Apprenants - ' . $current_promotion['name']);
    
    // Supprimer les en-têtes et pieds de page par défaut
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);
    
    // Définir les marges
    $pdf->SetMargins(10, 10, 10);
    
    // Ajouter une page
    $pdf->AddPage();
    
    // Définir la police
    $pdf->SetFont('helvetica', 'B', 14);
    
    // Titre
    $pdf->Cell(0, 10, 'Liste des Apprenants - ' . $current_promotion['name'], 0, 1, 'C');
    $pdf->Ln(5);
    
    // En-têtes du tableau
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->SetFillColor(79, 129, 189);
    $pdf->SetTextColor(255);
    
    $pdf->Cell(30, 7, 'Matricule', 1, 0, 'C', true);
    $pdf->Cell(30, 7, 'Nom', 1, 0, 'C', true);
    $pdf->Cell(30, 7, 'Prénom', 1, 0, 'C', true);
    $pdf->Cell(50, 7, 'Email', 1, 0, 'C', true);
    $pdf->Cell(30, 7, 'Téléphone', 1, 0, 'C', true);
    $pdf->Cell(40, 7, 'Référentiel', 1, 0, 'C', true);
    $pdf->Cell(20, 7, 'Statut', 1, 1, 'C', true);
    
    // Contenu du tableau
    $pdf->SetFont('helvetica', '', 9);
    $pdf->SetTextColor(0);
    $fill = false;
    
    foreach ($promotion_apprenants as $apprenant) {
        $referentiel_name = isset($referentiels_map[$apprenant['referentiel_id']]) 
            ? $referentiels_map[$apprenant['referentiel_id']] 
            : $apprenant['referentiel_id'];
        
        $pdf->Cell(30, 6, $apprenant['matricule'], 1, 0, 'L', $fill);
        $pdf->Cell(30, 6, $apprenant['nom'], 1, 0, 'L', $fill);
        $pdf->Cell(30, 6, $apprenant['prenom'], 1, 0, 'L', $fill);
        $pdf->Cell(50, 6, $apprenant['email'], 1, 0, 'L', $fill);
        $pdf->Cell(30, 6, $apprenant['telephone'], 1, 0, 'L', $fill);
        $pdf->Cell(40, 6, $referentiel_name, 1, 0, 'L', $fill);
        $pdf->Cell(20, 6, $apprenant['statut'], 1, 1, 'C', $fill);
        
        $fill = !$fill; // Alterner les couleurs de fond
    }
    
    // Générer le PDF
    $pdf->Output('apprenants_' . $current_promotion['id'] . '.pdf', 'D');
    exit;
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
 * Affiche le formulaire d'import des apprenants
 */
function import_apprenants() {
    global $model, $session_services;
    
    // Vérification de l'authentification
    $user = check_auth();
    
    // Récupérer la promotion en cours (avec l'état "en_cours")
    $promotion_en_cours = $model['get_promotion_en_cours']();
    
    // Vérifier si une promotion est en cours
    if (!$promotion_en_cours) {
        $session_services['set_flash_message']('warning', 'Aucune promotion n\'est actuellement en cours. L\'importation d\'apprenants n\'est possible que pour une promotion en cours.');
        redirect('?page=promotions');
        return;
    }
    
    // Récupérer uniquement les référentiels de la promotion en cours
    $referentiels = $model['get_referentiels_by_promotion']($promotion_en_cours['id']);
    
    // Si aucun référentiel n'est associé à la promotion
    if (empty($referentiels)) {
        $session_services['set_flash_message']('warning', 'Aucun référentiel n\'est associé à la promotion en cours. Veuillez d\'abord ajouter des référentiels.');
        redirect('?page=referentiels');
        return;
    }
    
    // Afficher la vue
    render('admin.layout.php', 'apprenant/import.html.php', [
        'active_menu' => 'apprenants',
        'referentiels' => $referentiels,
        'promotion_en_cours' => $promotion_en_cours
    ]);
}

/**
 * Traite l'import du fichier Excel des apprenants
 */
function process_import_apprenants() {
    global $model, $session_services;
    
    // Vérification de l'authentification
    $user = check_auth();
    
    // Récupérer la promotion en cours (avec l'état "en_cours")
    $promotion_en_cours = $model['get_current_promotion']();
    
    // Vérifier si une promotion est en cours
    if (!$promotion_en_cours) {
        $session_services['set_flash_message']('warning', 'Aucune promotion n\'est actuellement en cours. L\'importation d\'apprenants n\'est possible que pour une promotion en cours.');
        redirect('?page=promotions');
        return;
    }
    
    // Récupérer les référentiels de la promotion en cours
    $promotion_referentiels = $model['get_referentiels_by_promotion']($promotion_en_cours['id']);
    $promotion_referentiel_ids = array_column($promotion_referentiels, 'id');
    
    // Vérifier si un fichier a été uploadé
    if (!isset($_FILES['excel_file']) || $_FILES['excel_file']['error'] !== UPLOAD_ERR_OK) {
        $session_services['set_flash_message']('error', 'Erreur lors de l\'upload du fichier. Veuillez réessayer.');
        redirect('?page=import-apprenants');
        return;
    }
    
    // Vérifier le type de fichier
    $file_extension = pathinfo($_FILES['excel_file']['name'], PATHINFO_EXTENSION);
    if ($file_extension !== 'xlsx') {
        $session_services['set_flash_message']('error', 'Le fichier doit être au format Excel (.xlsx)');
        redirect('?page=import-apprenants');
        return;
    }
    
    // Récupérer le référentiel par défaut
    $default_referentiel_id = $_POST['referentiel_id'] ?? '';
    if (empty($default_referentiel_id)) {
        $session_services['set_flash_message']('error', 'Veuillez sélectionner un référentiel par défaut');
        redirect('?page=import-apprenants');
        return;
    }
    
    // Vérifier que le référentiel par défaut appartient à la promotion en cours
    if (!in_array($default_referentiel_id, $promotion_referentiel_ids)) {
        $session_services['set_flash_message']('error', 'Le référentiel sélectionné n\'appartient pas à la promotion en cours');
        redirect('?page=import-apprenants');
        return;
    }
    
    try {
        // Charger la bibliothèque PhpSpreadsheet
        require_once __DIR__ . '/../../vendor/autoload.php';
        
        // Lire le fichier Excel
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($_FILES['excel_file']['tmp_name']);
        $worksheet = $spreadsheet->getActiveSheet();
        $rows = $worksheet->toArray();
        
        // Vérifier si le fichier a des données
        if (count($rows) <= 1) {
            throw new \Exception("Le fichier ne contient pas de données");
        }
        
        // Récupérer les en-têtes (première ligne)
        $headers = array_map('strtolower', $rows[0]);
        
        // Vérifier les colonnes requises
        $required_columns = ['nom', 'prenom', 'email', 'telephone', 'adresse'];
        foreach ($required_columns as $column) {
            if (!in_array($column, $headers)) {
                throw new \Exception("Colonne requise manquante: $column");
            }
        }
        
        // Récupérer les indices des colonnes
        $column_indices = [];
        foreach ($headers as $index => $header) {
            $column_indices[$header] = $index;
        }
        
        // Traiter chaque ligne (sauf l'en-tête)
        $success_count = 0;
        $error_count = 0;
        $waiting_list = []; // Liste des apprenants en attente
        
        for ($i = 1; $i < count($rows); $i++) {
            $row = $rows[$i];
            
            // Vérifier si la ligne a des données
            if (empty($row[$column_indices['nom']]) || empty($row[$column_indices['prenom']])) {
                continue; // Ignorer les lignes vides
            }
            
            // Extraire les données de l'apprenant
            $nom = trim($row[$column_indices['nom']]);
            $prenom = trim($row[$column_indices['prenom']]);
            $email = trim($row[$column_indices['email']]);
            $telephone = trim($row[$column_indices['telephone']]);
            $adresse = trim($row[$column_indices['adresse']]);
            $date_naissance = isset($column_indices['date_naissance']) ? trim($row[$column_indices['date_naissance']]) : '';
            $lieu_naissance = isset($column_indices['lieu_naissance']) ? trim($row[$column_indices['lieu_naissance']]) : '';
            
            // Récupérer le référentiel_id du fichier ou utiliser celui par défaut
            $referentiel_id = isset($column_indices['referentiel_id']) && !empty($row[$column_indices['referentiel_id']]) 
                ? trim($row[$column_indices['referentiel_id']]) 
                : $default_referentiel_id;
            
            // Initialiser le tableau d'erreurs pour cet apprenant
            $apprenant_errors = [];
            
            // Valider les données
            if (empty($nom)) {
                $apprenant_errors['nom'] = "Le nom est requis";
            }
            
            if (empty($prenom)) {
                $apprenant_errors['prenom'] = "Le prénom est requis";
            }
            
            if (empty($email)) {
                $apprenant_errors['email'] = "L'email est requis";
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $apprenant_errors['email'] = "Format d'email invalide";
            } elseif ($model['email_exists']($email)) {
                $apprenant_errors['email'] = "L'email existe déjà";
            }
            
            if (empty($telephone)) {
                $apprenant_errors['telephone'] = "Le téléphone est requis";
            }
            
            if (empty($adresse)) {
                $apprenant_errors['adresse'] = "L'adresse est requise";
            }
            
            // Vérifier que le référentiel appartient à la promotion en cours
            if (!in_array($referentiel_id, $promotion_referentiel_ids)) {
                $apprenant_errors['referentiel_id'] = "Le référentiel n'appartient pas à la promotion en cours";
            }
            
            // Si aucune erreur, ajouter l'apprenant
            if (empty($apprenant_errors)) {
                try {
                    // Générer un matricule unique
                    $matricule = $model['generate_matricule']();
                    
                    // Préparer les données de l'apprenant
                    $apprenant_data = [
                        'id' => uniqid(),
                        'matricule' => $matricule,
                        'nom' => $nom,
                        'prenom' => $prenom,
                        'email' => $email,
                        'telephone' => $telephone,
                        'adresse' => $adresse,
                        'date_naissance' => $date_naissance,
                        'lieu_naissance' => $lieu_naissance,
                        'promotion_id' => $promotion_en_cours['id'],
                        'referentiel_id' => $referentiel_id,
                        'statut' => 'actif',
                        'date_inscription' => date('Y-m-d'),
                        'photo' => 'assets/images/apprenants/default.jpg' // Photo par défaut
                    ];
                    
                    // Ajouter l'apprenant
                    $result = $model['create_apprenant']($apprenant_data);
                    
                    if ($result) {
                        $success_count++;
                    } else {
                        throw new \Exception("Erreur lors de l'ajout de l'apprenant");
                    }
                } catch (\Exception $e) {
                    $error_count++;
                    $apprenant_errors['general'] = $e->getMessage();
                    
                    // Ajouter à la liste d'attente
                    $waiting_list[] = [
                        'nom' => $nom,
                        'prenom' => $prenom,
                        'email' => $email,
                        'telephone' => $telephone,
                        'adresse' => $adresse,
                        'date_naissance' => $date_naissance,
                        'lieu_naissance' => $lieu_naissance,
                        'referentiel_id' => $referentiel_id,
                        'errors' => $apprenant_errors
                    ];
                }
            } else {
                $error_count++;
                
                // Ajouter à la liste d'attente
                $waiting_list[] = [
                    'nom' => $nom,
                    'prenom' => $prenom,
                    'email' => $email,
                    'telephone' => $telephone,
                    'adresse' => $adresse,
                    'date_naissance' => $date_naissance,
                    'lieu_naissance' => $lieu_naissance,
                    'referentiel_id' => $referentiel_id,
                    'errors' => $apprenant_errors
                ];
            }
        }
        
        // Sauvegarder la liste d'attente dans la session
        $session_services['set_session']('waiting_list', $waiting_list);
        
        // Afficher un message de succès ou d'erreur
        if ($success_count > 0) {
            $message = "$success_count apprenants ont été importés avec succès.";
            if ($error_count > 0) {
                $message .= " $error_count apprenants n'ont pas pu être importés et ont été placés en liste d'attente.";
                $session_services['set_flash_message']('warning', $message);
                redirect('?page=waiting-list');
                return;
            } else {
                $session_services['set_flash_message']('success', $message);
            }
        } else {
            if ($error_count > 0) {
                $message = "Aucun apprenant n'a pu être importé. $error_count apprenants ont été placés en liste d'attente.";
                $session_services['set_flash_message']('warning', $message);
                redirect('?page=waiting-list');
                return;
            } else {
                $session_services['set_flash_message']('error', "Aucun apprenant n'a pu être importé. Vérifiez le format du fichier.");
            }
        }
        
        redirect('?page=apprenants');
        
    } catch (\Exception $e) {
        $session_services['set_flash_message']('error', $e->getMessage());
        redirect('?page=import-apprenants');
    }
}

/**
 * Télécharge un template Excel pour l'import des apprenants
 */
function download_apprenant_template() {
    // Charger la bibliothèque PhpSpreadsheet
    require_once __DIR__ . '/../../vendor/autoload.php';
    
    // Créer un nouveau document Excel
    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    
    // Définir les en-têtes
    $headers = ['nom', 'prenom', 'email', 'telephone', 'adresse', 'date_naissance', 'lieu_naissance', 'referentiel_id'];
    
    // Ajouter les en-têtes à la première ligne
    foreach ($headers as $index => $header) {
        $column = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($index);
        $sheet->setCellValue($column . '1', $header);
    }
    
    // Style pour les en-têtes
    $headerStyle = [
        'font' => [
            'bold' => true,
            'color' => ['rgb' => 'FFFFFF'],
        ],
        'fill' => [
            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
            'startColor' => ['rgb' => '4F81BD'],
        ],
    ];
    $sheet->getStyle('A1:' . \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($headers) - 1) . '1')->applyFromArray($headerStyle);
    
    // Ajuster la largeur des colonnes automatiquement
    foreach (range(0, count($headers) - 1) as $col) {
        $column = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);
        $sheet->getColumnDimension($column)->setAutoSize(true);
    }
    
    // Ajouter quelques exemples de données
    $examples = [
        ['Doe', 'John', 'john.doe@example.com', '771234567', 'Dakar, Senegal', '1995-05-15', 'Dakar', ''],
        ['Smith', 'Jane', 'jane.smith@example.com', '772345678', 'Thies, Senegal', '1997-08-22', 'Thies', ''],
    ];
    
    foreach ($examples as $rowIndex => $rowData) {
        foreach ($rowData as $colIndex => $cellValue) {
            $column = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex);
            $sheet->setCellValue($column . ($rowIndex + 2), $cellValue);
        }
    }
    
    // Créer le writer Excel
    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
    
    // Configuration des headers pour le téléchargement
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="template_import_apprenants.xlsx"');
    header('Cache-Control: max-age=0');
    
    // Output du fichier Excel
    $writer->save('php://output');
    exit;
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
    global $model, $validator_services, $session_services;
    
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
    $nom = trim($_POST['nom'] ?? '');
    $prenom = trim($_POST['prenom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telephone = trim($_POST['telephone'] ?? '');
    $adresse = trim($_POST['adresse'] ?? '');
    $date_naissance = trim($_POST['date_naissance'] ?? '');
    $lieu_naissance = trim($_POST['lieu_naissance'] ?? '');
    $referentiel_id = $_POST['referentiel_id'] ?? '';
    $statut = $_POST['statut'] ?? 'actif';
    
    // Validation des données
    $errors = [];
    
    // Validation des champs obligatoires
    if (empty($nom)) {
        $errors['nom'] = 'Le nom est requis';
    } elseif (strlen($nom) > 50) {
        $errors['nom'] = 'Le nom ne doit pas dépasser 50 caractères';
    }
    
    if (empty($prenom)) {
        $errors['prenom'] = 'Le prénom est requis';
    }
    
    if (empty($email)) {
        $errors['email'] = 'L\'email est requis';
    } elseif (!$validator_services['is_email']($email)) {
        $errors['email'] = 'Format d\'email invalide';
    } elseif ($email !== $apprenant['email'] && $model['email_exists']($email)) {
        $errors['email'] = 'Cet email est déjà utilisé';
    }
    
    if (empty($telephone)) {
        $errors['telephone'] = 'Le téléphone est requis';
    }
    
    if (empty($adresse)) {
        $errors['adresse'] = 'L\'adresse est requise';
    }
    
    if (empty($referentiel_id)) {
        $errors['referentiel_id'] = 'Le référentiel est requis';
    }
    
    // Gestion de la photo
    $photo_path = $apprenant['photo']; // Conserver l'ancienne photo par défaut
    
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $allowed = ['jpg', 'jpeg', 'png'];
        $max_size = 2 * 1024 * 1024; // 2MB en bytes
        
        $filename = $_FILES['photo']['name'];
        $filetype = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $filesize = $_FILES['photo']['size'];
        
        // Vérification du format
        if (!in_array($filetype, $allowed)) {
            $errors['photo'] = 'Format invalide. Formats acceptés : JPG, PNG';
        }
        
        // Vérification de la taille
        if ($filesize > $max_size) {
            $errors['photo'] = 'L\'image ne doit pas dépasser 2MB';
        }
        
        // Upload si pas d'erreur
        if (!isset($errors['photo'])) {
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
            'nom' => $nom,
            'prenom' => $prenom,
            'email' => $email,
            'telephone' => $telephone,
            'adresse' => $adresse,
            'date_naissance' => $date_naissance,
            'lieu_naissance' => $lieu_naissance,
            'referentiel_id' => $referentiel_id,
            'statut' => $statut
        ]);
        return;
    }
    
    // Préparation des données de l'apprenant
    $apprenant_data = [
        'nom' => $nom,
        'prenom' => $prenom,
        'photo' => $photo_path,
        'adresse' => $adresse,
        'telephone' => $telephone,
        'email' => $email,
        'date_naissance' => $date_naissance,
        'lieu_naissance' => $lieu_naissance,
        'referentiel_id' => $referentiel_id,
        'statut' => $statut
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
// function waiting_list() {
//     global $model, $session_services;
    
//     // Vérification de l'authentification
//     $user = check_auth();
    
//     // Récupérer la liste d'attente de la session
//     $waiting_list = $session_services['get']('waiting_list', []);
    
//     // Si la liste d'attente est vide, rediriger vers la liste des apprenants
//     if (empty($waiting_list)) {
//         $session_services['set_flash_message']('info', 'La liste d\'attente est vide.');
//         redirect('?page=apprenants');
//         return;
//     }
    
//     // Récupérer les référentiels pour afficher les noms
//     $referentiels = $model['get_all_referentiels']();
//     $referentiels_map = [];
//     foreach ($referentiels as $ref) {
//         $referentiels_map[$ref['id']] = $ref['name'];
//     }
    
//     // Rendre la vue
//     render('admin.layout.php', 'apprenant/waiting-list.html.php', [
//         'active_menu' => 'apprenants',
//         'waiting_list' => $waiting_list,
//         'referentiels_map' => $referentiels_map
//     ]);
// }

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
    $apprenant_index = isset($_POST['apprenant_index']) ? (int)$_POST['apprenant_index'] : null;
    
    // Récupérer la liste d'attente
    $waiting_list = $session_services['get_session']('waiting_list', []);


    
    // Vérifier si l'index est valide
    if ($apprenant_index === null || !isset($waiting_list[$apprenant_index])) {
        $session_services['set_flash_message']('warning', 'Apprenant non trouvé dans la liste d\'attente');
        redirect('?page=waiting-list');
        return;
    }
    
    // Récupérer l'apprenant en attente
    $apprenant = $waiting_list[$apprenant_index];
    
    // Récupérer la promotion active
    $current_promotion = $model['get_current_promotion']();
    
    // Récupérer les référentiels de la promotion
    $referentiels = $model['get_referentiels_by_promotion']($current_promotion['id']);
    
    // Rendre la vue pour corriger l'apprenant
    render('admin.layout.php', 'apprenant/correct-waiting.html.php', [
        'active_menu' => 'apprenants',
        'apprenant' => $apprenant,
        'apprenant_index' => $apprenant_index,
        'current_promotion' => $current_promotion,
        'referentiels' => $referentiels,
        'errors' => $apprenant['errors'] ?? []
    ]);
}

/**
 * Traite le formulaire de correction d'un apprenant en liste d'attente
 */

/**
 * Traite le formulaire de correction d'un apprenant en liste d'attente
 */
function process_correct_waiting_apprenant() {
    global $model, $session_services, $validator_services;
    
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
    $nom = trim($_POST['nom'] ?? '');
    $prenom = trim($_POST['prenom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telephone = trim($_POST['telephone'] ?? '');
    $adresse = trim($_POST['adresse'] ?? '');
    $date_naissance = trim($_POST['date_naissance'] ?? '');
    $lieu_naissance = trim($_POST['lieu_naissance'] ?? '');
    $referentiel_id = $_POST['referentiel_id'] ?? '';
    
    // Validation des données
    $errors = [];
    
    // Validation des champs obligatoires
    if (empty($nom)) {
        $errors['nom'] = 'Le nom est requis';
    } elseif (strlen($nom) > 50) {
        $errors['nom'] = 'Le nom ne doit pas dépasser 50 caractères';
    }
    
    if (empty($prenom)) {
        $errors['prenom'] = 'Le prénom est requis';
    }
    
    if (empty($email)) {
        $errors['email'] = 'L\'email est requis';
    } elseif (!$validator_services['is_email']($email)) {
        $errors['email'] = 'Format d\'email invalide';
    } elseif ($model['email_exists']($email)) {
        $errors['email'] = 'Cet email est déjà utilisé';
    }
    
    if (empty($telephone)) {
        $errors['telephone'] = 'Le téléphone est requis';
    }
    
    if (empty($adresse)) {
        $errors['adresse'] = 'L\'adresse est requise';
    }
    
    if (empty($date_naissance)) {
        $errors['date_naissance'] = 'La date de naissance est requise';
    }
    
    if (empty($lieu_naissance)) {
        $errors['lieu_naissance'] = 'Le lieu de naissance est requis';
    }
    
    if (empty($referentiel_id)) {
        $errors['referentiel_id'] = 'Le référentiel est requis';
    }
    
    // Si des erreurs persistent, afficher à nouveau le formulaire avec les erreurs
    if (!empty($errors)) {
        // Récupérer la promotion active
        $current_promotion = $model['get_current_promotion']();
        
        // Récupérer les référentiels de la promotion
        $referentiels = $model['get_referentiels_by_promotion']($current_promotion['id']);
        
        render('admin.layout.php', 'apprenant/correct-waiting.html.php', [
            'active_menu' => 'apprenants',
            'apprenant' => [
                'nom' => $nom,
                'prenom' => $prenom,
                'email' => $email,
                'telephone' => $telephone,
                'adresse' => $adresse,
                'date_naissance' => $date_naissance,
                'lieu_naissance' => $lieu_naissance,
                'referentiel_id' => $referentiel_id
            ],
            'apprenant_index' => $apprenant_index,
            'current_promotion' => $current_promotion,
            'referentiels' => $referentiels,
            'errors' => $errors
        ]);
        return;
    }
    
    // Récupérer la promotion active
    $current_promotion = $model['get_current_promotion']();
    
    // Génération du matricule
    $matricule = $model['generate_matricule']();
    
    // Préparation des données de l'apprenant
    $apprenant_data = [
        'id' => uniqid(),
        'matricule' => $matricule,
        'nom' => $nom,
        'prenom' => $prenom,
        'photo' => 'assets/images/apprenants/default.jpg', // Photo par défaut
        'adresse' => $adresse,
        'telephone' => $telephone,
        'email' => $email,
        'date_naissance' => $date_naissance,
        'lieu_naissance' => $lieu_naissance,
        'promotion_id' => $current_promotion['id'],
        'referentiel_id' => $referentiel_id,
        'statut' => 'actif',
        'date_inscription' => date('Y-m-d')
    ];
    
    // Ajouter l'apprenant
    $result = $model['create_apprenant']($apprenant_data);
    
    if (!$result) {
        $session_services['set_flash_message']('danger', 'Erreur lors de l\'ajout de l\'apprenant');
        redirect('?page=apprenants&tab=waiting');
        return;
    }
    
    // Récupérer les informations du référentiel pour l'email
    $referentiel = $model['get_referentiel_by_id']($referentiel_id);
    
    // Envoyer l'email de bienvenue si la fonction existe
    if (function_exists('send_welcome_email')) {
        $email_sent = $mail_services['send_welcome_email']($apprenant_data, $current_promotion, $referentiel);
        
        // Message de succès avec indication de l'envoi d'email
        if ($email_sent) {
            $session_services['set_flash_message']('success', 'Apprenant ajouté avec succès. Un email de bienvenue a été envoyé.');
        } else {
            $session_services['set_flash_message']('success', 'Apprenant ajouté avec succès, mais l\'envoi de l\'email a échoué.');
        }
    } else {
        $session_services['set_flash_message']('success', 'Apprenant ajouté avec succès.');
    }
    
    // Supprimer l'apprenant de la liste d'attente
    unset($waiting_list[$apprenant_index]);
    $waiting_list = array_values($waiting_list); // Réindexer le tableau
    $session_services['set_session']('waiting_list', $waiting_list);
    
    redirect('?page=apprenants&tab=waiting');
}