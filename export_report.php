<?php
session_start();
include 'db.php';

// Redirect to login if not authenticated
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$type = $_GET['type']; // Get the export type (excel or pdf)

// Fetch spare parts data
try {
    $stmt = $conn->query("SELECT *, (quantity - issued_quantity) AS current_stock FROM spare_parts");
    $parts = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching spare parts: " . $e->getMessage());
}

if ($type === 'excel') {
    // Export to Excel
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment; filename="spare_parts_report.xls"');

    // Output Excel headers
    echo "ID\tPart Name\tPart Number\tQuantity\tIssued Quantity\tCurrent Stock\tReorder Level\tPrice\tBin Number\tSupplier\tTotal Price\n";

    // Output data rows
    foreach ($parts as $part) {
        echo "{$part['id']}\t{$part['part_name']}\t{$part['part_number']}\t{$part['quantity']}\t{$part['issued_quantity']}\t{$part['current_stock']}\t{$part['reorder_level']}\t{$part['price']}\t{$part['bin_number']}\t{$part['supplier']}\t{$part['total_price']}\n";
    }
} elseif ($type === 'pdf') {
    // Export to PDF
    require('fpdf/fpdf.php'); // Include the FPDF library

    // Create a new PDF instance
    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 12);

    // Add headers
    $pdf->Cell(10, 10, 'ID', 1);
    $pdf->Cell(40, 10, 'Part Name', 1);
    $pdf->Cell(30, 10, 'Part Number', 1);
    $pdf->Cell(20, 10, 'Quantity', 1);
    $pdf->Cell(20, 10, 'Issued', 1);
    $pdf->Cell(20, 10, 'Stock', 1);
    $pdf->Cell(20, 10, 'Reorder', 1);
    $pdf->Cell(20, 10, 'Price', 1);
    $pdf->Cell(30, 10, 'Bin Number', 1);
    $pdf->Cell(30, 10, 'Supplier', 1);
    $pdf->Cell(30, 10, 'Total Price', 1);
    $pdf->Ln();

    // Add data rows
    foreach ($parts as $part) {
        $pdf->Cell(10, 10, $part['id'], 1);
        $pdf->Cell(40, 10, $part['part_name'], 1);
        $pdf->Cell(30, 10, $part['part_number'], 1);
        $pdf->Cell(20, 10, $part['quantity'], 1);
        $pdf->Cell(20, 10, $part['issued_quantity'], 1);
        $pdf->Cell(20, 10, $part['current_stock'], 1);
        $pdf->Cell(20, 10, $part['reorder_level'], 1);
        $pdf->Cell(20, 10, '$' . number_format($part['price'], 2), 1);
        $pdf->Cell(30, 10, $part['bin_number'], 1);
        $pdf->Cell(30, 10, $part['supplier'], 1);
        $pdf->Cell(30, 10, '$' . number_format($part['total_price'], 2), 1);
        $pdf->Ln();
    }

    // Output the PDF
    $pdf->Output('D', 'spare_parts_report.pdf');
} else {
    // Invalid export type
    die("Invalid export type.");
}
?>