<?php

namespace App\Models;

// Fonctions pour la gestion des apprenants
$apprenant_model = [
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
        
        foreach ($data['apprenants'] as $apprenant) {
            if (isset($apprenant['email']) && $apprenant['email'] === $email) {
                return $apprenant;
            }
        }
        
        return null;
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
    'create_apprenant' => function ($data) use (&$model) {
        $current_data = $model['read_data']();
        
        // Générer un ID unique
        $id = uniqid();
        
        // Générer un matricule
        $matricule = $model['generate_matricule']();
        
        // Mot de passe par défaut
        $default_password = 'passer123';
        $hashed_password = password_hash($default_password, PASSWORD_DEFAULT);
        
        // Préparer les données de l'apprenant
        $apprenant = [
            'id' => $id,
            'matricule' => $matricule,
            'nom' => $data['nom'],
            'prenom' => $data['prenom'],
            'photo' => $data['photo'] ?? 'assets/images/apprenants/default.jpg',
            'adresse' => $data['adresse'],
            'telephone' => $data['telephone'],
            'email' => $data['email'],
            'date_naissance' => $data['date_naissance'],
            'lieu_naissance' => $data['lieu_naissance'],
            'promotion_id' => $data['promotion_id'] ?? null,
            'referentiel_id' => $data['referentiel_id'],
            'statut' => 'actif',
            'date_inscription' => date('Y-m-d'),
            'password' => $hashed_password,
            'password_change_required' => true
        ];
        
        // Ajouter l'apprenant aux données
        $current_data['apprenants'][] = $apprenant;
        
        // Sauvegarder les données
        $result = $model['write_data']($current_data);
        
        if ($result) {
            // Récupérer les informations du référentiel
            $referentiel = null;
            foreach ($current_data['referentiels'] as $ref) {
                if ($ref['id'] == $data['referentiel_id']) {
                    $referentiel = $ref;
                    break;
                }
            }
            
            // Envoyer un email avec les identifiants
            global $mail_services;
            if ($referentiel) {
                $mail_services['send_credentials_email']($apprenant, $referentiel, $default_password);
            }
            
            return $id;
        }
        
        return false;
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
    
    // Mettre à jour le mot de passe d'un apprenant
    'update_apprenant_password' => function ($id, $new_password) use (&$model) {
        $data = $model['read_data']();
        
        foreach ($data['apprenants'] as &$apprenant) {
            if ($apprenant['id'] === $id) {
                $apprenant['password'] = password_hash($new_password, PASSWORD_DEFAULT);
                $apprenant['password_change_required'] = false;
                return $model['write_data']($data);
            }
        }
        
        return false;
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