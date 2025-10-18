<?php
/**
 * Test API Endpoints
 * Bu dosya API endpoint'lerinin doğru çalışıp çalışmadığını test eder
 */

// Include API router for handling API requests
require_once 'api-router.php';

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API Endpoints Test</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .endpoint { margin: 10px 0; padding: 10px; border: 1px solid #ddd; }
        .success { background-color: #d4edda; border-color: #c3e6cb; }
        .error { background-color: #f8d7da; border-color: #f5c6cb; }
        .status { font-weight: bold; }
        .response { margin-top: 10px; font-family: monospace; font-size: 12px; }
    </style>
</head>
<body>
    <h1>API Endpoints Test</h1>
    
    <div id="results"></div>
    
    <script>
        const apiBase = window.location.origin + '/api';
        const endpoints = [
            'settings',
            'services', 
            'portfolio',
            'blog',
            'blog/recent',
            'contact-info',
            'content'
        ];
        
        async function testEndpoint(endpoint) {
            const url = `${apiBase}/${endpoint}`;
            const resultDiv = document.createElement('div');
            resultDiv.className = 'endpoint';
            
            try {
                const response = await fetch(url);
                const status = response.status;
                const isSuccess = response.ok;
                
                resultDiv.className = `endpoint ${isSuccess ? 'success' : 'error'}`;
                resultDiv.innerHTML = `
                    <div class="status">${endpoint}: ${status} ${isSuccess ? '✓' : '✗'}</div>
                    <div class="response">URL: ${url}</div>
                `;
                
                if (isSuccess) {
                    try {
                        const data = await response.json();
                        resultDiv.innerHTML += `<div class="response">Response: ${JSON.stringify(data, null, 2)}</div>`;
                    } catch (e) {
                        resultDiv.innerHTML += `<div class="response">Response parsing failed: ${e.message}</div>`;
                    }
                } else {
                    resultDiv.innerHTML += `<div class="response">Error: ${response.statusText}</div>`;
                }
                
            } catch (error) {
                resultDiv.className = 'endpoint error';
                resultDiv.innerHTML = `
                    <div class="status">${endpoint}: Error ✗</div>
                    <div class="response">URL: ${url}</div>
                    <div class="response">Error: ${error.message}</div>
                `;
            }
            
            document.getElementById('results').appendChild(resultDiv);
        }
        
        async function testAllEndpoints() {
            for (const endpoint of endpoints) {
                await testEndpoint(endpoint);
                // Small delay between requests
                await new Promise(resolve => setTimeout(resolve, 100));
            }
        }
        
        // Start testing when page loads
        window.addEventListener('load', testAllEndpoints);
    </script>
</body>
</html>
