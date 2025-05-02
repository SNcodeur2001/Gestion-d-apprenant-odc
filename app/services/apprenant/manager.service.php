<?php

namespace App\Services\Apprenant;

/**
 * Filtre les apprenants selon différents critères
 * 
 * @param array $apprenants Liste des apprenants
 * @param string $search Terme de recherche
 * @param string $classe_filter Filtre par classe/référentiel
 * @param string $status_filter Filtre par statut
 * @return array Apprenants filtrés
 */
function filter_apprenants($apprenants, $search = '', $classe_filter = '', $status_filter = '') {
    $filtered_apprenants = $apprenants;
    
    // Filtrer par recherche
    if (!empty($search)) {
        $filtered_apprenants = array_filter($filtered_apprenants, function($apprenant) use ($search) {
            return (
                stripos($apprenant['nom'], $search) !== false ||
                stripos($apprenant['prenom'], $search) !== false ||
                stripos($apprenant['matricule'], $search) !== false ||
                stripos($apprenant['email'], $search) !== false
            );
        });
    }
    
    // Filtrer par référentiel/classe
    if (!empty($classe_filter)) {
        $filtered_apprenants = array_filter($filtered_apprenants, function($apprenant) use ($classe_filter) {
            return $apprenant['referentiel_id'] == $classe_filter;
        });
    }
    
    // Filtrer par statut
    if (!empty($status_filter)) {
        $filtered_apprenants = array_filter($filtered_apprenants, function($apprenant) use ($status_filter) {
            return $apprenant['statut'] == $status_filter;
        });
    }
    
    return array_values($filtered_apprenants);
}

/**
 * Prépare les données d'un apprenant pour la création ou la mise à jour
 * 
 * @param array $data Données de l'apprenant
 * @param string $photo_path Chemin de la photo
 * @param string|null $matricule Matricule (null pour mise à jour)
 * @return array Données préparées
 */
function prepare_apprenant_data($data, $photo_path, $matricule = null) {
    $apprenant_data = [
        'nom' => $data['nom'],
        'prenom' => $data['prenom'],
        'photo' => $photo_path,
        'adresse' => $data['adresse'],
        'telephone' => $data['telephone'],
        'email' => $data['email'],
        'date_naissance' => $data['date_naissance'],
        'lieu_naissance' => $data['lieu_naissance'],
        'referentiel_id' => $data['referentiel_id']
    ];
    
    // Ajouter les champs spécifiques à la création
    if ($matricule !== null) {
        $apprenant_data['id'] = uniqid();
        $apprenant_data['matricule'] = $matricule;
        $apprenant_data['promotion_id'] = $data['promotion_id'];
        $apprenant_data['statut'] = 'actif';
        $apprenant_data['date_inscription'] = date('Y-m-d');
    } else if (isset($data['statut'])) {
        $apprenant_data['statut'] = $data['statut'];
    }
    
    return $apprenant_data;
}

// Exporter les fonctions
$apprenant_manager_services = [
    'filter_apprenants' => 'App\Services\Apprenant\filter_apprenants',
    'prepare_apprenant_data' => 'App\Services\Apprenant\prepare_apprenant_data'
];