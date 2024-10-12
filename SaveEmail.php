<?php
session_start();


// Get the email from the POST request
if (isset($_POST['email'])) {
    $email = $_POST['email'];
    
    // Sanitize the email input
    $email = filter_var($email, FILTER_SANITIZE_EMAIL);
    
    // Prevent CSV injection
    function sanitizeForCSV($data) {
        if (preg_match('/^[=+\-@]/', $data)) {
            $data = "'" . $data;
        }
        return $data;
    }
    $email = sanitizeForCSV($email);


    // Specify the file to store emails
    $file = 'database/emails.csv';
    
    // Append the email to the CSV file
    $handle = fopen($file, 'a');
    fputcsv($handle, [$email]);
    fclose($handle);
    
    // Send a response back to the AJAX call
    echo 'Email saved successfully!';
} else {
    echo 'Invalid request!';
}
?>
