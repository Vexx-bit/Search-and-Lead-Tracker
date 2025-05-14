<?php
require_once '../config/database.php';

// Set headers for CSV download
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="leads.csv"');

// Create output stream
$output = fopen('php://output', 'w');

// Add CSV headers
fputcsv($output, ['ID', 'Company Name', 'Niche', 'Location', 'Status', 'Notes', 'Created At', 'Updated At']);

// Get all leads
$sql = "SELECT * FROM leads ORDER BY created_at DESC";
$result = $conn->query($sql);

// Add leads to CSV
while ($row = $result->fetch_assoc()) {
    fputcsv($output, [
        $row['id'],
        $row['company_name'],
        $row['niche'],
        $row['location'],
        $row['status'],
        $row['notes'],
        $row['created_at'],
        $row['updated_at']
    ]);
}

fclose($output);
$conn->close();
