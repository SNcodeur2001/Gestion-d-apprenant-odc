<!-- app/views/apprenant/add.html.php -->

<div class="form-container">
    <h2 class="form-title">Ajout apprenant</h2>

    <form action="?page=add-apprenant-process" method="POST" enctype="multipart/form-data" class="apprenant-form">
        <!-- Section Informations de l'apprenant -->
        <div class="form-section">
            <div class="section-header">
                <h3>Informations de l'apprenant</h3>
                <button type="button" class="edit-button">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                    </svg>
                </button>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="prenom">Prénom(s)*</label>
                    <input type="text" id="prenom" name="prenom" placeholder="Entrez le prénom" value="<?= htmlspecialchars($prenom ?? '') ?>">
                    <?php if (isset($errors['prenom'])): ?>
                        <div class="error-message"><?= htmlspecialchars($errors['prenom']) ?></div>
                    <?php endif; ?>
                </div>
                <div class="form-group">
                    <label for="nom">Nom*</label>
                    <input type="text" id="nom" name="nom" placeholder="Entrez le nom" value="<?= htmlspecialchars($nom ?? '') ?>">
                    <?php if (isset($errors['nom'])): ?>
                        <div class="error-message"><?= htmlspecialchars($errors['nom']) ?></div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="date_naissance">Date de naissance* (JJ/MM/AAAA)</label>
                    <input type="text" id="date_naissance" name="date_naissance" placeholder="Ex: 15/05/1995" value="<?= htmlspecialchars($date_naissance ?? '') ?>">
                    <?php if (isset($errors['date_naissance'])): ?>
                        <div class="error-message"><?= htmlspecialchars($errors['date_naissance']) ?></div>
                    <?php endif; ?>
                </div>
                <div class="form-group">
                    <label for="lieu_naissance">Lieu de naissance*</label>
                    <input type="text" id="lieu_naissance" name="lieu_naissance" placeholder="Ville de naissance" value="<?= htmlspecialchars($lieu_naissance ?? '') ?>">
                    <?php if (isset($errors['lieu_naissance'])): ?>
                        <div class="error-message"><?= htmlspecialchars($errors['lieu_naissance']) ?></div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="adresse">Adresse*</label>
                    <input type="text" id="adresse" name="adresse" placeholder="Adresse complète" value="<?= htmlspecialchars($adresse ?? '') ?>">
                    <?php if (isset($errors['adresse'])): ?>
                        <div class="error-message"><?= htmlspecialchars($errors['adresse']) ?></div>
                    <?php endif; ?>
                </div>
                <div class="form-group">
                    <label for="email">Email*</label>
                    <input type="text" id="email" name="email" placeholder="example@email.com" value="<?= htmlspecialchars($email ?? '') ?>">
                    <?php if (isset($errors['email'])): ?>
                        <div class="error-message"><?= htmlspecialchars($errors['email']) ?></div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="telephone">Téléphone*</label>
                    <input type="text" id="telephone" name="telephone" placeholder="+221 XX XXX XX XX" value="<?= htmlspecialchars($telephone ?? '') ?>">
                    <?php if (isset($errors['telephone'])): ?>
                        <div class="error-message"><?= htmlspecialchars($errors['telephone']) ?></div>
                    <?php endif; ?>
                </div>
                <div class="form-group document-upload">
                    <div class="document-box">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                            <polyline points="14 2 14 8 20 8"></polyline>
                            <line x1="12" y1="18" x2="12" y2="12"></line>
                            <line x1="9" y1="15" x2="15" y2="15"></line>
                        </svg>
                        <button type="button" class="document-button" onclick="document.getElementById('photo').click()">Ajouter une photo</button>
                        <input type="file" id="photo" name="photo" accept="image/*" style="display: none;">
                        <p class="help-text">Formats acceptés: JPG, PNG (max 2MB)</p>
                    </div>
                    <?php if (isset($errors['photo'])): ?>
                        <div class="error-message"><?= htmlspecialchars($errors['photo']) ?></div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Section Informations du tuteur -->
        <div class="form-section">
            <div class="section-header">
                <h3>Informations du tuteur</h3>
                <button type="button" class="edit-button">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                    </svg>
                </button>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="tuteur_nom">Prénom(s) & nom</label>
                    <input type="text" id="tuteur_nom" name="tuteur_nom" placeholder="Nom complet du tuteur" value="<?= htmlspecialchars($tuteur_nom ?? '') ?>">
                    <?php if (isset($errors['tuteur_nom'])): ?>
                        <div class="error-message"><?= htmlspecialchars($errors['tuteur_nom']) ?></div>
                    <?php endif; ?>
                </div>
                <div class="form-group">
                    <label for="lien_parente">Lien de parenté</label>
                    <input type="text" id="lien_parente" name="lien_parente" placeholder="Ex: Père, Mère, Oncle..." value="<?= htmlspecialchars($lien_parente ?? '') ?>">
                    <?php if (isset($errors['lien_parente'])): ?>
                        <div class="error-message"><?= htmlspecialchars($errors['lien_parente']) ?></div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="tuteur_adresse">Adresse</label>
                    <input type="text" id="tuteur_adresse" name="tuteur_adresse" placeholder="Adresse du tuteur" value="<?= htmlspecialchars($tuteur_adresse ?? '') ?>">
                    <?php if (isset($errors['tuteur_adresse'])): ?>
                        <div class="error-message"><?= htmlspecialchars($errors['tuteur_adresse']) ?></div>
                    <?php endif; ?>
                </div>
                <div class="form-group">
                    <label for="tuteur_telephone">Téléphone</label>
                    <input type="text" id="tuteur_telephone" name="tuteur_telephone" placeholder="+221 XX XXX XX XX" value="<?= htmlspecialchars($tuteur_telephone ?? '') ?>">
                    <?php if (isset($errors['tuteur_telephone'])): ?>
                        <div class="error-message"><?= htmlspecialchars($errors['tuteur_telephone']) ?></div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Section référentiel (rendue visible) -->
        <div class="form-section">
            <div class="section-header">
                <h3>Référentiel</h3>
                <button type="button" class="edit-button">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                    </svg>
                </button>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="referentiel_id">Référentiel*</label>
                    <select id="referentiel_id" name="referentiel_id">
                        <option value="">Sélectionner un référentiel</option>
                        <?php foreach ($referentiels as $ref): ?>
                            <option value="<?= $ref['id'] ?>" <?= (isset($referentiel_id) && $referentiel_id == $ref['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($ref['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (isset($errors['referentiel_id'])): ?>
                        <div class="error-message"><?= htmlspecialchars($errors['referentiel_id']) ?></div>
                    <?php endif; ?>
                </div>
                <div class="form-group">
                    <label for="promotion">Promotion</label>
                    <input type="text" id="promotion" value="<?= htmlspecialchars($current_promotion['name']) ?>" readonly>
                    <p class="help-text">L'apprenant sera ajouté à la promotion active</p>
                </div>
            </div>
        </div>

        <!-- Boutons d'action -->
        <div class="form-actions">
            <button type="button" class="btn-cancel" onclick="window.location.href='?page=apprenants'">Annuler</button>
            <button type="submit" class="btn-submit">Enregistrer</button>
        </div>
    </form>
</div>

<style>
/* Styles spécifiques au formulaire d'ajout d'apprenant */
.form-container {
    max-width: 1000px;
    margin: 20px auto;
    background-color: white;
    border-radius: 8px;
    box-shadow: 0 1px 5px rgba(0, 0, 0, 0.1);
    padding: 30px;
}

.form-title {
    color: #0E8F7E;
    font-size: 24px;
    margin-bottom: 30px;
    text-align: center;
    font-weight: 600;
}

.form-section {
    margin-bottom: 30px;
    padding-bottom: 20px;
    border-bottom: 1px solid #f0f0f0;
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.section-header h3 {
    color: #444;
    font-size: 18px;
    font-weight: 500;
    margin: 0;
}

.edit-button {
    background: none;
    border: none;
    color: #777;
    cursor: pointer;
    padding: 5px;
}

.edit-button:hover {
    color: #333;
}

.form-row {
    display: flex;
    gap: 20px;
    margin-bottom: 15px;
}

.form-group {
    flex: 1;
    display: flex;
    flex-direction: column;
}

.form-group label {
    font-size: 14px;
    color: #555;
    margin-bottom: 8px;
}

.form-group input,
.form-group select,
.form-group textarea {
    padding: 10px 12px;
    border: 1px solid #e5e5e5;
    border-radius: 4px;
    font-size: 14px;
    color: #333;
    background-color: #f9f9f9;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
    border-color: #0E8F7E;
    outline: none;
    background-color: #fff;
}

.error-message {
    color: #ff5c5c;
    font-size: 12px;
    margin-top: 5px;
}

.document-box {
    border: 1px dashed #ccc;
    border-radius: 4px;
    padding: 20px;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 10px;
    cursor: pointer;
    transition: all 0.3s;
}

.document-box:hover {
    border-color: #0E8F7E;
    background-color: #f0f9f8;
}

.document-box svg {
    color: #0E8F7E;
}

.document-button {
    background: none;
    border: none;
    color: #0E8F7E;
    font-weight: 500;
    cursor: pointer;
    padding: 0;
}

.form-actions {
    display: flex;
    justify-content: flex-end;
    gap: 15px;
    padding-top: 20px;
}

.btn-cancel,
.btn-submit {
    padding: 10px 25px;
    border-radius: 4px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s;
}

.btn-cancel {
    background-color: white;
    color: #666;
    border: 1px solid #ddd;
}

.btn-cancel:hover {
    background-color: #f5f5f5;
}

.btn-submit {
    background-color: #0E8F7E;
    color: white;
    border: none;
}

.btn-submit:hover {
    background-color: #0a7b6c;
}

.hidden-section {
    display: none;
}

/* Responsive */
@media (max-width: 768px) {
    .form-row {
        flex-direction: column;
        gap: 15px;
    }
    
    .form-container {
        padding: 20px;
    }
}
</style>

<script>

document.getElementById('photo').addEventListener('change', function(e) {




    const file = e.target.files[0];
    if (file) {
        const fileName = file.name;
        const documentButton = document.querySelector('.document-button');
        documentButton.textContent = fileName;
        
        // Créer une prévisualisation
        const reader = new FileReader();
        reader.onload = function(e) {
            // Créer ou mettre à jour l'élément de prévisualisation
            let previewElement = document.querySelector('.photo-preview');
            if (!previewElement) {
                previewElement = document.createElement('div');
                previewElement.className = 'photo-preview';
                document.querySelector('.document-box').appendChild(previewElement);
            }
            
            // Afficher l'image
            previewElement.innerHTML = `<img src="${e.target.result}" alt="Prévisualisation" style="max-width: 100%; max-height: 150px; margin-top: 10px;">`;
        };
        reader.readAsDataURL(file);
    }
});
</script>