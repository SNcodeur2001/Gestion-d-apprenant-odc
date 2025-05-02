<!-- app/views/apprenant/list.html.php -->
<?php
// Récupération des paramètres de filtrage
$search = isset($_GET['search']) ? $_GET['search'] : '';
$classe_filter = isset($_GETs['classe_filter']) ? $_GET['classe_filter'] : '';
$status_filter = isset($_GET['status_filter']) ? $_GET['status_filter'] : '';
$active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'apprenants';
?>

<div class="apprenants-container">

<div class="apprenants-header">
        <div class="header-title">
            <h1>Apprenants</h1>
            <span class="counter"><?= $total_apprenants ?> apprenants</span>
        </div>
        
        <div class="apprenants-actions">
            <div class="search-box">
                <form action="?page=apprenants" method="GET">
                    <input type="hidden" name="page" value="apprenants">
                    <i class="search-icon">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="11" cy="11" r="8"></circle>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        </svg>
                    </i>
                    <input type="text" name="search" placeholder="Rechercher..." value="<?= htmlspecialchars($search) ?>">
                    <?php if (!empty($classe_filter)) { ?>
                        <input type="hidden" name="classe_filter" value="<?= htmlspecialchars($classe_filter) ?>">
                    <?php } ?>
                    <?php if (!empty($status_filter)) { ?>
                        <input type="hidden" name="status_filter" value="<?= htmlspecialchars($status_filter) ?>">
                    <?php } ?>
                    <input type="hidden" name="tab" value="<?= htmlspecialchars($active_tab) ?>">
                    <button type="submit" style="display:none;"></button>
                </form>
            </div>
            
            <div class="filter-dropdown">
                <form id="classe-filter-form" action="?page=apprenants" method="GET">
                    <input type="hidden" name="page" value="apprenants">
                    <?php if (!empty($search)) { ?>
                        <input type="hidden" name="search" value="<?= htmlspecialchars($search) ?>">
                    <?php } ?>
                    <?php if (!empty($status_filter)) { ?>
                        <input type="hidden" name="status_filter" value="<?= htmlspecialchars($status_filter) ?>">
                    <?php } ?>
                    <input type="hidden" name="tab" value="<?= htmlspecialchars($active_tab) ?>">
                    <select name="classe_filter" aria-label="Filtre par classe" onchange="document.getElementById('classe-filter-form').submit()">
                        <option value="">Filtre par classe</option>
                        <?php foreach ($referentiels as $ref): ?>
                            <option value="<?= $ref['id'] ?>" <?= $classe_filter === $ref['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($ref['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </form>
            </div>
            
            <div class="filter-dropdown">
                <form id="status-filter-form" action="?page=apprenants" method="GET">
                    <input type="hidden" name="page" value="apprenants">
                    <?php if (!empty($search)) { ?>
                        <input type="hidden" name="search" value="<?= htmlspecialchars($search) ?>">
                    <?php } ?>
                    <?php if (!empty($classe_filter)) { ?>
                        <input type="hidden" name="classe_filter" value="<?= htmlspecialchars($classe_filter) ?>">
                    <?php } ?>
                    <input type="hidden" name="tab" value="<?= htmlspecialchars($active_tab) ?>">
                    <select name="status_filter" aria-label="Filtre par status" onchange="document.getElementById('status-filter-form').submit()">
                        <option value="">Filtre par status</option>
                        <option value="actif" <?= $status_filter === 'actif' ? 'selected' : '' ?>>Actif</option>
                        <option value="remplacé" <?= $status_filter === 'remplacé' ? 'selected' : '' ?>>Remplacé</option>
                    </select>
                </form>
            </div>
            
            <div class="custom-dropdown">
                <button class="dropdown-btn">
                    <i class="fas fa-download"></i> Télécharger
                </button>
                <div class="dropdown-content">
                    <a href="?page=download-apprenants-list&format=excel">Format Excel</a>
                    <a href="?page=download-apprenants-list&format=pdf">Format PDF</a>
                    <a href="?page=download-apprenants-list&format=csv">Format CSV</a>
                </div>
            </div>

            <a href="?page=import-apprenants" class="import-button">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                    <polyline points="17 8 12 3 7 8"></polyline>
                    <line x1="12" y1="3" x2="12" y2="15"></line>
                </svg>
                Importer des apprenants
            </a>
            
            <a href="?page=add-apprenant" class="add-button">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M20 12h-8m-4 0H4m8 4V8"></path>
                </svg>
                Ajouter apprenant
            </a>
        </div>
    </div>

    <div class="tabs-container">
        <div class="tabs">
            <a href="?page=apprenants&tab=apprenants" class="tab <?= $active_tab === 'apprenants' ? 'active' : '' ?>">
                Liste des apprenants
            </a>
            <a href="?page=apprenants&tab=waiting" class="tab <?= $active_tab === 'waiting' ? 'active' : '' ?>">
                Liste d'attente <?php if(!empty($waiting_list)): ?><span class="badge"><?= count($waiting_list) ?></span><?php endif; ?>
            </a>
        </div>
    </div>

    <?php if($active_tab === 'apprenants'): ?>
    <!-- AFFICHAGE DES APPRENANTS -->
    <div class="table-container">
        <table class="apprenants-table">
            <thead>
                <tr>
                    <th class="photo-col">Photo</th>
                    <th class="matricule-col">Matricule</th>
                    <th class="nom-col">Nom Complet</th>
                    <th class="adresse-col">Adresse</th>
                    <th class="telephone-col">Téléphone</th>
                    <th class="referentiel-col">Référentiel</th>
                    <th class="statut-col">Statut</th>
                    <th class="actions-col">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($apprenants)): ?>
                    <tr>
                        <td colspan="8" class="no-data">Aucun apprenant trouvé</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($apprenants as $apprenant): ?>
                        <tr>
                            <td class="photo-col">
                                <img src="<?= htmlspecialchars($apprenant['photo']) ?>" alt="Photo de <?= htmlspecialchars($apprenant['prenom']) ?>" class="apprenant-photo">
                            </td>
                            <td class="matricule-col"><?= htmlspecialchars($apprenant['matricule']) ?></td>
                            <td class="nom-col"><?= htmlspecialchars($apprenant['nom'] . ' ' . $apprenant['prenom']) ?></td>
                            <td class="adresse-col"><?= htmlspecialchars($apprenant['adresse']) ?></td>
                            <td class="telephone-col"><?= htmlspecialchars($apprenant['telephone']) ?></td>
                            <td class="referentiel-col">
                                <?php
                                $referentiel_id = $apprenant['referentiel_id'];
                                $referentiel_name = isset($referentiels_map[$referentiel_id])
                                    ? $referentiels_map[$referentiel_id]
                                    : $referentiel_id;
                                
                                // Déterminer la classe CSS en fonction du nom du référentiel
                                $badge_class = '';
                                if (stripos($referentiel_name, 'WEB') !== false || stripos($referentiel_name, 'MOBILE') !== false) {
                                    $badge_class = 'dev-web';
                                } elseif (stripos($referentiel_name, 'DIG') !== false) {
                                    $badge_class = 'ref-dig';
                                } elseif (stripos($referentiel_name, 'DATA') !== false) {
                                    $badge_class = 'dev-data';
                                } elseif (stripos($referentiel_name, 'AWS') !== false) {
                                    $badge_class = 'aws';
                                } elseif (stripos($referentiel_name, 'HACK') !== false) {
                                    $badge_class = 'hackeuse';
                                }
                                ?>
                                <span class="ref-badge <?= $badge_class ?>"><?= htmlspecialchars($referentiel_name) ?></span>
                            </td>
                            <td class="statut-col">
                                <span class="statut-badge <?= $apprenant['statut'] === 'actif' ? 'actif' : 'remplace' ?>">
                                    <?= ucfirst($apprenant['statut']) ?>
                                </span>
                            </td>
                            <td class="actions-col">
                                <div class="actions-menu">
                                    <div class="action-dots">
                                        <svg class="dots-icon" width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                            <circle cx="12" cy="5" r="2"></circle>
                                            <circle cx="12" cy="12" r="2"></circle>
                                            <circle cx="12" cy="19" r="2"></circle>
                                        </svg>
                                    </div>
                                    <div class="actions-dropdown">
                                        <a href="?page=view-apprenant&id=<?= $apprenant['id'] ?>" class="dropdown-item">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M1 12s4-8 11-8 11 8 11-8-4 8-11-8-11-8z"></path>
                                                <circle cx="12" cy="12" r="3"></circle>
                                            </svg>
                                            Voir
                                        </a>
                                        <a href="?page=edit-apprenant&id=<?= $apprenant['id'] ?>" class="dropdown-item">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                            </svg>
                                            Modifier
                                        </a>
                                        <?php if ($apprenant['statut'] === 'actif') { ?>
                                            <a href="?page=replace-apprenant&id=<?= $apprenant['id'] ?>" class="dropdown-item">
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path d="M23 4v6h-6"></path>
                                                    <path d="M1 20v-6h6"></path>
                                                    <path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path>
                                                </svg>
                                                Remplacer
                                            </a>
                                        <?php } ?>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="pagination-footer">
        <div class="pagination-info">
            <span>Apprenants/page</span>
            <select>
                <option>10</option>
                <option>20</option>
                <option>30</option>
                <option>50</option>
            </select>
        </div>
        <div class="pagination-count">
            <?php 
            $start = ($current_page - 1) * 10 + 1;
            $end = min($start + count($apprenants) - 1, $total_apprenants);
            echo "$start à $end apprenants pour $total_apprenants";
            ?>
        </div>
        <div class="pagination-controls">
            <?php if ($current_page > 1): ?>
                <a href="?page=apprenants&page_num=<?= $current_page - 1 ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?><?= !empty($classe_filter) ? '&classe_filter=' . urlencode($classe_filter) : '' ?><?= !empty($status_filter) ? '&status_filter=' . urlencode($status_filter) : '' ?>&tab=<?= $active_tab ?>" class="pagination-btn prev">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="15 18 9 12 15 6"></polyline>
                    </svg>
                </a>
            <?php else: ?>
                <button class="pagination-btn prev" disabled>
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="15 18 9 12 15 6"></polyline>
                    </svg>
                </button>
            <?php endif; ?>
            
            <?php
            // Afficher les boutons de pagination
            $max_visible_pages = 3; // Nombre de pages visibles avant l'ellipse
            
            for ($i = 1; $i <= $total_pages; $i++) {
                // Afficher les premières pages, la page courante et les dernières pages
                if ($i <= $max_visible_pages || $i > $total_pages - $max_visible_pages || abs($i - $current_page) <= $max_visible_pages) {
                    if ($i == $current_page) {
                        echo "<button class='pagination-btn page-number active'>$i</button>";
                    } else {
                        echo "<a href='?page=apprenants&page_num=$i" . (!empty($search) ? '&search=' . urlencode($search) : '') . (!empty($classe_filter) ? '&classe_filter=' . urlencode($classe_filter) : '') . (!empty($status_filter) ? '&status_filter=' . urlencode($status_filter) : '') . "&tab=$active_tab' class='pagination-btn page-number'>$i</a>";
                    }
                } elseif ($i == $max_visible_pages + 1 || $i == $total_pages - $max_visible_pages) {
                    echo "<span class='pagination-ellipsis'>...</span>";
                }
            }
            ?>
            
            <?php if ($current_page < $total_pages): ?>
                <a href="?page=apprenants&page_num=<?= $current_page + 1 ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?><?= !empty($classe_filter) ? '&classe_filter=' . urlencode($classe_filter) : '' ?><?= !empty($status_filter) ? '&status_filter=' . urlencode($status_filter) : '' ?>&tab=<?= $active_tab ?>" class="pagination-btn next">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="9 18 15 12 9 6"></polyline>
                    </svg>
                </a>
            <?php else: ?>
                <button class="pagination-btn next" disabled>
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="9 18 15 12 9 6"></polyline>
                    </svg>
                </button>
            <?php endif; ?>
        </div>
    </div>

    <?php else: ?>
    <!-- AFFICHAGE DE LA LISTE D'ATTENTE -->
    <div class="table-container">
        <?php if (empty($waiting_list)): ?>
            <div class="empty-list">La liste d'attente est vide.</div>
        <?php else: ?>
            <table class="waiting-table apprenants-table">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Prénom</th>
                        <th>Email</th>
                        <th>Téléphone</th>
                        <th>Référentiel</th>
                        <th>Erreurs</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($waiting_list as $index => $apprenant): ?>
                        <tr>
                            <td><?= htmlspecialchars($apprenant['nom']) ?></td>
                            <td><?= htmlspecialchars($apprenant['prenom']) ?></td>
                            <td><?= htmlspecialchars($apprenant['email']) ?></td>
                            <td><?= htmlspecialchars($apprenant['telephone']) ?></td>
                            <td>
                                <?php 
                                $ref_id = $apprenant['referentiel_id'];
                                echo isset($referentiels_map[$ref_id]) ? htmlspecialchars($referentiels_map[$ref_id]) : 'Non spécifié';
                                ?>
                            </td>
                            <td>
                                <ul class="error-list">
                                    <?php 
                                    if (isset($apprenant['errors']) && is_array($apprenant['errors'])) {
                                        foreach ($apprenant['errors'] as $field => $error): 
                                            if (is_string($error)): // S'assurer que l'erreur est une chaîne
                                    ?>
                                                <li><?= htmlspecialchars($error) ?></li>
                                    <?php 
                                            endif;
                                        endforeach; 
                                    } else {
                                        echo "<li>Erreur non spécifiée</li>";
                                    }
                                    ?>
                                </ul>
                            </td>
                            <td>
                                <a href="?page=correct-waiting-apprenant&index=<?= $index ?>" class="btn-correct">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                    </svg>
                                    Corriger
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>

<!-- Styles existants... -->

<style>
/* Vos styles existants */

/* Styles pour les onglets */
.tabs-container {
    margin-top: 15px;
    border-bottom: 1px solid #e0e0e0;
}

.tabs {
    display: flex;
    gap: 0;
}

.tab {
    padding: 12px 20px;
    font-size: 14px;
    color: #666;
    cursor: pointer;
    position: relative;
    text-decoration: none;
    transition: color 0.2s;
}

.tab:hover {
    color: #333;
}

.tab.active {
    color: #333;
    font-weight: 500;
}

.tab.active::after {
    content: '';
    position: absolute;
    bottom: -1px;
    left: 0;
    width: 100%;
    height: 3px;
    background-color: #F8A427;
    border-radius: 3px 3px 0 0;
}

/* Badge pour indiquer le nombre d'éléments dans la liste d'attente */
.badge {
    display: inline-block;
    background-color: #e74c3c;
    color: white;
    padding: 2px 6px;
    border-radius: 10px;
    font-size: 11px;
    margin-left: 5px;
}

/* Styles pour la liste d'attente */
.empty-list {
    text-align: center;
    padding: 40px;
    background-color: #f9f9f9;
    border-radius: 8px;
    color: #666;
    font-size: 16px;
    margin: 20px 0;
}

.waiting-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

.error-list {
    margin: 0;
    padding-left: 20px;
    font-size: 13px;
    color: #e74c3c;
}

.error-list li {
    margin-bottom: 3px;
}

.btn-correct {


    display: inline-flex;
    align-items: center;
    gap: 5px;
    background-color: #F8A427;
    color: white;
    border: none;
    border-radius: 4px;

    padding: 6px 12px;
    font-size: 13px;
    text-decoration: none;
    transition: background-color 0.2s;
}

.btn-correct:hover {

    background-color: #e09320;
    color: white;
}

.btn-correct svg {
    width: 14px;
    height: 14px;
}

/* Style général pour la section des apprenants */
.apprenants-container {
    max-width: 100%;
    padding: 0 20px;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background-color: #ffffff;
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

/* En-tête et filtres */
.apprenants-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px 0;
    margin-bottom: 10px;
}

.header-title h1 {
    color: #007a64;
    font-size: 28px;
    font-weight: 500;
    margin: 0;
}

.counter {
    background-color: #ffb800;
    color: white;
    font-size: 12px;
    padding: 3px 8px;
    border-radius: 20px;
    margin-left: 10px;
    font-weight: normal;
}

.search-box {
    position: relative;
}

.search-box input {
    padding: 8px 15px;
    border: 1px solid #e0e0e0;
    border-radius: 4px;
    width: 200px;
    font-size: 14px;
}

.search-icon {
    position: absolute;
    left: 10px;
    top: 50%;
    transform: translateY(-50%);
    color: #888;
}

.filter-dropdown select {
    padding: 8px 15px;
    border: 1px solid #e0e0e0;
    border-radius: 4px;
    background-color: white;
    font-size: 14px;
    color: #444;
}

.add-button {
    display: flex;
    align-items: center;
    gap: 5px;
    background-color: #007a64;
    color: white;
    border: none;
    border-radius: 4px;
    padding: 8px 15px;
    font-size: 14px;
    text-decoration: none;
    transition: background-color 0.2s;
}

.add-button:hover {
    background-color: #005a48;
}

/* Onglets */
.tabs-container {
    border-bottom: 1px solid #e0e0e0;
    margin-bottom: 0;
}

.tabs {
    display: flex;
    gap: 0;
}

.tab {
    padding: 12px 20px;
    font-size: 14px;
    color: #666;
    cursor: pointer;
    position: relative;
    text-decoration: none;
    transition: color 0.2s;
    border-bottom: 2px solid transparent;
}

.tab:hover {
    color: #333;
}

.tab.active {
    color: #ff7800;
    font-weight: 500;
    border-bottom: 2px solid #ff7800;
}

/* Tableau des apprenants */
.table-container {
    width: 100%;
    overflow-x: auto;
}

.apprenants-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    margin-top: 0;
}

.apprenants-table th {
    background-color: #f8a427;
    color: white;
    text-align: left;
    padding: 12px 15px;
    font-weight: 500;
    font-size: 14px;
}

.apprenants-table th:first-child {
    border-top-left-radius: 6px;
}

.apprenants-table th:last-child {
    border-top-right-radius: 6px;
    text-align: center;
}

.apprenants-table td {
    padding: 12px 15px;
    border-bottom: 1px solid #f0f0f0;
    color: #444;
    font-size: 14px;
}

.apprenants-table tr:hover {
    background-color: #f9f9f9;
}

/* Style pour la colonne photo */
.photo-col {
    width: 60px;
}

.apprenant-photo {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid #f8f8f8;
}

/* Style pour les badges */
.ref-badge {
    display: inline-block;
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 500;
}

.dev-web {
    background-color: #E6F7F5;
    color: #19A88C;
}

.ref-dig {
    background-color: #E6EEFA;
    color: #4073ff;
}

.dev-data {
    background-color: #F0E5FA;
    color: #9750dd;
}

.aws {
    background-color: #FFF3D9;
    color: #F8A427;
}

.hackeuse {
    background-color: #FFE5F1;
    color: #ff5cb0;
}

/* Style pour le statut */
.statut-badge {
    display: inline-block;
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 500;
}

.actif {
    background-color: #E6F7F5;
    color: #19A88C;
}

.remplace {
    background-color: #FFE5E5;
    color: #dd5050;
}

/* Boutons d'action */
.actions-col {
    text-align: center;
}

.action-dots {
    display: inline-flex;
    justify-content: center;
    align-items: center;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    background: transparent;
    cursor: pointer;
    transition: background-color 0.2s;
}

.action-dots:hover {
    background-color: #f0f0f0;
}

.dots-icon {
    color: #777;
}

/* Menu déroulant des actions */
.actions-menu {
    position: relative;
    display: inline-block;
}

.actions-dropdown {
    display: none;
    position: absolute;
    right: 0;
    min-width: 160px;
    background-color: white;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    border-radius: 4px;
    z-index: 10;
    overflow: hidden;
}

.dropdown-item {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 10px 15px;
    color: #333;
    text-decoration: none;
    transition: background-color 0.2s;
    font-size: 13px;
}

.dropdown-item:hover {
    background-color: #f5f5f5;
}

.actions-menu:hover .actions-dropdown {
    display: block;
}

/* Pagination */
.pagination-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px 0;
    margin-top: 20px;
}

.pagination-info {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 14px;
    color: #666;
}

.pagination-info select {
    border: 1px solid #e0e0e0;
    border-radius: 4px;
    padding: 5px;
    font-size: 14px;
}

.pagination-count {
    font-size: 14px;
    color: #666;
}

.pagination-controls {
    display: flex;
    align-items: center;
    gap: 5px;
}

.pagination-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    border: 1px solid #e0e0e0;
    border-radius: 4px;
    background-color: white;
    cursor: pointer;
    font-size: 14px;
    color: #666;
    text-decoration: none;
}

.pagination-btn.active {
    background-color: #ff7800;
    color: white;
    border-color: #ff7800;
}

.pagination-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

/* Suppression du border-radius en haut à droite et en haut à gauche du tableau */
.table-container {
    border-top-left-radius: 0;
    border-top-right-radius: 0;
}

/* Amélioration des boutons d'action en haut */
.apprenants-actions {
    display: flex;
    gap: 10px;
    align-items: center;
}

.import-button {
    background-color: #4073ff;
    color: white;
    border: none;
    border-radius: 4px;
    padding: 8px 15px;
    display: flex;
    align-items: center;
    gap: 5px;
    font-size: 14px;
    text-decoration: none;
}

.import-button:hover {
    background-color: #3060e0;
}

/* Style pour les cases à cocher */
.apprenants-table tr {
    transition: background-color 0.2s;
}

.apprenants-table tr:nth-child(odd) {
    background-color: #fafafa;
}

.apprenants-table tr:hover {
    background-color: #f0f0f0;
}
/* Largeurs spécifiques pour les colonnes */
.matricule-col { width: 80px; }
.nom-col { width: 180px; }
.adresse-col { width: 220px; }
.telephone-col { width: 120px; }
.referentiel-col { width: 140px; }
.statut-col { width: 100px; }
.actions-col { width: 80px; }

/* Style pour les boutons d'action verts */
.btn-action {
    background-color: #19A88C;
    color: white;
    border: none;
    border-radius: 4px;
    padding: 4px 8px;
    font-size: 12px;
    cursor: pointer;
}

.btn-action:hover {
    background-color: #148a74;
}
/* Stylisation des champs de recherche et des sélecteurs */
.search-box input, .filter-dropdown select {
    height: 38px;
    padding-left: 30px;
    box-shadow: 0 1px 2px rgba(0,0,0,0.05);
}

.filter-dropdown select {
    padding-left: 10px;
    padding-right: 30px;
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%23555' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 10px center;
}

/* Style pour le dropdown de téléchargement */
.custom-dropdown {
    position: relative;
    display: inline-block;
}

.dropdown-btn {
    display: flex;
    align-items: center;
    gap: 5px;
    background-color: #4F81BD; /* Bleu cohérent avec le thème */
    color: white;
    border: none;
    border-radius: 4px;
    padding: 8px 15px;
    font-size: 14px;
    cursor: pointer;
    transition: all 0.2s ease;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.dropdown-btn:hover {
    background-color: #3a6491; /* Version plus foncée pour le hover */
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
}

.dropdown-content {
    display: none;
    position: absolute;
    min-width: 160px;
    background-color: white;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    border-radius: 4px;
    z-index: 10;
    overflow: hidden;
    margin-top: 5px;
}

.dropdown-content a {
    display: block;
    padding: 10px 15px;
    color: #333;
    text-decoration: none;
    transition: background-color 0.2s;
    font-size: 14px;
}

.dropdown-content a:hover {
    background-color: #f5f5f5;
    color: #4F81BD; /* Couleur cohérente avec le thème */
}

.custom-dropdown:hover .dropdown-content {
    display: block;
}

/* Mise à jour des boutons pour respecter la charte graphique */
.import-button {
    display: flex;
    align-items: center;
    gap: 5px;
    background-color: #F8A427; /* Orange plus doux */
    color: white;
    border: none;
    border-radius: 4px;
    padding: 8px 15px;
    font-size: 14px;
    text-decoration: none;
    transition: all 0.2s ease;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.import-button:hover {
    background-color: #e09320; /* Version plus foncée pour le hover */
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
}

.add-button {
    display: flex;
    align-items: center;
    gap: 5px;
    background-color: #5CB85C; /* Vert plus doux qui s'intègre mieux */
    color: white;
    border: none;
    border-radius: 4px;
    padding: 8px 15px;
    font-size: 14px;
    text-decoration: none;
    transition: all 0.2s ease;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.add-button:hover {
    background-color: #4a9d4a; /* Version plus foncée pour le hover */
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
}

/* Ajout d'un style commun pour aligner les boutons */
.apprenants-actions {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
    margin-bottom: 20px;
}

/* Ajustement pour les écrans plus petits */
@media (max-width: 768px) {
    .apprenants-actions {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .custom-dropdown, .import-button, .add-button {
        width: 100%;
        margin-bottom: 8px;
    }
    
    .dropdown-content {
        width: 100%;
    }
}
</style>