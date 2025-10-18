<?php
// Theme Test - Tüm Sayfalarda Gece Gündüz Modu Kontrolü
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="az" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Theme Test - NextCode Group</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/theme.css">
    <link rel="stylesheet" href="css/theme-variables.css">
    <link rel="stylesheet" href="css/theme-support.css">
    <link rel="stylesheet" href="css/styles.css">
    
    <style>
        .theme-test-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }
        
        .theme-test-section {
            margin-bottom: 3rem;
            padding: 2rem;
            border-radius: 1rem;
            background-color: var(--surface-color);
            border: 1px solid var(--border-color);
            box-shadow: 0 4px 15px var(--shadow-color);
        }
        
        .theme-test-title {
            color: var(--text-color);
            margin-bottom: 1.5rem;
            font-size: 1.5rem;
            font-weight: 700;
        }
        
        .theme-test-item {
            margin-bottom: 1rem;
            padding: 1rem;
            border-radius: 0.5rem;
            background-color: var(--background-secondary);
            border: 1px solid var(--border-light);
        }
        
        .theme-test-item h4 {
            color: var(--text-color);
            margin-bottom: 0.5rem;
        }
        
        .theme-test-item p {
            color: var(--text-secondary);
            margin-bottom: 0;
        }
        
        .theme-status {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 1rem;
            font-size: 0.875rem;
            font-weight: 600;
        }
        
        .theme-status.success {
            background-color: var(--success-color);
            color: white;
        }
        
        .theme-status.warning {
            background-color: var(--warning-color);
            color: white;
        }
        
        .theme-status.error {
            background-color: var(--error-color);
            color: white;
        }
        
        .theme-toggle-test {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
        }
        
        .theme-toggle-test button {
            background-color: var(--surface-color);
            border: 1px solid var(--border-color);
            color: var(--text-color);
            padding: 0.75rem;
            border-radius: 50%;
            width: 60px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px var(--shadow-color);
        }
        
        .theme-toggle-test button:hover {
            background-color: var(--primary-color);
            color: white;
            transform: scale(1.1);
        }
        
        .test-card {
            background-color: var(--surface-color);
            border: 1px solid var(--border-color);
            border-radius: 0.75rem;
            padding: 1.5rem;
            margin-bottom: 1rem;
            box-shadow: 0 2px 10px var(--shadow-color);
        }
        
        .test-card h5 {
            color: var(--text-color);
            margin-bottom: 1rem;
        }
        
        .test-card p {
            color: var(--text-secondary);
            margin-bottom: 0.5rem;
        }
        
        .test-card .btn {
            margin-top: 1rem;
        }
        
        .form-control {
            background-color: var(--surface-color);
            border-color: var(--border-color);
            color: var(--text-color);
        }
        
        .form-control:focus {
            background-color: var(--surface-color);
            border-color: var(--primary-color);
            color: var(--text-color);
            box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
        }
        
        .form-label {
            color: var(--text-color);
        }
        
        .alert {
            background-color: var(--surface-elevated);
            border-color: var(--border-color);
            color: var(--text-color);
        }
        
        .alert-primary {
            background-color: rgba(52, 152, 219, 0.1);
            border-color: var(--primary-color);
            color: var(--primary-color);
        }
        
        .alert-success {
            background-color: rgba(46, 204, 113, 0.1);
            border-color: var(--success-color);
            color: var(--success-color);
        }
        
        .alert-warning {
            background-color: rgba(243, 156, 18, 0.1);
            border-color: var(--warning-color);
            color: var(--warning-color);
        }
        
        .alert-danger {
            background-color: rgba(231, 76, 60, 0.1);
            border-color: var(--error-color);
            color: var(--error-color);
        }
        
        .table {
            color: var(--text-color);
        }
        
        .table th {
            background-color: var(--surface-elevated);
            border-color: var(--border-color);
            color: var(--text-color);
        }
        
        .table td {
            border-color: var(--border-color);
            color: var(--text-color);
        }
        
        .table-striped tbody tr:nth-of-type(odd) {
            background-color: var(--surface-elevated);
        }
        
        .modal-content {
            background-color: var(--surface-color);
            border-color: var(--border-color);
            color: var(--text-color);
        }
        
        .modal-header {
            background-color: var(--surface-elevated);
            border-bottom-color: var(--border-color);
        }
        
        .modal-title {
            color: var(--text-color);
        }
        
        .modal-body {
            background-color: var(--surface-color);
            color: var(--text-color);
        }
        
        .modal-footer {
            background-color: var(--surface-elevated);
            border-top-color: var(--border-color);
        }
        
        .btn-close {
            filter: invert(1);
        }
        
        .dropdown-menu {
            background-color: var(--surface-color);
            border-color: var(--border-color);
        }
        
        .dropdown-item {
            color: var(--text-color);
        }
        
        .dropdown-item:hover {
            background-color: var(--surface-elevated);
            color: var(--text-color);
        }
        
        .nav-tabs {
            border-bottom-color: var(--border-color);
        }
        
        .nav-tabs .nav-link {
            color: var(--text-color);
            border-color: transparent;
        }
        
        .nav-tabs .nav-link:hover {
            border-color: var(--border-color);
            color: var(--text-color);
        }
        
        .nav-tabs .nav-link.active {
            background-color: var(--surface-color);
            border-color: var(--border-color);
            color: var(--text-color);
        }
        
        .accordion-item {
            background-color: var(--surface-color);
            border-color: var(--border-color);
        }
        
        .accordion-header button {
            background-color: var(--surface-elevated);
            color: var(--text-color);
        }
        
        .accordion-header button:not(.collapsed) {
            background-color: var(--primary-color);
            color: white;
        }
        
        .accordion-body {
            background-color: var(--surface-color);
            color: var(--text-color);
        }
        
        .progress {
            background-color: var(--surface-elevated);
        }
        
        .progress-bar {
            background-color: var(--primary-color);
        }
        
        .spinner-border {
            color: var(--primary-color);
        }
        
        .spinner-grow {
            background-color: var(--primary-color);
        }
        
        .text-muted {
            color: var(--text-muted) !important;
        }
        
        .text-primary {
            color: var(--primary-color) !important;
        }
        
        .text-success {
            color: var(--success-color) !important;
        }
        
        .text-warning {
            color: var(--warning-color) !important;
        }
        
        .text-danger {
            color: var(--error-color) !important;
        }
        
        .text-info {
            color: var(--info-color) !important;
        }
        
        .bg-primary {
            background-color: var(--primary-color) !important;
        }
        
        .bg-success {
            background-color: var(--success-color) !important;
        }
        
        .bg-warning {
            background-color: var(--warning-color) !important;
        }
        
        .bg-danger {
            background-color: var(--error-color) !important;
        }
        
        .bg-info {
            background-color: var(--info-color) !important;
        }
        
        .border {
            border-color: var(--border-color) !important;
        }
        
        .border-primary {
            border-color: var(--primary-color) !important;
        }
        
        .border-success {
            border-color: var(--success-color) !important;
        }
        
        .border-warning {
            border-color: var(--warning-color) !important;
        }
        
        .border-danger {
            border-color: var(--error-color) !important;
        }
        
        .border-info {
            border-color: var(--info-color) !important;
        }
        
        /* Dark mode specific styles */
        [data-theme="dark"] {
            --primary-color: #667eea;
            --primary-hover: #5a6fd8;
            --secondary-color: #2ecc71;
            --secondary-hover: #58d68d;
            --background-color: #0d1117;
            --background-secondary: #161b22;
            --surface-color: #21262d;
            --surface-elevated: #30363d;
            --text-color: #f0f6fc;
            --text-secondary: #8b949e;
            --text-muted: #6e7681;
            --border-color: #30363d;
            --border-light: #21262d;
            --shadow-color: rgba(0, 0, 0, 0.3);
            --shadow-heavy: rgba(0, 0, 0, 0.5);
            --success-color: #238636;
            --warning-color: #d29922;
            --error-color: #da3633;
            --info-color: #1f6feb;
        }
        
        /* High contrast mode */
        [data-theme="high-contrast"] {
            --primary-color: #0000ff;
            --primary-hover: #0000cc;
            --secondary-color: #008000;
            --secondary-hover: #006600;
            --background-color: #ffffff;
            --background-secondary: #f0f0f0;
            --surface-color: #ffffff;
            --surface-elevated: #ffffff;
            --text-color: #000000;
            --text-secondary: #000000;
            --text-muted: #333333;
            --border-color: #000000;
            --border-light: #666666;
            --shadow-color: rgba(0, 0, 0, 0.8);
            --shadow-heavy: rgba(0, 0, 0, 1);
            --success-color: #008000;
            --warning-color: #ff8c00;
            --error-color: #ff0000;
            --info-color: #0000ff;
        }
        
        /* Theme transition */
        * {
            transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease;
        }
        
        /* Reduced motion */
        @media (prefers-reduced-motion: reduce) {
            * {
                transition: none !important;
                animation: none !important;
            }
        }
    </style>
</head>
<body>
    <div class="theme-toggle-test">
        <button id="themeToggle" title="Theme Toggle">
            <i class="fas fa-moon" id="themeIcon"></i>
        </button>
    </div>

    <div class="theme-test-container">
        <div class="theme-test-section">
            <h1 class="theme-test-title">🌙 Gece Gündüz Modu Test Səhifəsi</h1>
            <p class="text-muted">Bu səhifə tüm sayfalarda theme sisteminin düzgün çalışıp çalışmadığını test edir.</p>
        </div>

        <div class="theme-test-section">
            <h2 class="theme-test-title">📊 Theme Status</h2>
            <div class="row">
                <div class="col-md-4">
                    <div class="test-card">
                        <h5>Current Theme</h5>
                        <p id="currentTheme">Light</p>
                        <span class="theme-status success" id="themeStatus">Active</span>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="test-card">
                        <h5>CSS Variables</h5>
                        <p id="cssVariables">Loaded</p>
                        <span class="theme-status success" id="cssStatus">OK</span>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="test-card">
                        <h5>JavaScript</h5>
                        <p id="jsStatus">Loaded</p>
                        <span class="theme-status success" id="jsStatusBadge">OK</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="theme-test-section">
            <h2 class="theme-test-title">🎨 UI Components Test</h2>
            <div class="row">
                <div class="col-md-6">
                    <div class="test-card">
                        <h5>Buttons</h5>
                        <button class="btn btn-primary me-2">Primary</button>
                        <button class="btn btn-secondary me-2">Secondary</button>
                        <button class="btn btn-success me-2">Success</button>
                        <button class="btn btn-warning me-2">Warning</button>
                        <button class="btn btn-danger">Danger</button>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="test-card">
                        <h5>Alerts</h5>
                        <div class="alert alert-primary">Primary Alert</div>
                        <div class="alert alert-success">Success Alert</div>
                        <div class="alert alert-warning">Warning Alert</div>
                        <div class="alert alert-danger">Danger Alert</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="theme-test-section">
            <h2 class="theme-test-title">📝 Forms Test</h2>
            <div class="row">
                <div class="col-md-6">
                    <div class="test-card">
                        <h5>Form Controls</h5>
                        <div class="mb-3">
                            <label class="form-label">Text Input</label>
                            <input type="text" class="form-control" placeholder="Enter text">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Select</label>
                            <select class="form-select">
                                <option>Option 1</option>
                                <option>Option 2</option>
                                <option>Option 3</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Textarea</label>
                            <textarea class="form-control" rows="3" placeholder="Enter message"></textarea>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="test-card">
                        <h5>Table</h5>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>John Doe</td>
                                    <td><span class="badge bg-success">Active</span></td>
                                    <td><button class="btn btn-sm btn-outline-primary">Edit</button></td>
                                </tr>
                                <tr>
                                    <td>Jane Smith</td>
                                    <td><span class="badge bg-warning">Pending</span></td>
                                    <td><button class="btn btn-sm btn-outline-primary">Edit</button></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="theme-test-section">
            <h2 class="theme-test-title">🎯 Interactive Components</h2>
            <div class="row">
                <div class="col-md-6">
                    <div class="test-card">
                        <h5>Accordion</h5>
                        <div class="accordion" id="testAccordion">
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne">
                                        Accordion Item #1
                                    </button>
                                </h2>
                                <div id="collapseOne" class="accordion-collapse collapse show" data-bs-parent="#testAccordion">
                                    <div class="accordion-body">
                                        This is the first item's accordion body.
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo">
                                        Accordion Item #2
                                    </button>
                                </h2>
                                <div id="collapseTwo" class="accordion-collapse collapse" data-bs-parent="#testAccordion">
                                    <div class="accordion-body">
                                        This is the second item's accordion body.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="test-card">
                        <h5>Progress Bars</h5>
                        <div class="progress mb-3">
                            <div class="progress-bar" role="progressbar" style="width: 25%">25%</div>
                        </div>
                        <div class="progress mb-3">
                            <div class="progress-bar bg-success" role="progressbar" style="width: 50%">50%</div>
                        </div>
                        <div class="progress mb-3">
                            <div class="progress-bar bg-warning" role="progressbar" style="width: 75%">75%</div>
                        </div>
                        <div class="progress">
                            <div class="progress-bar bg-danger" role="progressbar" style="width: 100%">100%</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="theme-test-section">
            <h2 class="theme-test-title">🔧 Theme Controls</h2>
            <div class="row">
                <div class="col-md-4">
                    <div class="test-card">
                        <h5>Theme Selector</h5>
                        <select class="form-select" id="themeSelector">
                            <option value="light">Light Theme</option>
                            <option value="dark">Dark Theme</option>
                            <option value="high-contrast">High Contrast</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="test-card">
                        <h5>System Preference</h5>
                        <p id="systemPreference">Auto</p>
                        <button class="btn btn-outline-primary" id="followSystem">Follow System</button>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="test-card">
                        <h5>Theme Info</h5>
                        <p id="themeInfo">Theme system is working correctly</p>
                        <button class="btn btn-outline-success" id="testTheme">Test Theme</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Theme JS -->
    <script src="js/theme.js"></script>
    
    <script>
        // Theme test functionality
        document.addEventListener('DOMContentLoaded', function() {
            const themeToggle = document.getElementById('themeToggle');
            const themeIcon = document.getElementById('themeIcon');
            const themeSelector = document.getElementById('themeSelector');
            const currentTheme = document.getElementById('currentTheme');
            const themeStatus = document.getElementById('themeStatus');
            const cssStatus = document.getElementById('cssStatus');
            const jsStatusBadge = document.getElementById('jsStatusBadge');
            const systemPreference = document.getElementById('systemPreference');
            const followSystemBtn = document.getElementById('followSystem');
            const testThemeBtn = document.getElementById('testTheme');
            
            // Check if theme system is working
            function checkThemeSystem() {
                const html = document.documentElement;
                const currentThemeValue = html.getAttribute('data-theme') || 'light';
                
                // Update display
                currentTheme.textContent = currentThemeValue.charAt(0).toUpperCase() + currentThemeValue.slice(1);
                themeSelector.value = currentThemeValue;
                
                // Update icon
                if (currentThemeValue === 'dark') {
                    themeIcon.className = 'fas fa-sun';
                    themeIcon.style.color = '#f39c12';
                } else {
                    themeIcon.className = 'fas fa-moon';
                    themeIcon.style.color = '#667eea';
                }
                
                // Check CSS variables
                const computedStyle = getComputedStyle(document.documentElement);
                const primaryColor = computedStyle.getPropertyValue('--primary-color');
                if (primaryColor) {
                    cssStatus.textContent = 'OK';
                    cssStatus.className = 'theme-status success';
                } else {
                    cssStatus.textContent = 'Error';
                    cssStatus.className = 'theme-status error';
                }
                
                // Check JavaScript
                if (window.themeManager) {
                    jsStatusBadge.textContent = 'OK';
                    jsStatusBadge.className = 'theme-status success';
                } else {
                    jsStatusBadge.textContent = 'Error';
                    jsStatusBadge.className = 'theme-status error';
                }
            }
            
            // Theme toggle functionality
            themeToggle.addEventListener('click', function() {
                const html = document.documentElement;
                const currentThemeValue = html.getAttribute('data-theme') || 'light';
                const newTheme = currentThemeValue === 'dark' ? 'light' : 'dark';
                
                html.setAttribute('data-theme', newTheme);
                localStorage.setItem('theme', newTheme);
                
                checkThemeSystem();
            });
            
            // Theme selector functionality
            themeSelector.addEventListener('change', function() {
                const selectedTheme = this.value;
                document.documentElement.setAttribute('data-theme', selectedTheme);
                localStorage.setItem('theme', selectedTheme);
                
                checkThemeSystem();
            });
            
            // Follow system preference
            followSystemBtn.addEventListener('click', function() {
                const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                const systemTheme = prefersDark ? 'dark' : 'light';
                
                document.documentElement.setAttribute('data-theme', systemTheme);
                localStorage.setItem('theme', systemTheme);
                
                checkThemeSystem();
            });
            
            // Test theme functionality
            testThemeBtn.addEventListener('click', function() {
                const themes = ['light', 'dark', 'high-contrast'];
                let currentIndex = 0;
                
                const interval = setInterval(() => {
                    const theme = themes[currentIndex];
                    document.documentElement.setAttribute('data-theme', theme);
                    checkThemeSystem();
                    
                    currentIndex++;
                    if (currentIndex >= themes.length) {
                        clearInterval(interval);
                        // Return to saved theme
                        const savedTheme = localStorage.getItem('theme') || 'light';
                        document.documentElement.setAttribute('data-theme', savedTheme);
                        checkThemeSystem();
                    }
                }, 1000);
            });
            
            // Check system preference
            function checkSystemPreference() {
                const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                systemPreference.textContent = prefersDark ? 'Dark' : 'Light';
            }
            
            // Listen for system theme changes
            window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', function(e) {
                checkSystemPreference();
            });
            
            // Initialize
            checkThemeSystem();
            checkSystemPreference();
            
            // Load saved theme
            const savedTheme = localStorage.getItem('theme');
            if (savedTheme) {
                document.documentElement.setAttribute('data-theme', savedTheme);
                checkThemeSystem();
            }
        });
    </script>
</body>
</html>
