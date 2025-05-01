<?php

namespace App\Models;

// Fonctions pour la gestion des référentiels
$referentiel_model = [
    // Récupérer tous les référentiels
    'get_all_referentiels' => function () use (&$model) {
        $data = $model['read_data']();
        return $data['referentiels'] ?? [];
    },
    
    // Récupérer un référentiel par son ID
    'get_referentiel_by_id' => function ($id) use (&$model) {
        $data = $model['read_data']();
        
        // Utiliser array_filter au lieu de foreach
        $filtered_referentiels = array_filter($data['referentiels'] ?? [], function ($referentiel) use ($id) {
            return $referentiel['id'] === $id;
        });
        
        return !empty($filtered_referentiels) ? reset($filtered_referentiels) : null;
    },
    
    // Vérifier si un nom de référentiel existe déjà
    'referentiel_name_exists' => function ($name, $exclude_id = null) use (&$model) {
        $data = $model['read_data']();
        
        // Utiliser array_filter au lieu de foreach
        $filtered_referentiels = array_filter($data['referentiels'] ?? [], function ($referentiel) use ($name, $exclude_id) {
            return strtolower($referentiel['name']) === strtolower($name) && ($exclude_id === null || $referentiel['id'] !== $exclude_id);
        });
        
        return !empty($filtered_referentiels);
    },
    
    // Créer un nouveau référentiel
    'create_referentiel' => function ($referentiel_data) use (&$model) {
        $data = $model['read_data']();
        
        // Générer un ID unique
        $referentiel_data['id'] = uniqid();
        
        // Ajouter le référentiel à la liste
        $data['referentiels'][] = $referentiel_data;
        
        // Sauvegarder les modifications
        return $model['write_data']($data);
    },
    
    // Récupérer les référentiels d'une promotion
    'get_referentiels_by_promotion' => function($promotion_id) use (&$model) {
        $data = $model['read_data']();
        
        // Trouver la promotion
        $promotion = null;
        foreach ($data['promotions'] as $p) {
            if ($p['id'] == $promotion_id) {
                $promotion = $p;
                break;
            }
        }
        
        if (!$promotion || empty($promotion['referentiels'])) {
            return [];
        }
        
        // Récupérer les référentiels associés
        return array_filter($data['referentiels'], function($ref) use ($promotion) {
            return in_array($ref['id'], $promotion['referentiels']);
        });
    },
    
    // Rechercher des référentiels par terme de recherche
    'search_referentiels' => function(string $query) use (&$model) {
        $referentiels = $model['get_all_referentiels']();
        if (empty($query)) {
            return $referentiels;
        }
        
        return array_filter($referentiels, function($ref) use ($query) {
            return stripos($ref['name'], $query) !== false || 
                   stripos($ref['description'], $query) !== false;
        });
    },
    
    // Récupérer un référentiel par son nom
    'get_referentiel_by_name' => function($name) use (&$model) {
        $data = $model['read_data']();
        foreach ($data['referentiels'] as $ref) {
            if (strtolower($ref['name']) === strtolower($name)) {
                return $ref;
            }
        }
        return null;
    },
    
    // Vérifier si un référentiel a des apprenants
    'referentiel_has_apprenants' => function ($promotion_id, $referentiel_id) use (&$model) {
        $data = $model['read_data']();
        
        // Trouver la promotion
        $promotion = null;
        foreach ($data['promotions'] as $p) {
            if ($p['id'] == $promotion_id) {
                $promotion = $p;
                break;
            }
        }
        
        if (!$promotion || empty($promotion['apprenants'])) {
            return false;
        }
        
        // Vérifier si des apprenants utilisent ce référentiel
        foreach ($promotion['apprenants'] as $apprenant) {
            if (isset($apprenant['referentiel_id']) && $apprenant['referentiel_id'] == $referentiel_id) {
                return true;
            }
        }
        
        return false;
    },
    
    // Mettre à jour un référentiel
    'update_referentiel' => function ($id, $referentiel_data) use (&$model) {
        $data = $model['read_data']();
        
        // Trouver l'index du référentiel
        $referentiel_indices = array_keys(array_filter($data['referentiels'], function($ref) use ($id) {
            return $ref['id'] === $id;
        }));
        
        if (empty($referentiel_indices)) {
            return false;
        }
        
        $referentiel_index = reset($referentiel_indices);
        
        // Mettre à jour les données du référentiel
        $data['referentiels'][$referentiel_index] = array_merge(
            $data['referentiels'][$referentiel_index],
            $referentiel_data
        );
        
        return $model['write_data']($data);
    }
];