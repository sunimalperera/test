<?php
session_start();
include 'db.php';

// Redirect to login if not authenticated
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$type = $_GET['type'];

// Fetch data
$stmt = $conn->query("SELECT *, (quantity - issued_quantity) AS current_stock FROM spare_parts");
$parts = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($type === 'excel') {
    // Export to Excel
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment; filename="spare_parts.xls"');

    echo "ID\tPart Name\tPart Number\tQuantity\tIssued Quantity\tCurrent Stock\tReorder Level\tBin Number\n";
    foreach ($parts as $part) {
        echo "{$part['id']}\t{$part['part_name']}\t{$part['part_number']}\t{$part['quantity']}\t{$part['issued_quantity']}\t{$part['current_stock']}\t{$part['reorder_level']}\t{$part['bin_number']}\n";
    }
} elseif ($type === 'pdf') {
    // Export to PDF
    require('fpdf/fpdf.php');

    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('helvetica', 'B', 12);

    // Add headers
    $pdf->Cell(10, 10, 'ID', 1);
    $pdf->Cell(40, 10, 'Part Name', 1);
    $pdf->Cell(40, 10, 'Part Number', 1);
    $pdf->Cell(20, 10, 'Quantity', 1);
    $pdf->Cell(20, 10, 'Issued', 1);
    $pdf->Cell(20, 10, 'Stock', 1);
    $pdf->Cell(20, 10, 'Reorder', 1);
    $pdf->Cell(30, 10, 'Bin Number', 1);
    $pdf->Ln();

    // Add data
    foreach ($parts as $part) {
        $pdf->Cell(10, 10, $part['id'], 1);
        $pdf->Cell(40, 10, $part['part_name'], 1);
        $pdf->Cell(40, 10, $part['part_number'], 1);
        $pdf->Cell(20, 10, $part['quantity'], 1);
        $pdf->Cell(20, 10, $part['issued_quantity'], 1);
        $pdf->Cell(20, 10, $part['current_stock'], 1);
        $pdf->Cell(20, 10, $part['reorder_level'], 1);
        $pdf->Cell(30, 10, $part['bin_number'], 1);
        $pdf->Ln();
    }

    // Output PDF
    $pdf->Output('D', 'spare_parts.pdf');
}
?>