<?php

namespace App\Models;

require_once __DIR__ . '/../enums/status.enum.php';

use App\Enums\Status;

// Fonctions pour la gestion des promotions
$promotion_model = [
    // Récupérer toutes les promotions
    'get_all_promotions' => function () use (&$model) {
        $data = $model['read_data']();
        return $data['promotions'] ?? [];
    },
    
    // Récupérer une promotion par son ID
    'get_promotion_by_id' => function ($id) use (&$model) {
        $data = $model['read_data']();
        
        // Utiliser array_filter au lieu de foreach
        $filtered_promotions = array_filter($data['promotions'] ?? [], function ($promotion) use ($id) {
            return $promotion['id'] === $id;
        });
        
        return !empty($filtered_promotions) ? reset($filtered_promotions) : null;
    },
    
    // Vérifier si un nom de promotion existe déjà
    'promotion_name_exists' => function(string $name) use (&$model): bool {
        $data = $model['read_data']();
        
        foreach ($data['promotions'] as $promotion) {
            if (strtolower($promotion['name']) === strtolower($name)) {
                return true;
            }
        }
        
        return false;
    },
    
    // Créer une nouvelle promotion
    'create_promotion' => function(array $promotion_data) use (&$model) {
        $data = $model['read_data']();
        
        // Générer un nouvel ID
        $max_id = 0;
        foreach ($data['promotions'] as $promotion) {
            $max_id = max($max_id, (int)$promotion['id']);
        }
        
        $promotion_data['id'] = $max_id + 1;
        $promotion_data['status'] = 'inactive'; // Statut inactif par défaut
        
        // Ajouter la promotion
        $data['promotions'][] = $promotion_data;
        
        // Sauvegarder les données
        return $model['write_data']($data);
    },
    
    // Mettre à jour une promotion existante
    'update_promotion' => function ($id, $promotion_data) use (&$model) {
        $data = $model['read_data']();
        
        // Trouver l'index de la promotion
        $promotion_indices = array_keys(array_filter($data['promotions'], function($promotion) use ($id) {
            return $promotion['id'] === $id;
        }));
        
        if (empty($promotion_indices)) {
            return false;
        }
        
        $promotion_index = reset($promotion_indices);
        
        // Mettre à jour les données de la promotion
        $data['promotions'][$promotion_index] = array_merge(
            $data['promotions'][$promotion_index],
            $promotion_data
        );
        
        if ($model['write_data']($data)) {
            return $data['promotions'][$promotion_index];
        }
        
        return null;
    },
    
    // Basculer le statut d'une promotion (activer/désactiver)
    'toggle_promotion_status' => function(int $promotion_id) use (&$model) {
        $data = $model['read_data']();
        
        // Trouver la promotion à modifier
        $target_promotion = null;
        $target_index = null;
        
        foreach ($data['promotions'] as $index => $promotion) {
            if ((int)$promotion['id'] === $promotion_id) {
                $target_promotion = $promotion;
                $target_index = $index;
                break;
            }
        }
        
        if ($target_index === null) {
            return false;
        }
        
        // Si la promotion est inactive
        if ($target_promotion['status'] === Status::INACTIVE->value) {
            // Désactiver toutes les promotions
            $data['promotions'] = array_map(function($p) {
                $p['status'] = Status::INACTIVE->value;
                return $p;
            }, $data['promotions']);
            
            // Activer la promotion ciblée
            $data['promotions'][$target_index]['status'] = Status::ACTIVE->value;
        } else {
            // Si la promotion est active, la désactiver
            $data['promotions'][$target_index]['status'] = Status::INACTIVE->value;
        }
        
        // Sauvegarder les modifications
        if ($model['write_data']($data)) {
            return $data['promotions'][$target_index];
        }
        
        return null;
    },
    
    // Mettre à jour le statut d'une promotion
    'update_promotion_status' => function(int $promotion_id, string $status) use (&$model) {
        $data = $model['read_data']();
        
        foreach ($data['promotions'] as &$promotion) {
            if ((int)$promotion['id'] === $promotion_id) {
                $promotion['status'] = $status;
                return $model['write_data']($data);
            }
        }
        
        return false;
    },
    
    // Rechercher des promotions par terme de recherche
    'search_promotions' => function($search_term) use (&$model) {
        $promotions = $model['get_all_promotions']();
        
        if (empty($search_term)) {
            return $promotions;
        }
        
        return array_values(array_filter($promotions, function($promotion) use ($search_term) {
            return stripos($promotion['name'], $search_term) !== false;
        }));
    },
    
    // Récupérer la promotion en cours
    'get_promotion_en_cours' => function () use (&$model) {
        $data = $model['read_data']();
        
        foreach ($data['promotions'] as $promotion) {
            if (isset($promotion['etat']) && $promotion['etat'] === 'en_cours') {
                return $promotion;
            }
        }
        
        return null;
    },
    
    // Récupérer la promotion active courante
    'get_current_promotion' => function () use (&$model) {
        $data = $model['read_data']();
        
        // Utiliser array_filter au lieu de foreach
        $active_promotions = array_filter($data['promotions'] ?? [], function ($promotion) {
        
            return $promotion['status'] === Status::ACTIVE->value;
        });
        
        if (empty($active_promotions)) {
            return null;
        }
        
        // Trier par date de début (la plus récente d'abord)
        usort($active_promotions, function ($a, $b) {
            return strtotime($b['date_debut']) - strtotime($a['date_debut']);
        });
        
        return reset($active_promotions);
    },
    
    // Récupérer les statistiques des promotions
    'get_promotions_stats' => function () use (&$model) {
        $data = $model['read_data']();
        
        // Nombre total de promotions
        $total_promotions = count($data['promotions'] ?? []);
        
        // Nombre de promotions actives
        $active_promotions = count(array_filter($data['promotions'] ?? [], function ($promotion) {
            return $promotion['status'] === 'active';
        }));
        
        // Récupérer la promotion courante
        $current_promotion = $model['get_current_promotion']();
        
        // Nombre d'apprenants dans la promotion courante
        $current_promotion_apprenants = 0;
        if ($current_promotion) {
            $current_promotion_apprenants = count(array_filter($data['apprenants'] ?? [], function ($apprenant) use ($current_promotion) {
                return $apprenant['promotion_id'] === $current_promotion['id'];
            }));
        }
        
        // Nombre de référentiels dans la promotion courante
        $current_promotion_referentiels = 0;
        if ($current_promotion) {
            $current_promotion_referentiels = count($current_promotion['referentiels'] ?? []);
        }
        
        return [
            'total_promotions' => $total_promotions,
            'active_promotions' => $active_promotions,
            'current_promotion_apprenants' => $current_promotion_apprenants,
            'current_promotion_referentiels' => $current_promotion_referentiels
        ];
    },
    
    // Assigner des référentiels à une promotion
    'assign_referentiels_to_promotion' => function ($promotion_id, $referentiel_ids) use (&$model) {
        $data = $model['read_data']();
        
        // Trouver l'index de la promotion
        $promotion_indices = array_keys(array_filter($data['promotions'], function($promotion) use ($promotion_id) {
            return $promotion['id'] === $promotion_id;
        }));
        
        if (empty($promotion_indices)) {
            return false;
        }
        
        $promotion_index = reset($promotion_indices);
        
        // Ajouter les référentiels à la promotion
        if (!isset($data['promotions'][$promotion_index]['referentiels'])) {
            $data['promotions'][$promotion_index]['referentiels'] = [];
        }
        
        $data['promotions'][$promotion_index]['referentiels'] = array_unique(
            array_merge($data['promotions'][$promotion_index]['referentiels'], $referentiel_ids)
        );
        
        return $model['write_data']($data);
    },
    
    // Retirer un référentiel d'une promotion
    'unassign_referentiel_from_promotion' => function ($promotion_id, $referentiel_id) use (&$model) {
        $data = $model['read_data']();
        
        // Trouver l'index de la promotion
        $promotion_indices = array_keys(array_filter($data['promotions'], function($promotion) use ($promotion_id) {
            return $promotion['id'] == $promotion_id;
        }));
        
        if (empty($promotion_indices)) {
            return false;
        }
        
        $promotion_index = reset($promotion_indices);
        
        // Vérifier si la promotion a des référentiels
        if (!isset($data['promotions'][$promotion_index]['referentiels'])) {
            return false;
        }
        
        // Filtrer le référentiel à désaffecter
        $data['promotions'][$promotion_index]['referentiels'] = array_values(
            array_filter($data['promotions'][$promotion_index]['referentiels'], function($ref_id) use ($referentiel_id) {
                return $ref_id != $referentiel_id;
            })
        );
        
        return $model['write_data']($data);
    },
    
    // Mettre à jour le statut de fin d'une promotion
    'update_promotion_termination' => function($promotion_id, $is_terminated) use (&$model) {
        $data = $model['read_data']();
        
        foreach ($data['promotions'] as &$promotion) {
            if ($promotion['id'] == $promotion_id) {
                $promotion['status'] = $is_terminated ? 'terminee' : 'active';
                return $model['write_data']($data);
            }
        }
        
        return false;
    }
];