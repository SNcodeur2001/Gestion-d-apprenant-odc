<?php

namespace App\Services\Apprenant;

/**
 * Valide les données d'un apprenant
 * 
 * @param array $data Données de l'apprenant
 * @param array $validator_services Services de validation
 * @param array $model Modèle de données
 * @param string|null $current_email Email actuel (pour l'édition)
 * @return array Erreurs de validation
 */
function validate_apprenant_data($data, $validator_services, $model, $current_email = null) {
    $errors = [];
    
    // Validation des champs obligatoires
    if (empty($data['nom'])) {
        $errors['nom'] = 'Le nom est requis';
    } elseif (strlen($data['nom']) > 50) {
        $errors['nom'] = 'Le nom ne doit pas dépasser 50 caractères';
    }
    
    if (empty($data['prenom'])) {
        $errors['prenom'] = 'Le prénom est requis';
    }
    
    if (empty($data['email'])) {
        $errors['email'] = 'L\'email est requis';
    } elseif (!$validator_services['is_email']($data['email'])) {
        $errors['email'] = 'Format d\'email invalide';
    } elseif ($data['email'] !== $current_email && $model['email_exists']($data['email'])) {
        $errors['email'] = 'Cet email est déjà utilisé';
    }
    
    if (empty($data['telephone'])) {
        $errors['telephone'] = 'Le téléphone est requis';
    }
    
    if (empty($data['adresse'])) {
        $errors['adresse'] = 'L\'adresse est requise';
    }
    
    if (empty($data['date_naissance'])) {
        $errors['date_naissance'] = 'La date de naissance est requise';
    }
    
    if (empty($data['lieu_naissance'])) {
        $errors['lieu_naissance'] = 'Le lieu de naissance est requis';
    }
    
    if (empty($data['referentiel_id'])) {
        $errors['referentiel_id'] = 'Le référentiel est requis';
    }
    
    return $errors;
}

/**
 * Valide une photo d'apprenant
 * 
 * @param array $file Fichier téléchargé ($_FILES['photo'])
 * @return array Erreurs de validation
 */
function validate_apprenant_photo($file) {
    $errors = [];
    
    if (!isset($file) || $file['error'] === UPLOAD_ERR_NO_FILE) {
        return $errors; // Pas d'erreur si pas de fichier (photo optionnelle)
    }
    
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $errors['photo'] = 'Erreur lors du téléchargement de la photo';
        return $errors;
    }
    
    $allowed = ['jpg', 'jpeg', 'png'];
    $max_size = 2 * 1024 * 1024; // 2MB en bytes
    
    $filename = $file['name'];
    $filetype = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    $filesize = $file['size'];
    
    // Vérification du format
    if (!in_array($filetype, $allowed)) {
        $errors['photo'] = 'Format invalide. Formats acceptés : JPG, PNG';
    }
    
    // Vérification de la taille
    if ($filesize > $max_size) {
        $errors['photo'] = 'L\'image ne doit pas dépasser 2MB';
    }
    
    return $errors;
}

// Exporter les fonctions
$apprenant_validator_services = [
    'validate_apprenant_data' => 'App\Services\Apprenant\validate_apprenant_data',
    'validate_apprenant_photo' => 'App\Services\Apprenant\validate_apprenant_photo'
];