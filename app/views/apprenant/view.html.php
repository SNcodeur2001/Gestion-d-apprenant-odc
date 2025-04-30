<!-- app/views/apprenant/view.html.php -->

<div class="container">
    <div class="header">
        <div class="header-title">
            <h1>Détails de l'apprenant</h1>
            <div class="header-subtitle">Promotion: <?= isset($promotion['name']) ? htmlspecialchars($promotion['name']) : 'Non définie' ?></div>
        </div>
        <a href="?page=apprenants" class="btn-back">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M19 12H5M12 19l-7-7 7-7"/>
            </svg>
            Retour aux apprenants
        </a>
    </div>

    <div class="apprenant-profile">
        <div class="profile-header">
            <div class="profile-avatar">
                <img src="<?= htmlspecialchars($apprenant['photo']) ?>" alt="Photo de <?= htmlspecialchars($apprenant['prenom']) ?>">
            </div>
            <div class="profile-info">
                <h2 class="profile-name"><?= htmlspecialchars($apprenant['nom'] . ' ' . $apprenant['prenom']) ?></h2>
                <div class="profile-matricule">Matricule: <?= htmlspecialchars($apprenant['matricule']) ?></div>
                <div class="profile-status">
                    <span class="statut-badge <?= $apprenant['statut'] === 'actif' ? 'actif' : 'remplace' ?>">
                        <?= ucfirst($apprenant['statut']) ?>
                    </span>
                </div>
            </div>
            <div class="profile-actions">
                <a href="?page=edit-apprenant&id=<?= $apprenant['id'] ?>" class="btn-edit">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                    </svg>
                    Modifier
                </a>
                <?php if ($apprenant['statut'] === 'actif') { ?>
                    <a href="?page=replace-apprenant&id=<?= $apprenant['id'] ?>" class="btn-replace">
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

        <div class="profile-content">
            <div class="profile-section">
                <h3 class="section-title">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                    Informations personnelles
                </h3>
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">Date de naissance</div>
                        <div class="info-value"><?= date('d/m/Y', strtotime($apprenant['date_naissance'])) ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Lieu de naissance</div>
                        <div class="info-value"><?= htmlspecialchars($apprenant['lieu_naissance']) ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Date d'inscription</div>
                        <div class="info-value"><?= date('d/m/Y', strtotime($apprenant['date_inscription'])) ?></div>
                    </div>
                </div>
            </div>

            <div class="profile-section">
                <h3 class="section-title">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                    </svg>
                    Coordonnées
                </h3>
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">Email</div>
                        <div class="info-value"><?= htmlspecialchars($apprenant['email']) ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Téléphone</div>
                        <div class="info-value"><?= htmlspecialchars($apprenant['telephone']) ?></div>
                    </div>
                    <div class="info-item full-width">
                        <div class="info-label">Adresse</div>
                        <div class="info-value"><?= htmlspecialchars($apprenant['adresse']) ?></div>
                    </div>
                </div>
            </div>

            <div class="profile-section">
                <h3 class="section-title">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path>
                        <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path>
                    </svg>
                    Formation
                </h3>
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">Promotion</div>
                        <div class="info-value"><?= isset($promotion['name']) ? htmlspecialchars($promotion['name']) : 'Non définie' ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Référentiel</div>
                        <div class="info-value">
                            <?php
                            $referentiel_name = isset($referentiel['name']) ? $referentiel['name'] : 'Non défini';
                            
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
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Période de formation</div>
                        <div class="info-value">
                            <?= isset($promotion['date_debut']) ? date('d/m/Y', strtotime($promotion['date_debut'])) : 'Non définie' ?> - 
                            <?= isset($promotion['date_fin']) ? date('d/m/Y', strtotime($promotion['date_fin'])) : 'Non définie' ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Styles pour la vue de détail d'un apprenant */
body {
    font-family: 'Arial', sans-serif;
    background-color: #f8f9fa;
    margin: 0;
    padding: 0;
}

.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}

.header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.header-title h1 {
    font-size: 24px;
    color: #333;
    margin: 0;
}

.header-subtitle {
    font-size: 14px;
    color: #666;
}

.btn-back {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 16px;
    background-color: #f5f5f5;
    border: 1px solid #ddd;
    border-radius: 4px;
    color: #333;
    text-decoration: none;
    font-size: 14px;
    transition: all 0.2s;
}

.btn-back:hover {
    background-color: #e9e9e9;
}

.apprenant-profile {
    background-color: white;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    overflow: hidden;
}

.profile-header {
    display: flex;
    padding: 24px;
    background-color: #f8f9fa;
    border-bottom: 1px solid #f0f0f0;
}

.profile-avatar {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    overflow: hidden;
    border: 3px solid white;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
    margin-right: 20px;
}

.profile-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.profile-info {
    flex-grow: 1;
}

.profile-name {
    font-size: 24px;
    font-weight: 600;
    color: #333;
    margin: 0 0 5px 0;
}

.profile-matricule {
    font-size: 14px;
    color: #666;
    margin-bottom: 10px;
}

.profile-actions {
    display: flex;
    gap: 10px;
    margin-left: auto;
    align-self: center;
}

.btn-edit, .btn-replace {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 10px 16px;
    border-radius: 4px;
    font-size: 14px;
    font-weight: 500;
    text-decoration: none;
    transition: all 0.2s;
}

.btn-edit {
    background-color: #F8A427;
    color: white;
}

.btn-replace {
    background-color: #f5f5f5;
    color: #333;
    border: 1px solid #ddd;
}

.btn-edit:hover {
    background-color: #e09320;
}

.btn-replace:hover {
    background-color: #e9e9e9;
}

.profile-content {
    padding: 24px;
}

.profile-section {
    margin-bottom: 30px;
}

.profile-section:last-child {
    margin-bottom: 0;
}

.section-title {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 18px;
    font-weight: 500;
    color: #333;
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 1px solid #f0f0f0;
}

.section-title svg {
    color: #F8A427;
}

.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 16px;
}

.full-width {
    grid-column: 1 / -1;
}

.info-item {
    background-color: #f8f9fa;
    padding: 15px;
    border-radius: 6px;
}

.info-label {
    font-size: 13px;
    color: #666;
    margin-bottom: 5px;
}

.info-value {
    font-size: 15px;
    color: #333;
    font-weight: 500;
}

/* Badges pour les référentiels et statuts */
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

/* Responsive */
@media (max-width: 768px) {
    .profile-header {
        flex-direction: column;
        align-items: center;
        text-align: center;
    }
    
    .profile-avatar {
        margin-right: 0;
        margin-bottom: 15px;
    }
    
    .profile-actions {
        margin-left: 0;
        margin-top: 15px;
        justify-content: center;
    }
    
    .info-grid {
        grid-template-columns: 1fr;
    }
}
</style>