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
            <div class="profile-role"><?= htmlspecialchars($referentiel['name'] ?? 'DEV WEB/MOBILE') ?></div>
            <div class="profile-status"><?= ucfirst($apprenant['statut']) ?></div>
            
            <!-- QR Code pour les informations de l'apprenant -->
            <div class="qr-code-container">
                <div id="apprenant-qrcode"></div>
                <div class="qr-code-label">Scanner pour les informations</div>
            </div>
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
            <div class="contact-item">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                    <line x1="16" y1="2" x2="16" y2="6"></line>
                    <line x1="8" y1="2" x2="8" y2="6"></line>
                    <line x1="3" y1="10" x2="21" y2="10"></line>
                </svg>
                <span><?= htmlspecialchars($apprenant['date_naissance']) ?></span>
            </div>
            <div class="contact-item">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M20 7h-4V3M4 17h4v4"></path>
                    <path d="M7 7h10M7 12h10M7 17h4"></path>
                </svg>
                <span>Matricule: <?= htmlspecialchars($apprenant['matricule']) ?></span>
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

<!-- QR Code script library -->
<script src="https://cdn.jsdelivr.net/npm/qrcode-generator@1.4.4/qrcode.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Récupérer les informations de l'apprenant pour le QR code
    const apprenantData = {
        matricule: "<?= htmlspecialchars($apprenant['matricule']) ?>",
        nom: "<?= htmlspecialchars($apprenant['nom']) ?>",
        prenom: "<?= htmlspecialchars($apprenant['prenom']) ?>",
        email: "<?= htmlspecialchars($apprenant['email']) ?>",
        telephone: "<?= htmlspecialchars($apprenant['telephone']) ?>",
        referentiel: "<?= htmlspecialchars($referentiel['name'] ?? '') ?>",
        promotion: "<?= htmlspecialchars($promotion['name'] ?? '') ?>"
    };
    
    // Convertir les données en chaîne JSON et l'encoder pour le QR code
    const apprenantJson = JSON.stringify(apprenantData);
    
    // Générer le QR code
    const qr = qrcode(0, 'M');
    qr.addData(apprenantJson);
    qr.make();
    
    // Insérer le QR code dans la page
    document.getElementById('apprenant-qrcode').innerHTML = qr.createImgTag(5);
});
</script>

<style>
/* Styles existants  */
/* Variables globales pour une cohérence visuelle */
:root {
  --primary: #17a2b8;
  --primary-light: #e2f5ff;
  --secondary: #fd7e14;
  --secondary-light: #fff3cd;
  --success: #20c997;
  --success-light: #e3fcef;
  --danger: #dc3545;
  --danger-light: #f8d7da;
  --dark: #343a40;
  --gray-100: #f8f9fa;
  --gray-200: #eaeaea;
  --gray-300: #e6e6e6;
  --gray-400: #ced4da;
  --gray-500: #adb5bd;
  --gray-600: #6c757d;
  --gray-700: #495057;
  --gray-800: #343a40;
  --shadow-sm: 0 2px 4px rgba(0, 0, 0, 0.05);
  --shadow-md: 0 4px 8px rgba(0, 0, 0, 0.08);
  --shadow-lg: 0 8px 16px rgba(0, 0, 0, 0.1);
  --radius-sm: 4px;
  --radius-md: 8px;
  --radius-lg: 12px;
  --radius-xl: 16px;
  --radius-round: 50%;
  --transition: all 0.3s ease;
}

/* Styles Généraux & Reset */
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

body {
  font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
  background-color: #f5f7fa;
  color: var(--gray-700);
  line-height: 1.5;
}

/* En-tête de page */
.apprenant-header {
  display: flex;
  align-items: center;
  margin-bottom: 24px;
  border-bottom: 1px solid var(--gray-200);
  padding-bottom: 16px;
}

.app-title {
  font-size: 28px;
  font-weight: 600;
  color: var(--primary);
  margin-right: 8px;
  letter-spacing: -0.5px;
}

.page-title {
  font-size: 20px;
  color: var(--secondary);
  font-weight: 500;
  opacity: 0.9;
}

/* Layout principal */
.apprenant-content-wrapper {
  display: grid;
  grid-template-columns: 300px 1fr;
  gap: 24px;
  max-width: 1400px;
  margin: 0 auto;
}

/* Profile Sidebar */
.apprenant-profile {
  background-color: white;
  border-radius: var(--radius-lg);
  box-shadow: var(--shadow-md);
  padding: 24px;
  height: fit-content;
  transition: var(--transition);
}

.apprenant-profile:hover {
  box-shadow: var(--shadow-lg);
}

.back-link {
  display: flex;
  align-items: center;
  text-decoration: none;
  color: var(--gray-600);
  font-size: 15px;
  margin-bottom: 24px;
  transition: var(--transition);
  font-weight: 500;
}

.back-link:hover {
  color: var(--primary);
  transform: translateX(-3px);
}

.back-link svg {
  margin-right: 8px;
  transition: var(--transition);
}

.back-link:hover svg {
  stroke: var(--primary);
}

.profile-section {
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
  margin-bottom: 24px;
  padding-bottom: 24px;
  border-bottom: 1px solid var(--gray-200);
}

.profile-image {
  width: 140px;
  height: 140px;
  border-radius: var(--radius-round);
  overflow: hidden;
  margin-bottom: 20px;
  border: 4px solid var(--gray-100);
  box-shadow: var(--shadow-sm);
  transition: var(--transition);
}

.profile-image:hover {
  transform: scale(1.03);
  border-color: var(--primary-light);
}

.profile-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: var(--transition);
}

.profile-name {
  font-size: 22px;
  font-weight: 700;
  color: var(--gray-800);
  margin-bottom: 8px;
  letter-spacing: -0.3px;
}

.profile-role {
  display: inline-block;
  background-color: var(--success);
  color: white;
  padding: 5px 14px;
  border-radius: 20px;
  font-size: 13px;
  margin-bottom: 12px;
  font-weight: 500;
  letter-spacing: 0.2px;
  box-shadow: 0 2px 4px rgba(32, 201, 151, 0.2);
}

.profile-status {
  background-color: var(--success-light);
  color: var(--success);
  padding: 5px 16px;
  border-radius: 20px;
  font-size: 13px;
  font-weight: 500;
  box-shadow: 0 2px 4px rgba(32, 201, 151, 0.1);
}

/* QR Code Container Styles */
.qr-code-container {
  margin-top: 24px;
  display: flex;
  flex-direction: column;
  align-items: center;
}

#apprenant-qrcode {
  padding: 12px;
  background-color: white;
  border-radius: var(--radius-md);
  box-shadow: var(--shadow-md);
  margin-bottom: 12px;
  transition: var(--transition);
}

#apprenant-qrcode:hover {
  transform: translateY(-3px);
  box-shadow: var(--shadow-lg);
}

#apprenant-qrcode img {
  display: block;
  max-width: 160px;
  height: 160px;
}

.qr-code-label {
  font-size: 13px;
  color: var(--gray-600);
  margin-top: 6px;
}

/* Informations de contact */
.contact-info {
  margin-top: 24px;
}

.contact-item {
  display: flex;
  align-items: center;
  margin-bottom: 16px;
  font-size: 15px;
  color: var(--gray-700);
  transition: var(--transition);
}

.contact-item:hover {
  color: var(--primary);
}

.contact-item svg {
  margin-right: 12px;
  min-width: 20px;
  stroke: var(--secondary);
  transition: var(--transition);
}

.contact-item:hover svg {
  transform: scale(1.15);
}

.contact-item span {
  word-break: break-word;
  font-weight: 500;
}

/* Partie droite avec stats et modules */
.apprenant-details {
  display: flex;
  flex-direction: column;
}

.stats-row {
  display: flex;
  margin-bottom: 28px;
  gap: 20px;
}

.stat-card {
  flex: 1;
  display: flex;
  align-items: center;
  background-color: white;
  border-radius: var(--radius-lg);
  padding: 20px;
  box-shadow: var(--shadow-md);
  transition: var(--transition);
  overflow: hidden;
  position: relative;
}

.stat-card:hover {
  transform: translateY(-3px);
  box-shadow: var(--shadow-lg);
}

.stat-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  height: 4px;
  width: 100%;
}

.stat-card:nth-child(1)::before {
  background-color: var(--success);
}

.stat-card:nth-child(2)::before {
  background-color: var(--secondary);
}

.stat-card:nth-child(3)::before {
  background-color: var(--danger);
}

.stat-icon {
  width: 56px;
  height: 56px;
  border-radius: var(--radius-round);
  display: flex;
  align-items: center;
  justify-content: center;
  margin-right: 16px;
  transition: var(--transition);
}

.presence-icon {
  background-color: var(--success-light);
  color: var(--success);
}

.retard-icon {
  background-color: var(--secondary-light);
  color: var(--secondary);
}

.absence-icon {
  background-color: var(--danger-light);
  color: var(--danger);
}

.stat-card:hover .stat-icon {
  transform: scale(1.1) rotate(5deg);
}

.stat-icon svg {
  width: 26px;
  height: 26px;
  transition: var(--transition);
}

.stat-card:hover .stat-icon svg {
  transform: scale(1.1);
}

.stat-content {
  display: flex;
  flex-direction: column;
}

.stat-value {
  font-size: 32px;
  font-weight: 700;
  color: var(--gray-800);
  line-height: 1;
  letter-spacing: -0.5px;
}

.stat-label {
  font-size: 15px;
  color: var(--gray-600);
  margin-top: 6px;
  font-weight: 500;
}

.tabs-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 24px;
}

.tab-title {
  font-size: 18px;
  font-weight: 600;
  color: white;
  background-color: var(--secondary);
  padding: 10px 18px;
  border-radius: var(--radius-md);
  box-shadow: 0 3px 6px rgba(253, 126, 20, 0.2);
  transition: var(--transition);
  letter-spacing: 0.3px;
}

.tab-title:hover {
  background-color: #f07214;
  transform: translateY(-2px);
  box-shadow: 0 4px 8px rgba(253, 126, 20, 0.25);
}

.absences-info {
  display: flex;
  align-items: center;
  background-color: white;
  padding: 10px 18px;
  border-radius: var(--radius-md);
  box-shadow: var(--shadow-sm);
  font-weight: 500;
  color: var(--gray-700);
  font-size: 14px;
  transition: var(--transition);
}

.absences-info:hover {
  box-shadow: var(--shadow-md);
  transform: translateY(-2px);
}

/* Grille de modules */
.modules-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
  gap: 24px;
}

.module-card {
  background-color: white;
  border-radius: var(--radius-lg);
  box-shadow: var(--shadow-md);
  overflow: hidden;
  transition: var(--transition);
  display: flex;
  flex-direction: column;
}

.module-card:hover {
  transform: translateY(-5px);
  box-shadow: var(--shadow-lg);
}

.module-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 14px 18px;
  background-color: var(--gray-100);
  border-bottom: 1px solid var(--gray-200);
}

.module-duration {
  display: flex;
  align-items: center;
  font-size: 14px;
  color: var(--gray-600);
  font-weight: 500;
}

.module-duration svg {
  margin-right: 6px;
  color: var(--primary);
}

.module-actions {
  display: flex;
  align-items: center;
}

.module-action {
  width: 30px;
  height: 30px;
  display: flex;
  align-items: center;
  justify-content: center;
  background-color: var(--gray-200);
  border-radius: var(--radius-sm);
  margin-left: 5px;
  cursor: pointer;
  transition: var(--transition);
}

.module-action:hover {
  background-color: var(--primary-light);
  color: var(--primary);
  transform: rotate(90deg);
}

.module-content {
  padding: 18px;
  flex-grow: 1;
}

.module-title {
  font-size: 18px;
  font-weight: 600;
  color: var(--gray-800);
  margin-bottom: 8px;
  transition: var(--transition);
}

.module-card:hover .module-title {
  color: var(--primary);
}

.module-description {
  font-size: 14px;
  color: var(--gray-600);
  margin-bottom: 18px;
  line-height: 1.5;
}

.module-status {
  display: inline-block;
  font-size: 13px;
  padding: 4px 12px;
  border-radius: 20px;
  margin-bottom: 15px;
  font-weight: 500;
  transition: var(--transition);
}

.module-status.completed {
  background-color: var(--success-light);
  color: var(--success);
}

.module-status.ongoing {
  background-color: var(--primary-light);
  color: var(--primary);
}

.module-card:hover .module-status {
  transform: scale(1.05);
}

.module-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 14px 18px;
  border-top: 1px solid var(--gray-200);
  background-color: var(--gray-100);
}

.module-date {
  display: flex;
  align-items: center;
  font-size: 13px;
  color: var(--gray-700);
  font-weight: 500;
}

.module-date svg {
  margin-right: 5px;
  color: var(--gray-600);
}

.module-time {
  display: flex;
  align-items: center;
  font-size: 13px;
  color: var(--gray-700);
  font-weight: 500;
}

.module-time svg {
  margin-right: 5px;
  color: var(--secondary);
}

/* Bordures colorées */
.colored-border {
  border-top: 4px solid;
}

.algo-border {
  border-color: var(--dark);
}

.frontend-border {
  border-color: #28a745;
}

.backend-border {
  border-color: #007bff;
}

/* Animation subtile pour les cartes au hover */
.module-card:hover {
  animation: pulse 2s infinite;
}

@keyframes pulse {
  0% {
    box-shadow: var(--shadow-md);
  }
  50% {
    box-shadow: 0 6px 15px rgba(0, 0, 0, 0.12);
  }
  100% {
    box-shadow: var(--shadow-md);
  }
}

/* Responsive fixes avec media queries améliorées */
@media (max-width: 1200px) {
  .modules-grid {
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  }
}

@media (max-width: 992px) {
  .apprenant-content-wrapper {
    grid-template-columns: 1fr;
    gap: 30px;
  }
  
  .apprenant-profile {
    order: 1;
  }
  
  .apprenant-details {
    order: 0;
  }
  
  .stat-card {
    padding: 16px;
  }
  
  .stat-icon {
    width: 48px;
    height: 48px;
  }
  
  .stat-value {
    font-size: 28px;
  }
}

@media (max-width: 768px) {
  .stats-row {
    flex-direction: column;
    gap: 16px;
  }
  
  .modules-grid {
    grid-template-columns: 1fr;
  }
  
  .tabs-header {
    flex-direction: column;
    gap: 12px;
    align-items: flex-start;
  }
  
  .tab-title, .absences-info {
    width: 100%;
  }
}

@media (max-width: 480px) {
  .apprenant-header {
    flex-direction: column;
    align-items: flex-start;
    gap: 5px;
  }
  
  .module-header, .module-footer {
    flex-direction: column;
    gap: 8px;
    align-items: flex-start;
  }
  
  .module-actions {
    margin-top: 8px;
  }
}

/* Améliorations d'accessibilité */
@media (prefers-reduced-motion: reduce) {
  * {
    animation-duration: 0.01ms !important;
    animation-iteration-count: 1 !important;
    transition-duration: 0.01ms !important;
    scroll-behavior: auto !important;
  }
}

/* Mode sombre (à activer avec une classe ou media query selon votre préférence) */
@media (prefers-color-scheme: dark) {
  :root {
    --primary: #25b6cc;
    --primary-light: rgba(37, 182, 204, 0.15);
    --secondary: #ff8c38;
    --secondary-light: rgba(255, 140, 56, 0.15);
    --success: #2dd4a7;
    --success-light: rgba(45, 212, 167, 0.15);
    --danger: #e84c5c;
    --danger-light: rgba(232, 76, 92, 0.15);
    --dark: #212529;
    --gray-100: #2a2e33;
    --gray-200: #343a40;
    --gray-300: #495057;
    --gray-400: #6c757d;
    --gray-500: #adb5bd;
    --gray-600: #ced4da;
    --gray-700: #dee2e6;
    --gray-800: #f8f9fa;
  }
  
  body {
    background-color: #1a1d21;
    color: var(--gray-700);
  }
  
  .apprenant-profile, .stat-card, .module-card, .absences-info {
    background-color: #252a30;
  }
  
  .module-header, .module-footer {
    background-color: #202428;
  }
}
</style>