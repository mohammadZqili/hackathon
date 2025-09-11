<?php

// Simple script to test each route
$routes = [
    '/team-leader/dashboard',
    '/team-leader/team',
    '/team-leader/team/edit',
    '/team-leader/idea',
    '/team-leader/idea/create',
    '/team-leader/idea/edit',
];

$base_url = 'http://127.0.0.1:8000';

foreach ($routes as $route) {
    echo "\n\nTesting: $route\n";
    echo str_repeat('-', 50) . "\n";
    
    $ch = curl_init($base_url . $route);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $body = substr($response, $headerSize);
    
    curl_close($ch);
    
    echo "Status: $httpCode\n";
    
    // Check for errors in response
    if (preg_match('/<title>(.*?)<\/title>/i', $body, $matches)) {
        echo "Title: " . $matches[1] . "\n";
    }
    
    if (strpos($body, 'Exception') !== false || strpos($body, 'Error') !== false) {
        // Extract error message
        if (preg_match('/message["\']?\s*[:=]\s*["\']([^"\']+)["\']/', $body, $matches)) {
            echo "ERROR: " . $matches[1] . "\n";
        }
        
        // Extract file and line
        if (preg_match('/file["\']?\s*[:=]\s*["\']([^"\']+)["\']/', $body, $matches)) {
            echo "File: " . basename($matches[1]) . "\n";
        }
        
        if (preg_match('/line["\']?\s*[:=]\s*(\d+)/', $body, $matches)) {
            echo "Line: " . $matches[1] . "\n";
        }
    }
    
    // Check if it's a redirect to login
    if ($httpCode == 302 || $httpCode == 301) {
        echo "Redirected (likely to login)\n";
    }
}