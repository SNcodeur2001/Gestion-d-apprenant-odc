<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Apprenants ODC</title>
    <link rel="stylesheet" href="assets/css/dashboard.css">
    <link rel="stylesheet" href="/assets/css/promo.css?v=<?= time() ?>" />
    <link rel="stylesheet" href="assets/css/modal.css">
    <link rel="stylesheet" href="assets/css/modal-form.css">
    <link rel="stylesheet" href="assets/css/add-promotion.css">
    <link rel="stylesheet" href="/assets/css/add-referentiel.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body>
    <div class="app-container">
        <!-- Sidebar / Menu latéral -->
        <div class="sidebar">
            <div class="logo-container">
                <div class="logo">
                    <img style="width: 100px; display: block; margin: auto;" src="assets/images/sonatel-logo.png" alt="Sonatel Academy">
                    <div class="logo-text">

                    </div>
                </div>
                <p class="promotion">
                    <?php
                    // Récupérer la promotion active
                    global $model;
                    $active_promotion = $model['get_current_promotion']();
                    echo $active_promotion ? '' . htmlspecialchars($active_promotion['name']) : 'Aucune promotion active';
                    ?>
                </p>
            </div>

            <!-- Menu de navigation -->
            <nav class="main-nav">
                <ul>
                    <li class="<?= isset($active_menu) && $active_menu === 'dashboard' ? 'active' : '' ?>">
                        <a href="?page=dashboard">
                            <span class="icon"></span>
                            <span>Tableau de bord</span>
                        </a>
                    </li>
                    <li class="<?= isset($active_menu) && $active_menu === 'promotions' ? 'active' : '' ?>">
                        <a href="?page=promotions">
                            <span class="icon"></span>
                            <span>Promotions</span>
                        </a>
                    </li>
                    <li class="<?= isset($active_menu) && $active_menu === 'referentiels' ? 'active' : '' ?>">
                        <a href="?page=referentiels">
                            <span class="icon"></span>
                            <span>Référentiels</span>
                        </a>
                    </li>
                    <li class="<?= isset($active_menu) && $active_menu === 'apprenants' ? 'active' : '' ?>">
                        <a href="?page=apprenants">
                            <span class="icon"></span>
                            <span>Apprenants</span>
                        </a>
                    </li>
                    <li class="<?= isset($active_menu) && $active_menu === 'presences' ? 'active' : '' ?>">
                        <a href="?page=presences">
                            <span class="icon"></span>
                            <span>Gestion des présences</span>
                        </a>
                    </li>
                    <li class="<?= isset($active_menu) && $active_menu === 'kits' ? 'active' : '' ?>">
                        <a href="?page=kits">
                            <span class="icon"></span>
                            <span>Kits & Laptops</span>
                        </a>
                    </li>
                    <li class="<?= isset($active_menu) && $active_menu === 'rapports' ? 'active' : '' ?>">
                        <a href="?page=rapports">
                            <span class="icon"></span>
                            <span>Rapports & Stats</span>
                        </a>
                    </li>
                </ul>
            </nav>
            <div id="bouton">
                <a href="?page=logout">
                    <button>Déconnexion</button>
                </a>
            </div>
        </div>

        <!-- Contenu principal -->
        <div class="main-content">
            <!-- Header / Entête -->
            <header class="top-header">
                <div class="search-bar">
                    <i id="icon" class="fa-solid fa-magnifying-glass"></i>
                    <input id="inp" type="text" placeholder="Rechercher...">
                </div>

                <div class="user-menu">
                    <div class="notifications">
                        <i class="fa-regular fa-bell"></i>
                    </div>
                    <div class="user-profile">
                        <div class="user-info">
                            <span class="user-name"><?= htmlspecialchars($_SESSION['user']['name']) ?></span>
                            <span class="user-role"><?= htmlspecialchars($_SESSION['user']['profile']) ?></span>
                        </div>
                        <div class="avatar">
                            <img src="<?= $_SESSION['user']['image'] ?? 'assets/images/1.jpeg' ?>"
                                alt="Photo de profil"
                                onerror="this.src='assets/images/1.jpeg'">
                        </div>
                    </div>
                </div>
            </header>

            <!-- Messages flash -->
            <?php if (isset($flash) && $flash): ?>
                <div class="alert alert-<?= $flash['type'] ?>">
                    <?= $flash['message'] ?>
                </div>
            <?php endif; ?>

            <!-- Contenu de la page -->
            <div class="page-content">
                <?= $content ?>
            </div>
        </div>
    </div>
</body>

</html>