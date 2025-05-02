<?php

namespace App\Services\Apprenant;

/**
 * Valide et traite un fichier Excel d'import d'apprenants
 * 
 * @param array $file Fichier téléchargé ($_FILES['excel_file'])
 * @param string $default_referentiel_id ID du référentiel par défaut
 * @param array $promotion_referentiel_ids IDs des référentiels de la promotion
 * @param array $model Modèle de données
 * @return array Résultat du traitement
 */
function process_import_file($file, $default_referentiel_id, $promotion_referentiel_ids, $model) {
    // Vérifier si un fichier a été uploadé
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        return [
            'success' => false,
            'message' => 'Erreur lors de l\'upload du fichier. Veuillez réessayer.'
        ];
    }
    
    // Vérifier le type de fichier
    $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    if ($file_extension !== 'xlsx') {
        return [
            'success' => false,
            'message' => 'Le fichier doit être au format Excel (.xlsx)'
        ];
    }
    
    try {
        // Charger la bibliothèque PhpSpreadsheet
        require_once __DIR__ . '/../../../vendor/autoload.php';
        
        // Lire le fichier Excel
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file['tmp_name']);
        $worksheet = $spreadsheet->getActiveSheet();
        $rows = $worksheet->toArray();
        
        // Vérifier si le fichier a des données
        if (count($rows) <= 1) {
            return [
                'success' => false,
                'message' => 'Le fichier ne contient pas de données'
            ];
        }
        
        // Récupérer les en-têtes (première ligne)
        $headers = array_map('strtolower', $rows[0]);
        
        // Vérifier les colonnes requises
        $required_columns = ['nom', 'prenom', 'email', 'telephone', 'adresse'];
        foreach ($required_columns as $column) {
            if (!in_array($column, $headers)) {
                return [
                    'success' => false,
                    'message' => "Colonne requise manquante: $column"
                ];
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
                        'promotion_id' => $_POST['promotion_id'],
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
        
        return [
            'success' => true,
            'success_count' => $success_count,
            'error_count' => $error_count,
            'waiting_list' => $waiting_list
        ];
        
    } catch (\Exception $e) {
        return [
            'success' => false,
            'message' => $e->getMessage()
        ];
    }
}

// Exporter les fonctions
$apprenant_import_services = [
    'process_import_file' => 'App\Services\Apprenant\process_import_file'
];