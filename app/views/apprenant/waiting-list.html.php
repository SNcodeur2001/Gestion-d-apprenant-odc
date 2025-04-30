<!-- app/views/apprenant/waiting-list.html.php -->
<div class="waiting-list-container">
    <div class="waiting-list-header">
        <h1>Liste d'attente des apprenants</h1>
        <div class="header-actions">
            <a href="?page=apprenants" class="btn-back">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M19 12H5M12 19l-7-7 7-7"/>
                </svg>
                Retour aux apprenants
            </a>
        </div>
    </div>

    <div class="waiting-list-content">
        <?php if (empty($waiting_list)): ?>
            <div class="empty-list">La liste d'attente est vide.</div>
        <?php else: ?>
            <div class="table-container">
                <table class="waiting-table">
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
                                        <?php foreach ($apprenant['errors'] as $field => $error): ?>
                                            <li><?= htmlspecialchars($error) ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </td>
                                <td>
                                    <form action="?page=correct-waiting-apprenant" method="POST">
                                        <input type="hidden" name="apprenant_index" value="<?= $index ?>">
                                        <button type="submit" class="btn-correct">Corriger</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
.waiting-list-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}

.waiting-list-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
}

.waiting-list-header h1 {
    margin: 0;
    color: #333;
    font-size: 24px;
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

.empty-list {
    text-align: center;
    padding: 40px;
    background-color: #f9f9f9;
    border-radius: 8px;
    color: #666;
    font-size: 16px;
}

.table-container {
    overflow-x: auto;
}

.waiting-table {
    width: 100%;
    border-collapse: collapse;
    background-color: white;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    border-radius: 8px;
    overflow: hidden;
}

.waiting-table th,
.waiting-table td {
    padding: 12px 15px;
    text-align: left;
    border-bottom: 1px solid #eee;
}

.waiting-table th {
    background-color: #F8A427;
    color: white;
    font-weight: 600;
    font-size: 14px;
}

.error-list {
    margin: 0;
    padding-left: 20px;
    font-size: 13px;
    color: #e74c3c;
}

.error-list li {
    margin-bottom: 4px;
}

.btn-correct {
    padding: 6px 12px;
    background-color: #3498db;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 13px;
    transition: background-color 0.2s;
}

.btn-correct:hover {
    background-color: #2980b9;
}
</style>