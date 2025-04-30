<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page non trouvée</title>
    <!-- Ajout de Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body, html {
            margin: 0;
            padding: 0;
            height: 100%;
        }

        .error-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start; /* Changé de center à flex-start */
            padding-top: 100px; /* Ajout d'un padding en haut */
            height: 100vh;
            background: linear-gradient(45deg, #1a1a1a, #2c3e50);
            font-family: 'Arial', sans-serif;
            color: white;
            overflow: hidden;
        }

        .error-number {
            font-size: 120px; /* Taille réduite */
            font-weight: bold;
            position: relative;
            animation: glow 2s ease-in-out infinite alternate;
            margin: 0;
        }

        .error-text {
            font-size: 24px;
            margin: 10px 0; /* Marge réduite */
            opacity: 0;
            animation: fadeIn 1s ease-out forwards;
            animation-delay: 0.5s;
        }

        .error-description {
            font-size: 18px;
            color: #bdc3c7;
            text-align: center;
            max-width: 600px;
            margin: 0 20px 30px; /* Ajout d'une marge en bas */
            opacity: 0;
            animation: fadeIn 1s ease-out forwards;
            animation-delay: 1s;
        }

        .back-button, .return-button {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 15px 30px;
            font-size: 16px;
            color: white;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.3s ease;
            opacity: 1;
            position: relative;
        }

        .back-button i, .return-button i {
            font-size: 16px;
        }

        .back-button {
            background: #0E8F7E;
            box-shadow: 0 4px 15px rgba(14, 143, 126, 0.2);
        }

        .return-button {
            background: #4a5568;
            box-shadow: 0 4px 15px rgba(74, 85, 104, 0.2);
        }

        .back-button:hover, .return-button:hover {
            transform: translateY(-3px);
        }

        .back-button:hover {
            background: #10a090;
            box-shadow: 0 6px 20px rgba(14, 143, 126, 0.4);
        }

        .return-button:hover {
            background: #2d3748;
            box-shadow: 0 6px 20px rgba(74, 85, 104, 0.4);
        }

        .back-button:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(14, 143, 126, 0.5);
        }

        .particles {
            position: absolute;
            width: 100%;
            height: 100%;
        }

        .particle {
            position: absolute;
            width: 5px;
            height: 5px;
            background: white;
            border-radius: 50%;
            animation: float 20s infinite linear;
        }

        @keyframes glow {
            from {
                text-shadow: 0 0 10px #fff, 0 0 20px #fff, 0 0 30px #0E8F7E,
                           0 0 40px #0E8F7E, 0 0 50px #0E8F7E, 0 0 60px #0E8F7E;
            }
            to {
                text-shadow: 0 0 20px #fff, 0 0 30px #4FCDB9, 0 0 40px #4FCDB9,
                           0 0 50px #4FCDB9, 0 0 60px #4FCDB9, 0 0 70px #4FCDB9;
            }
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes float {
            0% { transform: translateY(0) translateX(0); opacity: 0; }
            50% { opacity: 1; }
            100% { transform: translateY(-1000px) translateX(100px); opacity: 0; }
        }

        .buttons-container {
            display: flex;
            gap: 20px;
            margin-top: 20px; /* Marge réduite */
            z-index: 1000;
            position: relative;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <h1 class="error-number">404</h1>
        <h2 class="error-text">Page non trouvée</h2>
        <p class="error-description">
            Da nga bakh rek !! Delloul !! 
        </p>
        
        <div class="buttons-container">
            <button onclick="window.history.back()" class="return-button">
                <i class="fas fa-arrow-left"></i> 
                Retour
            </button>
            <a href="?page=dashboard" class="back-button">
                <i class="fas fa-home"></i> 
                Accueil
            </a>
        </div>

        <div class="particles">
            <?php for($i = 0; $i < 50; $i++): ?>
                <div class="particle" style="
                    left: <?= rand(0, 100) ?>%;
                    top: <?= rand(0, 100) ?>%;
                    animation-delay: <?= rand(0, 20) / 10 ?>s;
                "></div>
            <?php endfor; ?>
        </div>
    </div>
</body>
</html>

