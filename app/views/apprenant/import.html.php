<!-- app/views/apprenant/import.html.php -->
<div class="import-container">
    <h1>Importer des apprenants</h1>
    
    <div class="import-info">
        <div class="alert alert-info">
            <p><strong>Promotion en cours:</strong> <?= htmlspecialchars($promotion_en_cours['name']) ?></p>
            <p>Les apprenants importés seront automatiquement associés à cette promotion.</p>
            <p><em>Note: L'importation n'est possible que pour la promotion actuellement en cours.</em></p>
        </div>
    </div>
    
    <div class="import-form-container">
        <form action="?page=process-import-apprenants" method="post" enctype="multipart/form-data" class="import-form">
            <div class="form-group">
                <label for="excel_file">Fichier Excel (.xlsx)</label>
                <input type="file" id="excel_file" name="excel_file" accept=".xlsx" required>
                <small class="form-text text-muted">Le fichier doit contenir les colonnes: nom, prenom, email, telephone, adresse</small>
            </div>
            
            <div class="form-group">
                <label for="referentiel_id">Référentiel par défaut</label>
                <select id="referentiel_id" name="referentiel_id" required>
                    <option value="">Sélectionnez un référentiel</option>
                    <?php foreach ($referentiels as $referentiel): ?>
                        <option value="<?= $referentiel['id'] ?>"><?= htmlspecialchars($referentiel['name']) ?></option>
                    <?php endforeach; ?>
                </select>
                <small class="form-text text-muted">Ce référentiel sera utilisé pour les apprenants sans référentiel spécifié</small>
            </div>
            
            <div class="form-actions">
                <a href="?page=download-apprenant-template" class="btn btn-secondary">Télécharger le modèle</a>
                <button type="submit" class="btn btn-primary">Importer</button>
            </div>
        </form>
    </div>
    
    <div class="import-instructions">
        <h3>Instructions</h3>
        <ol>
            <li>Téléchargez le modèle Excel en cliquant sur le bouton "Télécharger le modèle"</li>
            <li>Remplissez le fichier avec les informations des apprenants</li>
            <li>Sélectionnez un référentiel par défaut (utilisé si non spécifié dans le fichier)</li>
            <li>Importez le fichier complété</li>
        </ol>
        
        <h3>Colonnes obligatoires</h3>
        <ul>
            <li><strong>nom</strong>: Nom de l'apprenant</li>
            <li><strong>prenom</strong>: Prénom de l'apprenant</li>
            <li><strong>email</strong>: Email de l'apprenant (doit être unique)</li>
            <li><strong>telephone</strong>: Numéro de téléphone</li>
            <li><strong>adresse</strong>: Adresse de l'apprenant</li>
        </ul>
    </div>
</div>

<style>
.import-container {
    max-width: 800px;
    margin: 0 auto;
    padding: 20px;
    background-color: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.import-header {
    margin-bottom: 30px;
    text-align: center;
}

.import-header h1 {
    color: #19A88C;
    margin-bottom: 10px;
}

.alert {
    padding: 15px;
    margin-bottom: 20px;
    border-radius: 4px;
}

.alert-error {
    background-color: #FFE5E5;
    color: #dd5050;
    border: 1px solid #dd5050;
}

.alert-success {
    background-color: #E6F7F5;
    color: #19A88C;
    border: 1px solid #19A88C;
}

.import-form {
    margin-bottom: 30px;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 500;
}

.form-group input[type="file"] {
    width: 100%;
    padding: 10px;
    border: 1px solid #e0e0e0;
    border-radius: 4px;
    background-color: #f8f9fa;
}

.form-group select {
    width: 100%;
    padding: 10px;
    border: 1px solid #e0e0e0;
    border-radius: 4px;
}

.help-text {
    font-size: 12px;
    color: #666;
    margin-top: 5px;
}

.form-actions {
    display: flex;
    justify-content: space-between;
    margin-top: 30px;
}

.cancel-button, .submit-button, .template-button {
    padding: 10px 20px;
    border-radius: 4px;
    font-weight: 500;
    text-decoration: none;
    cursor: pointer;
}

.cancel-button {
    background-color: #f8f9fa;
    color: #666;
    border: 1px solid #e0e0e0;
}

.submit-button {
    background-color: #19A88C;
    color: white;
    border: none;
}

.template-button {
    background-color: #4073ff;
    color: white;
    border: none;
    display: inline-block;
    margin-top: 10px;
}

.template-section {
    background-color: #f8f9fa;
    padding: 20px;
    border-radius: 4px;
    text-align: center;
}
</style>