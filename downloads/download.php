<?php
// Define the relative path to the downloads directory (which is the same as the script location in this case)
$downloadDir = __DIR__ . '/';

// Define the correct path to track download counts
$countsFile = $downloadDir . '/../database/download_counts.csv';

// Get the file from the URL
if (isset($_GET['file'])) {
    $file = basename($_GET['file']); // Prevent directory traversal attacks
    $filePath = $downloadDir . $file;

    // Check if the file exists
    if (file_exists($filePath)) {
        // Read the current counts from the CSV file
        $counts = [];
        if (($handle = fopen($countsFile, 'r')) !== false) {
            while (($data = fgetcsv($handle)) !== false) {
                $counts[$data[0]] = $data[1];
            }
            fclose($handle);
        }

        // Increment the count for the downloaded file
        if (isset($counts[$file])) {
            $counts[$file]++;
        } else {
            $counts[$file] = 1; // Initialize count if not present
        }

        // Write the updated counts back to the CSV file
        if (($handle = fopen($countsFile, 'w')) !== false) {
            foreach ($counts as $filename => $count) {
                fputcsv($handle, [$filename, $count]);
            }
            fclose($handle);
        }

        // Force file download
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $file . '"');
        header('Content-Length: ' . filesize($filePath));
        readfile($filePath);
        exit;
    } else {
        echo 'File not found!';
    }
} else {
    echo 'No file specified!';
}
