<?php

namespace App\Models;

require_once __DIR__ . '/../enums/path.enum.php';

use App\Enums;

// Fonctions de base pour manipuler les données
$model_base = [
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
    }
];