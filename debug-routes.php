#!/usr/bin/env php
<?php
/**
 * Route Debugging Tool - Verify routes are actually being used
 *
 * This tool helps diagnose routing issues by:
 * 1. Checking for physical files that shadow routes
 * 2. Testing route matching
 * 3. Checking web server configuration
 */

$appDir = dirname(__DIR__);
$webDir = $appDir . '/web';
$configDir = $appDir . '/config';
$routesDir = $appDir . '/Routes';

echo "=== Route Debugging Tool ===\n\n";

// Load Routes library
if (file_exists($routesDir . '/vendor/autoload.php')) {
    require_once $routesDir . '/vendor/autoload.php';
} else {
    echo "⚠️  Routes library not found at $routesDir\n";
    echo "Using Horde autoloader instead...\n";
    require_once $appDir . '/ingo/vendor/autoload.php';
}
use Horde\Routes\Mapper;

// Step 1: Check for physical file conflicts
echo "1. Checking for physical files that shadow routes...\n";

$routePaths = [
    '/responsive',
    '/smartmobile',
    '/smartmobile.php',
    '/responsive/rule/test123'
];

$conflicts = [];
foreach ($routePaths as $path) {
    $cleanPath = ltrim($path, '/');
    $physicalFile = $webDir . '/' . $cleanPath;

    if (file_exists($physicalFile)) {
        $conflicts[] = $path;
        echo "   ⚠️  CONFLICT: Physical file exists at $physicalFile\n";
        echo "       This will prevent routing! Apache serves files before routing.\n";
    } else {
        echo "   ✓ No physical file for $path (routing can work)\n";
    }
}

if (!empty($conflicts)) {
    echo "\n   🚨 CRITICAL: Physical files block these routes!\n";
    echo "   Action required: Remove or rename these files:\n";
    foreach ($conflicts as $path) {
        $cleanPath = ltrim($path, '/');
        echo "   - rm $webDir/$cleanPath\n";
    }
    echo "\n";
}

// Step 2: Load and test routes
echo "\n2. Loading routes from config/routes.php...\n";
$mapper = new Mapper();
require $configDir . '/routes.php';

$routes = $mapper->getRouteList();
echo "   Total routes registered: " . count($routes) . "\n";
foreach ($routes as $route) {
    $type = $route['type'] ?? 'primary';
    $name = $route['name'] ?: '(unnamed)';
    echo "   - $name [$type]: {$route['path']}\n";
}

// Step 3: Test route matching
echo "\n3. Testing route matching...\n";
foreach ($routePaths as $path) {
    $match = $mapper->match($path);
    if ($match) {
        echo "   ✓ $path → {$match['controller']}\n";
    } else {
        echo "   ✗ $path → NO MATCH\n";
    }
}

// Step 4: Check web server configuration
echo "\n4. Checking web server configuration...\n";

$htaccessFile = $webDir . '/.htaccess';
if (file_exists($htaccessFile)) {
    echo "   ✓ .htaccess exists\n";

    // Check for FallbackResource or RewriteRule
    $htaccess = file_get_contents($htaccessFile);
    if (strpos($htaccess, 'FallbackResource') !== false) {
        echo "   ✓ FallbackResource found (modern routing)\n";
    } elseif (strpos($htaccess, 'RewriteRule') !== false) {
        echo "   ✓ RewriteRule found (legacy routing)\n";
    } else {
        echo "   ⚠️  No routing directives found in .htaccess\n";
    }

    // Check if it skips existing files
    if (strpos($htaccess, 'RewriteCond %{REQUEST_FILENAME} !-f') !== false ||
        strpos($htaccess, '-f') !== false) {
        echo "   ✓ Skips existing files (correct behavior)\n";
        echo "      BUT physical files will NEVER reach the router!\n";
    }
} else {
    echo "   ⚠️  No .htaccess file found\n";
}

// Step 5: Check for common shim files
echo "\n5. Checking for legacy shim files...\n";

$legacyShims = [
    'smartmobile.php',
    'mobile.php',
    'index.php',
    'api.php'
];

foreach ($legacyShims as $shim) {
    $shimPath = $webDir . '/' . $shim;
    if (file_exists($shimPath)) {
        echo "   ⚠️  Found legacy shim: $shim\n";

        // Check what it does
        $content = file_get_contents($shimPath);
        if (strpos($content, 'vendor') !== false) {
            echo "       → References vendor/ (may be broken)\n";
        }
        if (strpos($content, 'require') !== false || strpos($content, 'include') !== false) {
            echo "       → Includes other files (check dependencies)\n";
        }
    }
}

// Step 6: Recommendations
echo "\n=== Recommendations ===\n\n";

if (!empty($conflicts)) {
    echo "🔴 CRITICAL: Remove physical files that shadow routes\n";
    echo "   These files prevent routing from working:\n";
    foreach ($conflicts as $path) {
        $cleanPath = ltrim($path, '/');
        echo "   rm $webDir/$cleanPath\n";
    }
    echo "\n";
}

echo "✅ To test if routing works:\n";
echo "   1. Remove any physical files for route paths\n";
echo "   2. Clear browser cache (Ctrl+Shift+R)\n";
echo "   3. Test URL: curl -I http://localhost/ingo/smartmobile.php\n";
echo "   4. Check journal: journalctl --since '1 minute ago' | grep ResponsiveController\n";
echo "\n";

echo "📊 To add route logging:\n";
echo "   Add to routes.php:\n";
echo "   error_log('ROUTE: Loaded routes.php');\n";
echo "   \n";
echo "   Add to ResponsiveController->handle():\n";
echo "   error_log('ROUTE: ResponsiveController called for ' . \$request->getUri()->getPath());\n";
echo "\n";

echo "🔍 To verify route was used:\n";
echo "   journalctl -f | grep 'ROUTE:'\n";
echo "   (then access the URL)\n";
echo "\n";
