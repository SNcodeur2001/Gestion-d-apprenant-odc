<?php

namespace App\Services\Apprenant;

/**
 * Exporte les apprenants au format CSV
 * 
 * @param array $apprenants Liste des apprenants
 * @param array $referentiels_map Mapping des référentiels
 * @param array $current_promotion Promotion courante
 * @return string Contenu CSV
 */
function export_apprenants_csv($apprenants, $referentiels_map, $current_promotion) {
    // Création du contenu CSV
    $csv_content = "Matricule,Nom,Prénom,Email,Téléphone,Adresse,Référentiel,Statut\n";
    
    foreach ($apprenants as $apprenant) {
        $referentiel_name = isset($referentiels_map[$apprenant['referentiel_id']]) 
            ? $referentiels_map[$apprenant['referentiel_id']] 
            : $apprenant['referentiel_id'];
        
        $csv_content .= sprintf(
            "%s,%s,%s,%s,%s,%s,%s,%s\n",
            $apprenant['matricule'],
            str_replace(',', ' ', $apprenant['nom']),
            str_replace(',', ' ', $apprenant['prenom']),
            $apprenant['email'],
            $apprenant['telephone'],
            str_replace(',', ' ', $apprenant['adresse']),
            str_replace(',', ' ', $referentiel_name),
            $apprenant['statut']
        );
    }
    
    return $csv_content;
}

/**
 * Exporte les apprenants au format Excel
 * 
 * @param array $apprenants Liste des apprenants
 * @param array $referentiels_map Mapping des référentiels
 * @param array $current_promotion Promotion courante
 */
function export_apprenants_excel($apprenants, $referentiels_map, $current_promotion) {
    // Charger la bibliothèque PhpSpreadsheet
    require_once __DIR__ . '/../../../vendor/autoload.php';
    
    // Créer un nouveau document Excel
    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    
    // Définir les en-têtes
    $sheet->setCellValue('A1', 'Matricule');
    $sheet->setCellValue('B1', 'Nom');
    $sheet->setCellValue('C1', 'Prénom');
    $sheet->setCellValue('D1', 'Email');
    $sheet->setCellValue('E1', 'Téléphone');
    $sheet->setCellValue('F1', 'Adresse');
    $sheet->setCellValue('G1', 'Référentiel');
    $sheet->setCellValue('H1', 'Statut');
    
    // Style pour les en-têtes
    $headerStyle = [
        'font' => [
            'bold' => true,
            'color' => ['rgb' => 'FFFFFF'],
        ],
        'fill' => [
            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
            'startColor' => ['rgb' => '4F81BD'],
        ],
    ];
    $sheet->getStyle('A1:H1')->applyFromArray($headerStyle);
    
    // Remplir les données
    $row = 2;
    foreach ($apprenants as $apprenant) {
        $referentiel_name = isset($referentiels_map[$apprenant['referentiel_id']]) 
            ? $referentiels_map[$apprenant['referentiel_id']] 
            : $apprenant['referentiel_id'];
        
        $sheet->setCellValue('A' . $row, $apprenant['matricule']);
        $sheet->setCellValue('B' . $row, $apprenant['nom']);
        $sheet->setCellValue('C' . $row, $apprenant['prenom']);
        $sheet->setCellValue('D' . $row, $apprenant['email']);
        $sheet->setCellValue('E' . $row, $apprenant['telephone']);
        $sheet->setCellValue('F' . $row, $apprenant['adresse']);
        $sheet->setCellValue('G' . $row, $referentiel_name);
        $sheet->setCellValue('H' . $row, $apprenant['statut']);
        
        $row++;
    }
    
    // Ajuster la largeur des colonnes automatiquement
    foreach (range('A', 'H') as $col) {
        $sheet->getColumnDimension($col)->setAutoSize(true);
    }
    
    // Créer le writer Excel
    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
    
    // Configuration des headers pour le téléchargement
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="apprenants_' . $current_promotion['id'] . '.xlsx"');
    header('Cache-Control: max-age=0');
    
    // Output du fichier Excel
    $writer->save('php://output');
    exit;
}

/**
 * Exporte les apprenants au format PDF
 * 
 * @param array $apprenants Liste des apprenants
 * @param array $referentiels_map Mapping des référentiels
 * @param array $current_promotion Promotion courante
 */
function export_apprenants_pdf($apprenants, $referentiels_map, $current_promotion) {
    // Charger la bibliothèque TCPDF
    require_once __DIR__ . '/../../../vendor/autoload.php';
    
    // Créer un nouveau document PDF
    $pdf = new \TCPDF('L', 'mm', 'A4', true, 'UTF-8');
    
    // Définir les informations du document
    $pdf->SetCreator('Ges-Apprenant');
    $pdf->SetAuthor('Sonatel Academy');
    $pdf->SetTitle('Liste des Apprenants - ' . $current_promotion['name']);
    
    // Supprimer les en-têtes et pieds de page par défaut
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);
    
    // Définir les marges
    $pdf->SetMargins(10, 10, 10);
    
    // Ajouter une page
    $pdf->AddPage();
    
    // Définir la police
    $pdf->SetFont('helvetica', 'B', 14);
    
    // Titre
    $pdf->Cell(0, 10, 'Liste des Apprenants - ' . $current_promotion['name'], 0, 1, 'C');
    $pdf->Ln(5);
    
    // En-têtes du tableau
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->SetFillColor(79, 129, 189);
    $pdf->SetTextColor(255);
    
    $pdf->Cell(30, 7, 'Matricule', 1, 0, 'C', true);
    $pdf->Cell(30, 7, 'Nom', 1, 0, 'C', true);
    $pdf->Cell(30, 7, 'Prénom', 1, 0, 'C', true);
    $pdf->Cell(50, 7, 'Email', 1, 0, 'C', true);
    $pdf->Cell(30, 7, 'Téléphone', 1, 0, 'C', true);
    $pdf->Cell(40, 7, 'Référentiel', 1, 0, 'C', true);
    $pdf->Cell(20, 7, 'Statut', 1, 1, 'C', true);
    
    // Contenu du tableau
    $pdf->SetFont('helvetica', '', 9);
    $pdf->SetTextColor(0);
    $fill = false;
    
    foreach ($apprenants as $apprenant) {
        $referentiel_name = isset($referentiels_map[$apprenant['referentiel_id']]) 
            ? $referentiels_map[$apprenant['referentiel_id']] 
            : $apprenant['referentiel_id'];
        
        $pdf->Cell(30, 6, $apprenant['matricule'], 1, 0, 'L', $fill);
        $pdf->Cell(30, 6, $apprenant['nom'], 1, 0, 'L', $fill);
        $pdf->Cell(30, 6, $apprenant['prenom'], 1, 0, 'L', $fill);
        $pdf->Cell(50, 6, $apprenant['email'], 1, 0, 'L', $fill);
        $pdf->Cell(30, 6, $apprenant['telephone'], 1, 0, 'L', $fill);
        $pdf->Cell(40, 6, $referentiel_name, 1, 0, 'L', $fill);
        $pdf->Cell(20, 6, $apprenant['statut'], 1, 1, 'C', $fill);
        
        $fill = !$fill; // Alterner les couleurs de fond
    }
    
    // Générer le PDF
    $pdf->Output('apprenants_' . $current_promotion['id'] . '.pdf', 'D');
    exit;
}

/**
 * Génère un template Excel pour l'import des apprenants
 */
function generate_apprenant_template() {
    // Charger la bibliothèque PhpSpreadsheet
    require_once __DIR__ . '/../../../vendor/autoload.php';
    
    // Créer un nouveau document Excel
    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    
    // Définir les en-têtes
    $headers = ['nom', 'prenom', 'email', 'telephone', 'adresse', 'date_naissance', 'lieu_naissance', 'referentiel_id'];
    
    // Ajouter les en-têtes à la première ligne
    foreach ($headers as $index => $header) {
        $column = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($index);
        $sheet->setCellValue($column . '1', $header);
    }
    
    // Style pour les en-têtes
    $headerStyle = [
        'font' => [
            'bold' => true,
            'color' => ['rgb' => 'FFFFFF'],
        ],
        'fill' => [
            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
            'startColor' => ['rgb' => '4F81BD'],
        ],
    ];
    $sheet->getStyle('A1:' . \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($headers) - 1) . '1')->applyFromArray($headerStyle);
    
    // Ajuster la largeur des colonnes automatiquement
    foreach (range(0, count($headers) - 1) as $col) {
        $column = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);
        $sheet->getColumnDimension($column)->setAutoSize(true);
    }
    
    // Ajouter quelques exemples de données
    $examples = [
        ['Doe', 'John', 'john.doe@example.com', '771234567', 'Dakar, Senegal', '1995-05-15', 'Dakar', ''],
        ['Smith', 'Jane', 'jane.smith@example.com', '772345678', 'Thies, Senegal', '1997-08-22', 'Thies', ''],
    ];
    
    foreach ($examples as $rowIndex => $rowData) {
        foreach ($rowData as $colIndex => $cellValue) {
            $column = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex);
            $sheet->setCellValue($column . ($rowIndex + 2), $cellValue);
        }
    }
    
    // Créer le writer Excel
    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
    
    // Configuration des headers pour le téléchargement
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="template_import_apprenants.xlsx"');
    header('Cache-Control: max-age=0');
    
    // Output du fichier Excel
    $writer->save('php://output');
    exit;
}

// Exporter les fonctions
$apprenant_export_services = [
    'export_apprenants_csv' => 'App\Services\Apprenant\export_apprenants_csv',
    'export_apprenants_excel' => 'App\Services\Apprenant\export_apprenants_excel',
    'export_apprenants_pdf' => 'App\Services\Apprenant\export_apprenants_pdf',
    'generate_apprenant_template' => 'App\Services\Apprenant\generate_apprenant_template'
];