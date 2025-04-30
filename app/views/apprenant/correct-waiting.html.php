</div>
                <div class="form-group">
                    <label for="nom">Nom*</label>
                    <input type="text" id="nom" name="nom" value="<?= htmlspecialchars($apprenant['nom'] ?? '') ?>">
                    <?php if (isset($errors['nom'])): ?>
                        <div class="error-message"><?= htmlspecialchars($errors['nom']) ?></div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="date_naissance">Date de naissance* (JJ/MM/AAAA)</label>
                    <input type="text" id="date_naissance" name="date_naissance" value="<?= htmlspecialchars($apprenant['date_naissance'] ?? '') ?>">
                    <?php if (isset($errors['date_naissance'])): ?>
                        <div class="error-message"><?= htmlspecialchars($errors['date_naissance']) ?></div>
                    <?php endif; ?>
                </div>
                <div class="form-group">
                    <label for="lieu_naissance">Lieu de naissance*</label>
                    <input type="text" id="lieu_naissance" name="lieu_naissance" value="<?= htmlspecialchars($apprenant['lieu_naissance'] ?? '') ?>">
                    <?php if (isset($errors['lieu_naissance'])): ?>
                        <div class="error-message"><?= htmlspecialchars($errors['lieu_naissance']) ?></div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="adresse">Adresse*</label>
                    <input type="text" id="adresse" name="adresse" value="<?= htmlspecialchars($apprenant['adresse'] ?? '') ?>">
                    <?php if (isset($errors['adresse'])): ?>
                        <div class="error-message"><?= htmlspecialchars($errors['adresse']) ?></div>
                    <?php endif; ?>
                </div>
                <div class="form-group">
                    <label for="email">Email*</label>
                    <input type="text" id="email" name="email" value="<?= htmlspecialchars($apprenant['email'] ?? '') ?>">
                    <?php if (isset($errors['email'])): ?>
                        <div class="error-message"><?= htmlspecialchars($errors['email']) ?></div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="telephone">Téléphone*</label>
                    <input type="text" id="telephone" name="telephone" value="<?= htmlspecialchars($apprenant['telephone'] ?? '') ?>">
                    <?php if (isset($errors['telephone'])): ?>
                        <div class="error-message"><?= htmlspecialchars($errors['telephone']) ?></div>
                    <?php endif; ?>
                </div>
                <div class="form-group">
                    <label for="referentiel_id">Référentiel*</label>
                    <select id="referentiel_id" name="referentiel_id">
                        <option value="">Sélectionner un référentiel</option>
                        <?php foreach ($referentiels as $ref): ?>
                            <option value="<?= $ref['id'] ?>" <?= (isset($apprenant['referentiel_id']) && $apprenant['referentiel_id'] == $ref['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($ref['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (isset($errors['referentiel_id'])): ?>
                        <div class="error-message"><?= htmlspecialchars($errors['referentiel_id']) ?></div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Boutons d'action -->
        <div class="form-actions">
            <a href="?page=apprenants&tab=waiting" class="btn-cancel">Annuler</a>
            <button type="submit" class="btn-submit">Enregistrer</button>
        </div>
    </form>
</div>

<style>
/* Styles spécifiques au formulaire de correction */
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
    text-decoration: none;
    display: inline-flex;
    align-items: center;
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