
    <style>
        .error-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            background: linear-gradient(45deg, #2c3e50, #c0392b);
            font-family: 'Arial', sans-serif;
            color: white;
            overflow: hidden;
        }

        .error-number {
            font-size: 150px;
            font-weight: bold;
            position: relative;
            animation: pulse 2s ease-in-out infinite;
            margin: 0;
        }

        .error-shield {
            font-size: 60px;
            margin: 20px 0;
            animation: rotate 5s linear infinite;
        }

        .error-text {
            font-size: 24px;
            margin: 20px 0;
            opacity: 0;
            animation: slideIn 1s ease-out forwards;
            animation-delay: 0.5s;
        }

        .error-description {
            font-size: 18px;
            color: #ecf0f1;
            text-align: center;
            max-width: 600px;
            margin: 0 20px;
            opacity: 0;
            animation: slideIn 1s ease-out forwards;
            animation-delay: 1s;
        }

        .back-button {
            margin-top: 40px;
            padding: 15px 30px;
            font-size: 16px;
            color: white;
            background: #e74c3c;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            text-decoration: none;
            transition: transform 0.3s, box-shadow 0.3s;
            opacity: 0;
            animation: slideIn 1s ease-out forwards;
            animation-delay: 1.5s;
        }

        .back-button:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(231, 76, 60, 0.4);
            background: #c0392b;
        }

        .security-grid {
            position: absolute;
            width: 100%;
            height: 100%;
            background-image: linear-gradient(rgba(255, 255, 255, 0.1) 1px, transparent 1px),
                            linear-gradient(90deg, rgba(255, 255, 255, 0.1) 1px, transparent 1px);
            background-size: 20px 20px;
            animation: moveGrid 20s linear infinite;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }

        @keyframes rotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        @keyframes slideIn {
            from { opacity: 0; transform: translateX(-50px); }
            to { opacity: 1; transform: translateX(0); }
        }

        @keyframes moveGrid {
            from { transform: translateX(0) translateY(0); }
            to { transform: translateX(-20px) translateY(-20px); }
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="security-grid"></div>
        <h1 class="error-number">403</h1>
        <div class="error-shield">🛡️</div>
        <h2 class="error-text">Accès Interdit</h2>
        <p class="error-description">
            Désolé, vous n'avez pas l'autorisation d'accéder à cette zone sécurisée.
            Veuillez vous connecter avec les droits appropriés.
        </p>
        <a href="?page=login" class="back-button">
            Retourner à la connexion
        </a>
    </div>
