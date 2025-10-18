<!DOCTYPE html>
<html>
<head>
    <title>Simple API Test</title>
</head>
<body>
    <h1>API Integration Test</h1>
    <div id="status">Loading...</div>
    <div id="blogGrid"></div>
    
    <script src="js/api-integration.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', async () => {
            const status = document.getElementById('status');
            
            // Test 1: Check if API integration exists
            status.innerHTML += '<br>API Integration exists: ' + (!!window.apiIntegration);
            
            if (!window.apiIntegration) {
                status.innerHTML += '<br>ERROR: API Integration not found!';
                return;
            }
            
            // Test 2: Test direct API call
            try {
                const response = await fetch('/api/blog');
                status.innerHTML += '<br>Direct API status: ' + response.status;
                
                if (response.ok) {
                    const data = await response.json();
                    status.innerHTML += '<br>API returned ' + data.length + ' posts';
                    
                    // Test 3: Try to load blog page
                    status.innerHTML += '<br>Calling loadBlogPage...';
                    await window.apiIntegration.loadBlogPage();
                    
                    // Check results
                    setTimeout(() => {
                        const blogGrid = document.getElementById('blogGrid');
                        const blogCards = blogGrid.querySelectorAll('.blog-card');
                        status.innerHTML += '<br>Blog cards created: ' + blogCards.length;
                        
                        if (blogCards.length > 0) {
                            status.innerHTML += '<br>SUCCESS: Blog posts loaded!';
                        } else {
                            status.innerHTML += '<br>ERROR: No blog cards created';
                            status.innerHTML += '<br>BlogGrid HTML: ' + blogGrid.innerHTML.substring(0, 200);
                        }
                    }, 1000);
                } else {
                    status.innerHTML += '<br>ERROR: API returned ' + response.status;
                }
            } catch (error) {
                status.innerHTML += '<br>ERROR: ' + error.message;
            }
        });
    </script>
</body>
</html>