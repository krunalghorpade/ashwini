<?php
header('Content-Type: application/json');

// Get JSON POST payload
$json = file_get_contents('php://input');
$data = json_decode($json, true);

if ($data) {
    // Basic sanitization
    $name = strip_tags($data['fullName'] ?? '');
    $dob = strip_tags($data['dob'] ?? '');
    $location = strip_tags($data['location'] ?? '');
    $email = filter_var($data['email'] ?? '', FILTER_SANITIZE_EMAIL);
    $contact = strip_tags($data['contactNumber'] ?? '');
    $insta = strip_tags($data['instaId'] ?? '');
    $message = strip_tags($data['specialMessage'] ?? '');
    $consent = strip_tags($data['consent'] ?? '');
    $timestamp = date('Y-m-d H:i:s');

    require_once 'database.php';
    $pdo = getDBConnection();
    
    if ($pdo) {
        $insertQuery = "INSERT INTO submissions (timestamp, full_name, dob, location, email, contact_number, insta_id, message, consent) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($insertQuery);
        $stmt->execute([$timestamp, $name, $dob, $location, $email, $contact, $insta, $message, $consent]);

        
        // Generate Legal PDF
        require('fpdf.php');
        $pdf = new FPDF();
        $pdf->AddPage();
        
        // Title
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->Cell(0, 10, 'VIDEO RELEASE AND CONSENT AGREEMENT', 0, 1, 'C');
        $pdf->SetFont('Arial', 'I', 10);
        $pdf->Cell(0, 10, 'Generated on: ' . $timestamp, 0, 1, 'C');
        $pdf->Ln(10);
        
        // Body Text
        $pdf->SetFont('Arial', '', 11);
        $legalText = "This agreement is entered into between " . ($name ? $name : 'the Submitter') . " (hereinafter referred to as the \"Releasor\") and Krunal Ghorpade / Kratex (hereinafter referred to as the \"Releasee\").\n\n" .
        "1. GRANT OF RIGHTS: The Releasor hereby grants the Releasee the absolute, irrevocable, and unrestricted right and permission to use, reuse, edit, alter, publish, broadcast, and distribute the video clips submitted by the Releasor for the \"Ashwini Reloaded\" music video and any related promotional materials (including but not limited to reels, teasers, social media posts).\n\n" .
        "2. NO FINANCIAL COMPENSATION: The Releasor acknowledges and agrees that their participation is entirely voluntary and that they will not receive financial compensation, royalties, or residual payments for the use of their submitted video clips.\n\n" .
        "3. WAIVER OF APPROVAL: The Releasor waives any right to inspect or approve the finished product, including any written copy or edited video wherein their likeness appears.\n\n" .
        "4. RELEASE OF LIABILITY: The Releasor releases and discharges the Releasee from any and all claims, demands, and causes of action arising out of or in connection with the use of the video clips, including but not limited to claims for invasion of privacy or defamation.\n\n" .
        "Accepted and agreed by:\n" .
        "Full Name: " . $name . "\n" .
        "Email: " . $email . "\n" .
        "Instagram ID: " . $insta . "\n" .
        "Date of Consent: " . $timestamp;
        
        $pdf->MultiCell(0, 7, $legalText);
        
        // Save PDF
        $safeName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $name);
        if (empty($safeName)) { $safeName = 'User'; }
        $pdfFilename = 'agreements/Release_' . $safeName . '_' . time() . '.pdf';
        $pdf->Output('F', $pdfFilename);
        
        echo json_encode(['status' => 'success', 'message' => 'Data saved successfully']);
    } else {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Could not write to file']);
    }
} else {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Invalid data']);
}
?>
