<?php

namespace App\Models;

// Charger le modèle de base
require_once __DIR__ . '/model.php';

// Charger les autres modèles
require_once __DIR__ . '/user.model.php';
require_once __DIR__ . '/promotion.model.php';
require_once __DIR__ . '/referentiel.model.php';
require_once __DIR__ . '/apprenant.model.php';

/**
 * Initialisation et fusion des modèles
 * 
 * Cette fonction combine tous les modèles en un seul tableau associatif.
 * Les fonctions du modèle de base sont disponibles pour toutes les autres fonctions.
 */
function init_models() {
    global $model_base, $user_model, $promotion_model, $referentiel_model, $apprenant_model;
    
    // Créer le modèle global avec les fonctions de base
    $model = $model_base;
    
    // Fusionner les modèles en utilisant une référence au modèle global
    merge_models($model, $user_model);
    merge_models($model, $promotion_model);
    merge_models($model, $referentiel_model);
    merge_models($model, $apprenant_model);
    
    return $model;
}

/**
 * Fusionner un modèle dans le modèle global
 * 
 * @param array &$model Le modèle global (par référence)
 * @param array $module_model Le modèle à fusionner
 */
function merge_models(&$model, $module_model) {
    foreach ($module_model as $key => $function) {
        $model[$key] = $function;
    }
}

// Initialiser et exporter le modèle global
$model = init_models();