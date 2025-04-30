<?php

namespace App\Models;

require_once __DIR__ . '/../enums/path.enum.php';
require_once __DIR__ . '/../enums/status.enum.php';
require_once __DIR__ . '/../enums/profile.enum.php';

use App\Enums;
use App\Enums\Status; // Ajout de cette ligne

// Collection de toutes les fonctions modèles pour l'application
$model = [
    // Fonctions de base pour manipuler les données
    'read_data' => function () {
        if (!file_exists(Enums\DATA_PATH)) {
            // Si le fichier n'existe pas, on renvoie une structure par défaut
            return [
                'users' => [],
                'promotions' => [],
                'referentiels' => [],
                'apprenants' => []
            ];
        }
        
        $json_data = file_get_contents(Enums\DATA_PATH);
        $data = json_decode($json_data, true);
        
        if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
            // En cas d'erreur de décodage JSON
            return [
                'users' => [],
                'promotions' => [],
                'referentiels' => [],
                'apprenants' => []
            ];
        }
        
        return $data;
    },
    
    'write_data' => function ($data) {
        // Vérifier si le dossier data existe, sinon le créer
        $data_dir = dirname(Enums\DATA_PATH);
        if (!is_dir($data_dir)) {
            mkdir($data_dir, 0777, true);
        }
        
        $json_data = json_encode($data, JSON_PRETTY_PRINT);
        return file_put_contents(Enums\DATA_PATH, $json_data) !== false;
    },
    
    'generate_id' => function () {
        return uniqid();
    },
    
    // Fonctions d'authentification
    'authenticate' => function ($email, $password) use (&$model) {
        $data = $model['read_data']();
        
        // Utiliser array_filter au lieu de foreach
        $filtered_users = array_filter($data['users'], function ($user) use ($email, $password) {
            return $user['email'] === $email && $user['password'] === $password;
        });
        
        // Si aucun utilisateur ne correspond
        if (empty($filtered_users)) {
            return null;
        }
        
        // Récupérer le premier utilisateur qui correspond
        return reset($filtered_users);
    },
    
    'get_user_by_email' => function ($email) use (&$model) {
        $data = $model['read_data']();
        
        // Utiliser array_filter au lieu de foreach
        $filtered_users = array_filter($data['users'], function ($user) use ($email) {
            return $user['email'] === $email;
        });
        
        // Si aucun utilisateur ne correspond
        if (empty($filtered_users)) {
            return null;
        }
        
        // Récupérer le premier utilisateur qui correspond
        return reset($filtered_users);
    },
    
    'get_user_by_id' => function ($user_id) use (&$model) {
        $data = $model['read_data']();
        
        // Utiliser array_filter au lieu de foreach
        $filtered_users = array_filter($data['users'], function ($user) use ($user_id) {
            return $user['id'] === $user_id;
        });
        
        // Si aucun utilisateur ne correspond
        if (empty($filtered_users)) {
            return null;
        }
        
        // Récupérer le premier utilisateur qui correspond
        return reset($filtered_users);
    },
    
    'change_password' => function ($user_id, $new_password) use (&$model) {
        $data = $model['read_data']();
        
        $user_indices = array_keys(array_filter($data['users'], function($user) use ($user_id) {
            return $user['id'] === $user_id;
        }));
        
        if (empty($user_indices)) {
            return false;
        }
        
        $user_index = reset($user_indices);
        
        // Mettre à jour le mot de passe (sans cryptage)
        $data['users'][$user_index]['password'] = $new_password;
        
        // Sauvegarder les modifications
        return $model['write_data']($data);
    },
    
    // Fonctions pour les promotions
    'get_all_promotions' => function () use (&$model) {
        $data = $model['read_data']();
        return $data['promotions'] ?? [];
    },
    
    'get_promotion_by_id' => function ($id) use (&$model) {
        $data = $model['read_data']();
        
        // Utiliser array_filter au lieu de foreach
        $filtered_promotions = array_filter($data['promotions'] ?? [], function ($promotion) use ($id) {
            return $promotion['id'] === $id;
        });
        
        return !empty($filtered_promotions) ? reset($filtered_promotions) : null;
    },
    
    'promotion_name_exists' => function(string $name) use (&$model): bool {
        $data = $model['read_data']();
        
        foreach ($data['promotions'] as $promotion) {
            if (strtolower($promotion['name']) === strtolower($name)) {
                return true;
            }
        }
        
        return false;
    },
    
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
    
    'search_promotions' => function($search_term) use (&$model) {
        $promotions = $model['get_all_promotions']();
        
        if (empty($search_term)) {
            return $promotions;
        }
        
        return array_values(array_filter($promotions, function($promotion) use ($search_term) {
            return stripos($promotion['name'], $search_term) !== false;
        }));
    },
    
    // Ajouter cette fonction au tableau $model
    'get_promotion_en_cours' => function () use (&$model) {
        $data = $model['read_data']();
        
        foreach ($data['promotions'] as $promotion) {
            if (isset($promotion['etat']) && $promotion['etat'] === 'en_cours') {
                return $promotion;
            }
        }
        
        return null;
    },
    
    // Fonctions pour les référentiels
    'get_all_referentiels' => function () use (&$model) {
        $data = $model['read_data']();
        return $data['referentiels'] ?? [];
    },
    
    'get_referentiel_by_id' => function ($id) use (&$model) {
        $data = $model['read_data']();
        
        // Utiliser array_filter au lieu de foreach
        $filtered_referentiels = array_filter($data['referentiels'] ?? [], function ($referentiel) use ($id) {
            return $referentiel['id'] === $id;
        });
        
        return !empty($filtered_referentiels) ? reset($filtered_referentiels) : null;
    },
    
    'referentiel_name_exists' => function ($name, $exclude_id = null) use (&$model) {
        $data = $model['read_data']();
        
        // Utiliser array_filter au lieu de foreach
        $filtered_referentiels = array_filter($data['referentiels'] ?? [], function ($referentiel) use ($name, $exclude_id) {
            return strtolower($referentiel['name']) === strtolower($name) && ($exclude_id === null || $referentiel['id'] !== $exclude_id);
        });
        
        return !empty($filtered_referentiels);
    },
    
    'create_referentiel' => function ($referentiel_data) use (&$model) {
        $data = $model['read_data']();
        
        // Générer un ID unique
        $referentiel_data['id'] = uniqid();
        
        // Ajouter le référentiel à la liste
        $data['referentiels'][] = $referentiel_data;
        
        // Sauvegarder les modifications
        return $model['write_data']($data);
    },
    
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
    
    'get_referentiel_by_name' => function($name) use (&$model) {
        $data = $model['read_data']();
        foreach ($data['referentiels'] as $ref) {
            if (strtolower($ref['name']) === strtolower($name)) {
                return $ref;
            }
        }
        return null;
    },
    
    // Fonction pour récupérer la promotion active courante
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
    
    // Statistiques diverses pour le tableau de bord
    'get_promotions_stats' => function () use (&$model) {
        $data = $model['read_data']();
        
        // Nombre total de promotions
        $total_promotions = count($data['promotions'] ?? []);
        
        // Nombre de promotions actives
        $active_promotions = count(array_filter($data['promotions'] ?? [], function ($promotion) {
            return $promotion['status'] === Enums\ACTIVE;
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
    
    // Fonctions pour les apprenants
    // 'get_all_apprenants' => function () use (&$model) {
    //     $data = $model['read_data']();
    //     return $data['apprenants'] ?? [];
    // },
    
    'get_apprenants_by_promotion' => function ($promotion_id) use (&$model) {
        $data = $model['read_data']();
        
        // Filtrer les apprenants par promotion
        $apprenants = array_filter($data['apprenants'] ?? [], function ($apprenant) use ($promotion_id) {
            return $apprenant['promotion_id'] === $promotion_id;
        });
        
        return array_values($apprenants);
    },
    
    'get_apprenant_by_id' => function ($id) use (&$model) {
        $data = $model['read_data']();
        
        // Filtrer les apprenants par ID
        $filtered_apprenants = array_filter($data['apprenants'] ?? [], function ($apprenant) use ($id) {
            return $apprenant['id'] === $id;
        });
        
        return !empty($filtered_apprenants) ? reset($filtered_apprenants) : null;
    },
    
    'get_apprenant_by_matricule' => function ($matricule) use (&$model) {
        $data = $model['read_data']();
        
        // Filtrer les apprenants par matricule
        $filtered_apprenants = array_filter($data['apprenants'] ?? [], function ($apprenant) use ($matricule) {
            return $apprenant['matricule'] === $matricule;
        });
        
        return !empty($filtered_apprenants) ? reset($filtered_apprenants) : null;
    },
    
    'generate_matricule' => function () use (&$model) {
        $data = $model['read_data']();
        $year = date('Y');
        $count = count($data['apprenants'] ?? []) + 1;
        
        return 'ODC-' . $year . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
    },
    
    'get_statistics' => function() use (&$model) {
        $data = $model['read_data']();
        
        // Trouver la promotion active
        $active_promotions = array_filter($data['promotions'], function($promotion) {
            return $promotion['status'] === 'active';
        });
        $active_promotion = reset($active_promotions);
        
        // Calculer les statistiques
        $stats = [
            'active_learners' => 0,
            'total_referentials' => count($data['referentiels'] ?? []),
            'active_promotions' => count($active_promotions),
            'total_promotions' => count($data['promotions'] ?? [])
        ];
        
        // Ajouter le nombre d'apprenants de la promotion active
        if ($active_promotion) {
            $stats['active_learners'] = count($active_promotion['apprenants'] ?? []);
        }
        
        return $stats;
    },
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
'update_promotion_termination' => function($promotion_id, $is_terminated) use (&$model) {
    $data = $model['read_data']();
    
    foreach ($data['promotions'] as &$promotion) {
        if ($promotion['id'] == $promotion_id) {
            $promotion['status'] = $is_terminated ? 'terminee' : 'active';
            return $model['write_data']($data);
        }
    }
    
    return false;
},

// Fonctions à ajouter au tableau $model dans app/models/model.php

// Récupérer tous les apprenants
'get_all_apprenants' => function () use (&$model) {
    $data = $model['read_data']();
    return $data['apprenants'] ?? [];
},

// Récupérer un apprenant par son ID
'get_apprenant_by_id' => function ($id) use (&$model) {
    $data = $model['read_data']();
    
    // Filtrer par ID
    $filtered_apprenants = array_filter($data['apprenants'] ?? [], function ($apprenant) use ($id) {
        return $apprenant['id'] === $id;
    });
    
    return !empty($filtered_apprenants) ? reset($filtered_apprenants) : null;
},

// Récupérer un apprenant par son matricule
'get_apprenant_by_matricule' => function ($matricule) use (&$model) {
    $data = $model['read_data']();
    
    // Filtrer par matricule
    $filtered_apprenants = array_filter($data['apprenants'] ?? [], function ($apprenant) use ($matricule) {
        return $apprenant['matricule'] === $matricule;
    });
    
    return !empty($filtered_apprenants) ? reset($filtered_apprenants) : null;
},

// Récupérer un apprenant par son email
'get_apprenant_by_email' => function ($email) use (&$model) {
    $data = $model['read_data']();
    
    // Filtrer par email
    $filtered_apprenants = array_filter($data['apprenants'] ?? [], function ($apprenant) use ($email) {
        return $apprenant['email'] === $email;
    });
    
    return !empty($filtered_apprenants) ? reset($filtered_apprenants) : null;
},

// Vérifier si un email existe déjà
'email_exists' => function ($email) use (&$model) {
    $data = $model['read_data']();
    
    foreach ($data['apprenants'] as $apprenant) {
        if ($apprenant['email'] === $email) {
            return true;
        }
    }
    
    return false;
},

// Vérifier les identifiants d'un apprenant
'authenticate_apprenant' => function ($email, $password) use (&$model) {
    $data = $model['read_data']();
    
    foreach ($data['apprenants'] as $apprenant) {
        if ($apprenant['email'] === $email) {
            // Si le mot de passe est hashé
            if (isset($apprenant['password']) && password_verify($password, $apprenant['password'])) {
                return $apprenant;
            }
            // Si le mot de passe est en clair (à éviter en production)
            else if (isset($apprenant['password']) && $apprenant['password'] === $password) {
                return $apprenant;
            }
        }
    }
    
    return null;
},

// Créer un nouvel apprenant
'create_apprenant' => function ($apprenant_data) use (&$model) {
    $data = $model['read_data']();
    
    // Ajouter l'apprenant
    $data['apprenants'][] = $apprenant_data;
    
    // Mettre à jour la promotion en ajoutant l'apprenant
    foreach ($data['promotions'] as &$promotion) {
        if ($promotion['id'] == $apprenant_data['promotion_id']) {
            if (!isset($promotion['apprenants'])) {
                $promotion['apprenants'] = [];
            }
            
            $promotion['apprenants'][] = [
                'id' => $apprenant_data['id'],
                'name' => $apprenant_data['nom'] . ' ' . $apprenant_data['prenom']
            ];
            
            break;
        }
    }
    
    // Mettre à jour le fichier de données
    return $model['write_data']($data);
},

// Mettre à jour un apprenant existant
'update_apprenant' => function ($id, $apprenant_data) use (&$model) {
    $data = $model['read_data']();
    
    // Trouver l'index de l'apprenant
    $apprenant_indices = array_keys(array_filter($data['apprenants'] ?? [], function ($apprenant) use ($id) {
        return $apprenant['id'] === $id;
    }));
    
    if (empty($apprenant_indices)) {
        return false;
    }
    
    $apprenant_index = reset($apprenant_indices);
    $old_apprenant = $data['apprenants'][$apprenant_index];
    
    // Mettre à jour les données de l'apprenant
    $data['apprenants'][$apprenant_index] = array_merge(
        $old_apprenant,
        $apprenant_data
    );
    
    // Si la promotion a changé, mettre à jour les tableaux de promotion
    if (isset($apprenant_data['promotion_id']) && $old_apprenant['promotion_id'] !== $apprenant_data['promotion_id']) {
        // Supprimer l'apprenant de l'ancienne promotion
        foreach ($data['promotions'] as &$promotion) {
            if ($promotion['id'] == $old_apprenant['promotion_id']) {
                if (isset($promotion['apprenants'])) {
                    $promotion['apprenants'] = array_filter($promotion['apprenants'], function ($app) use ($id) {
                        return $app['id'] !== $id;
                    });
                }
                break;
            }
        }
        
        // Ajouter l'apprenant à la nouvelle promotion
        foreach ($data['promotions'] as &$promotion) {
            if ($promotion['id'] == $apprenant_data['promotion_id']) {
                if (!isset($promotion['apprenants'])) {
                    $promotion['apprenants'] = [];
                }
                
                $promotion['apprenants'][] = [
                    'id' => $id,
                    'name' => $apprenant_data['nom'] . ' ' . $apprenant_data['prenom']
                ];
                
                break;
            }
        }
    }
    
    // Mettre à jour le fichier de données
    return $model['write_data']($data);
},

// Supprimer un apprenant
'delete_apprenant' => function ($id) use (&$model) {
    $data = $model['read_data']();
    
    // Trouver l'apprenant à supprimer
    $apprenant = null;
    foreach ($data['apprenants'] as $index => $app) {
        if ($app['id'] === $id) {
            $apprenant = $app;
            unset($data['apprenants'][$index]);
            break;
        }
    }
    
    if (!$apprenant) {
        return false;
    }
    
    // Supprimer l'apprenant de sa promotion
    foreach ($data['promotions'] as &$promotion) {
        if ($promotion['id'] == $apprenant['promotion_id']) {
            if (isset($promotion['apprenants'])) {
                $promotion['apprenants'] = array_filter($promotion['apprenants'], function ($app) use ($id) {
                    return $app['id'] !== $id;
                });
                
                // Réindexer le tableau
                $promotion['apprenants'] = array_values($promotion['apprenants']);
            }
            break;
        }
    }
    
    // Réindexer le tableau des apprenants
    $data['apprenants'] = array_values($data['apprenants']);
    
    // Mettre à jour le fichier de données
    return $model['write_data']($data);
},

// Compter les apprenants par référentiel
'count_apprenants_by_referentiel' => function ($referentiel_id) use (&$model) {
    $data = $model['read_data']();
    
    $count = 0;
    foreach ($data['apprenants'] as $apprenant) {
        if ($apprenant['referentiel_id'] === $referentiel_id) {
            $count++;
        }
    }
    
    return $count;
},

// Générer un matricule unique
'generate_matricule' => function () use (&$model) {
    $data = $model['read_data']();
    
    // Si la collection d'apprenants existe et n'est pas vide
    if (isset($data['apprenants']) && !empty($data['apprenants'])) {
        // Trouver le dernier matricule
        $max_matricule = 0;
        foreach ($data['apprenants'] as $apprenant) {
            if (isset($apprenant['matricule'])) {
                $matricule_int = (int)$apprenant['matricule'];
                if ($matricule_int > $max_matricule) {
                    $max_matricule = $matricule_int;
                }
            }
        }
        
        // Incrémenter le matricule
        return (string)($max_matricule + 1);
    }
    
    // Si aucun apprenant n'existe encore, commencer à 1058215 (selon l'exemple de l'image)
    return "1058215";
},

// Récupérer les apprenants par référentiel
'get_apprenants_by_referentiel' => function ($referentiel_id) use (&$model) {
    $data = $model['read_data']();
    
    // Filtrer les apprenants par référentiel
    $filtered_apprenants = array_filter($data['apprenants'] ?? [], function ($apprenant) use ($referentiel_id) {
        return $apprenant['referentiel_id'] === $referentiel_id;
    });
    
    return array_values($filtered_apprenants);
},

// Récupérer les apprenants par statut
'get_apprenants_by_status' => function ($status) use (&$model) {
    $data = $model['read_data']();
    
    // Filtrer les apprenants par statut
    $filtered_apprenants = array_filter($data['apprenants'] ?? [], function ($apprenant) use ($status) {
        return $apprenant['statut'] === $status;
    });
    
    return array_values($filtered_apprenants);
},

// Récupérer les apprenants par promotion
'get_apprenants_by_promotion' => function ($promotion_id) use (&$model) {
    $data = $model['read_data']();
    
    // Filtrer les apprenants par promotion
    $filtered_apprenants = array_filter($data['apprenants'] ?? [], function ($apprenant) use ($promotion_id) {
        return $apprenant['promotion_id'] == $promotion_id;
    });
    
    return array_values($filtered_apprenants);
}
];