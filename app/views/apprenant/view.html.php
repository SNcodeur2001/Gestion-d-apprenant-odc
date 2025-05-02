<!-- Titre de la page -->
<div class="apprenant-header">
    <h1 class="app-title">Apprenants</h1>
    <span class="page-title">/ Détails</span>
</div>

<!-- Contenu principal des détails de l'apprenant -->
<div class="apprenant-content-wrapper">
    <!-- Profil et infos de contact -->
    <div class="apprenant-profile">
        <a href="?page=apprenants" class="back-link">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M19 12H5M12 19l-7-7 7-7"></path>
            </svg>
            Retour sur la liste
        </a>
        
        <div class="profile-section">
            <div class="profile-image">
                <img src="<?= $apprenant['photo'] ?>" alt="<?= htmlspecialchars($apprenant['prenom'] . ' ' . $apprenant['nom']) ?>">
            </div>
            <h2 class="profile-name"><?= htmlspecialchars($apprenant['prenom'] . ' ' . $apprenant['nom']) ?></h2>
            <div class="profile-role"><?= htmlspecialchars($role ?? 'DEV WEB/MOBILE') ?></div>
            <div class="profile-status"><?= ucfirst($apprenant['statut']) ?></div>
        </div>
        
        <div class="contact-info">
            <div class="contact-item">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                </svg>
                <span><?= htmlspecialchars($apprenant['telephone']) ?></span>
            </div>
            <div class="contact-item">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                    <polyline points="22,6 12,13 2,6"></polyline>
                </svg>
                <span><?= htmlspecialchars($apprenant['email']) ?></span>
            </div>
            <div class="contact-item">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                    <circle cx="12" cy="10" r="3"></circle>
                </svg>
                <span><?= htmlspecialchars($apprenant['adresse']) ?></span>
            </div>
        </div>
    </div>
    
    <!-- Statistiques et modules -->
    <div class="apprenant-details">
        <div class="stats-row">
            <div class="stat-card">
                <div class="stat-icon presence-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10z"></path>
                        <path d="M9 12l2 2 4-4"></path>
                    </svg>
                </div>
                <div class="stat-content">
                    <div class="stat-value"><?= $presences ?? 20 ?></div>
                    <div class="stat-label">Présence(s)</div>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon retard-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                </div>
                <div class="stat-content">
                    <div class="stat-value"><?= $retards ?? 5 ?></div>
                    <div class="stat-label">Retard(s)</div>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon absence-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10z"></path>
                        <line x1="15" y1="9" x2="9" y2="15"></line>
                        <line x1="9" y1="9" x2="15" y2="15"></line>
                    </svg>
                </div>
                <div class="stat-content">
                    <div class="stat-value"><?= $absences ?? 1 ?></div>
                    <div class="stat-label">Absence(s)</div>
                </div>
            </div>
        </div>
        
        <div class="tabs-header">
            <div class="tab-title">Programme & Modules</div>
            <div class="absences-info">
                Total absences par étudiant
            </div>
        </div>
        
        <div class="modules-grid">
            <!-- Module 1 -->
            <div class="module-card colored-border algo-border">
                <div class="module-header">
                    <div class="module-duration">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <polyline points="12 6 12 12 16 14"></polyline>
                        </svg>
                        30 jours
                    </div>
                    <div class="module-actions">
                        <div class="module-action">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="1"></circle>
                                <circle cx="12" cy="5" r="1"></circle>
                                <circle cx="12" cy="19" r="1"></circle>
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="module-content">
                    <h3 class="module-title">Algorithme & Langage C</h3>
                    <p class="module-description">Complexité algorithmique & pratique codage en langage C</p>
                    <div class="module-status completed">Débutant</div>
                </div>
                <div class="module-footer">
                    <div class="module-date">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="16" y1="2" x2="16" y2="6"></line>
                            <line x1="8" y1="2" x2="8" y2="6"></line>
                            <line x1="3" y1="10" x2="21" y2="10"></line>
                        </svg>
                        15 Février 2025
                    </div>
                    <div class="module-time">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <polyline points="12 6 12 12 16 14"></polyline>
                        </svg>
                        12:45 pm
                    </div>
                </div>
            </div>
            
            <!-- Module 2 -->
            <div class="module-card colored-border frontend-border">
                <div class="module-header">
                    <div class="module-duration">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <polyline points="12 6 12 12 16 14"></polyline>
                        </svg>
                        15 jours
                    </div>
                    <div class="module-actions">
                        <div class="module-action">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="1"></circle>
                                <circle cx="12" cy="5" r="1"></circle>
                                <circle cx="12" cy="19" r="1"></circle>
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="module-content">
                    <h3 class="module-title">Frontend 1: Html, Css & JS</h3>
                    <p class="module-description">Création d'interfaces de design avec animations avancées !</p>
                    <div class="module-status completed">Débutant</div>
                </div>
                <div class="module-footer">
                    <div class="module-date">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="16" y1="2" x2="16" y2="6"></line>
                            <line x1="8" y1="2" x2="8" y2="6"></line>
                            <line x1="3" y1="10" x2="21" y2="10"></line>
                        </svg>
                        24 Mars 2025
                    </div>
                    <div class="module-time">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <polyline points="12 6 12 12 16 14"></polyline>
                        </svg>
                        12:45 pm
                    </div>
                </div>
            </div>
            
            <!-- Autres modules -->
            <!-- ... Continuez avec les autres modules (3-6) ... -->
        </div>
    </div>
</div>

<style>
/* Styles spécifiques à la page détails apprenant, qui ne devraient pas entrer en conflit avec votre layout */
.apprenant-header {
    display: flex;
    align-items: center;
    margin-bottom: 20px;
    border-bottom: 1px solid #eaeaea;
    padding-bottom: 10px;
}

.app-title {
    font-size: 24px;
    font-weight: 500;
    color: #17a2b8;
    margin-right: 5px;
}

.page-title {
    font-size: 18px;
    color: #fd7e14;
    font-weight: 400;
}

.apprenant-content-wrapper {
    display: grid;
    grid-template-columns: 250px 1fr;
    gap: 20px;
}

.apprenant-profile {
    background-color: white;
    border-radius: 8px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    padding: 20px;
}

.back-link {
    display: flex;
    align-items: center;
    text-decoration: none;
    color: #666;
    font-size: 14px;
    margin-bottom: 20px;
}

.back-link svg {
    margin-right: 5px;
}

.profile-section {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    margin-bottom: 20px;
}

.profile-image {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    overflow: hidden;
    margin-bottom: 15px;
    border: 3px solid #f1f1f1;
}

.profile-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.profile-name {
    font-size: 18px;
    font-weight: 600;
    color: #333;
    margin-bottom: 5px;
}

.profile-role {
    display: inline-block;
    background-color: #20c997;
    color: white;
    padding: 3px 12px;
    border-radius: 20px;
    font-size: 12px;
    margin-bottom: 10px;
}

.profile-status {
    background-color: #e3fcef;
    color: #20c997;
    padding: 3px 15px;
    border-radius: 20px;
    font-size: 12px;
}

.contact-info {
    margin-top: 20px;
}

.contact-item {
    display: flex;
    align-items: center;
    margin-bottom: 12px;
    font-size: 14px;
    color: #555;
}

.contact-item svg {
    margin-right: 10px;
    min-width: 20px;
}

.contact-item span {
    word-break: break-word;
}

/* Styles pour la partie droite avec les stats et modules */
.apprenant-details {
    display: flex;
    flex-direction: column;
}

.stats-row {
    display: flex;
    margin-bottom: 20px;
    gap: 15px;
}

.stat-card {
    flex: 1;
    display: flex;
    align-items: center;
    background-color: white;
    border-radius: 8px;
    padding: 15px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.05);
}

.stat-icon {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 15px;
}

.presence-icon {
    background-color: #c2e7de;
    color: #20c997;
}

.retard-icon {
    background-color: #fff3cd;
    color: #fd7e14;
}

.absence-icon {
    background-color: #f8d7da;
    color: #dc3545;
}

.stat-icon svg {
    width: 24px;
    height: 24px;
}

.stat-content {
    display: flex;
    flex-direction: column;
}

.stat-value {
    font-size: 24px;
    font-weight: 600;
    color: #333;
    line-height: 1;
}

.stat-label {
    font-size: 14px;
    color: #666;
    margin-top: 5px;
}

.tabs-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
}

.tab-title {
    font-size: 18px;
    font-weight: 600;
    color: #333;
    background-color: #fd7e14;
    color: white;
    padding: 10px 15px;
    border-radius: 5px;
}

.absences-info {
    display: flex;
    align-items: center;
    background-color: white;
    padding: 10px 15px;
    border-radius: 5px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.modules-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 20px;
}

.module-card {
    background-color: white;
    border-radius: 8px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    overflow: hidden;
}

.module-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 15px;
    background-color: #f8f9fa;
    border-bottom: 1px solid #eaeaea;
}

.module-duration {
    display: flex;
    align-items: center;
    font-size: 13px;
    color: #555;
}

.module-duration svg {
    margin-right: 5px;
}

.module-actions {
    display: flex;
    align-items: center;
}

.module-action {
    width: 28px;
    height: 28px;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: #f0f0f0;
    border-radius: 4px;
    margin-left: 5px;
    cursor: pointer;
}

.module-content {
    padding: 15px;
}

.module-title {
    font-size: 16px;
    font-weight: 600;
    color: #333;
    margin-bottom: 5px;
}

.module-description {
    font-size: 13px;
    color: #666;
    margin-bottom: 15px;
}

.module-status {
    display: inline-block;
    font-size: 12px;
    padding: 3px 10px;
    border-radius: 15px;
    margin-bottom: 15px;
}

.module-status.completed {
    background-color: #e3fcef;
    color: #20c997;
}

.module-status.ongoing {
    background-color: #e2f5ff;
    color: #17a2b8;
}

.module-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 15px;
    border-top: 1px solid #eaeaea;
}

.module-date {
    display: flex;
    align-items: center;
    font-size: 13px;
    color: #666;
}

.module-date svg {
    margin-right: 5px;
}

.module-time {
    display: flex;
    align-items: center;
    font-size: 13px;
    color: #666;
}

.module-time svg {
    margin-right: 5px;
    color: #fd7e14;
}

.colored-border {
    border-top: 3px solid;
    margin-top: -3px;
}

.algo-border {
    border-color: #343a40;
}

.frontend-border {
    border-color: #28a745;
}

.backend-border {
    border-color: #007bff;
}

/* Responsive fixes */
@media (max-width: 992px) {
    .apprenant-content-wrapper {
        grid-template-columns: 1fr;
    }
    
    .apprenant-profile {
        order: 1;
    }
    
    .apprenant-details {
        order: 0;
    }
}

@media (max-width: 768px) {
    .stats-row {
        flex-direction: column;
        gap: 10px;
    }
    
    .modules-grid {
        grid-template-columns: 1fr;
    }
}
