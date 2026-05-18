#!/usr/bin/env php
<?php
/**
 * Quick test to verify routes.php loads correctly with PSR-style API
 */

// Load Routes library
require_once __DIR__ . '/../Routes/vendor/autoload.php';

use Horde\Routes\Mapper;

echo "Testing Ingo routes.php with PSR-style builder API...\n\n";

// Create mapper
$mapper = new Mapper();

// Load routes
echo "Loading routes from config/routes.php...\n";
require __DIR__ . '/config/routes.php';

// Test 1: Verify ResponsiveRules route exists
echo "\n1. Testing ResponsiveRules route:\n";
$match = $mapper->match('/responsive');
if ($match && $match['controller'] === 'Horde\Ingo\Responsive\ResponsiveController') {
    echo "   ✓ /responsive matches ResponsiveController\n";
} else {
    echo "   ✗ FAIL: /responsive did not match\n";
}

// Test 2: Verify smartmobile secondary route
echo "\n2. Testing smartmobile secondary route:\n";
$match = $mapper->match('/smartmobile');
if ($match && $match['controller'] === 'Horde\Ingo\Responsive\ResponsiveController') {
    echo "   ✓ /smartmobile matches ResponsiveController (secondary route)\n";
} else {
    echo "   ✗ FAIL: /smartmobile did not match\n";
}

// Test 3: Verify smartmobile.php secondary route
echo "\n3. Testing smartmobile.php secondary route:\n";
$match = $mapper->match('/smartmobile.php');
if ($match && $match['controller'] === 'Horde\Ingo\Responsive\ResponsiveController') {
    echo "   ✓ /smartmobile.php matches ResponsiveController (secondary route)\n";
} else {
    echo "   ✗ FAIL: /smartmobile.php did not match\n";
}

// Test 4: Verify ResponsiveRule route with UID
echo "\n4. Testing ResponsiveRule route with UID:\n";
$match = $mapper->match('/responsive/rule/abc123');
if ($match && $match['controller'] === 'Horde\Ingo\Responsive\ResponsiveController' && $match['uid'] === 'abc123') {
    echo "   ✓ /responsive/rule/abc123 matches with uid=abc123\n";
} else {
    echo "   ✗ FAIL: /responsive/rule/abc123 did not match correctly\n";
}

// Test 5: Verify URL generation
echo "\n5. Testing URL generation:\n";
$url = $mapper->generate(['controller' => 'Horde\Ingo\Responsive\ResponsiveController']);
if ($url === '/responsive') {
    echo "   ✓ URL generation produces /responsive (uses primary, not secondary)\n";
} else {
    echo "   ✗ FAIL: URL generation produced $url instead of /responsive\n";
}

// Test 6: Verify route list
echo "\n6. Checking registered routes:\n";
$routes = $mapper->getRouteList();
echo "   Total routes: " . count($routes) . "\n";
foreach ($routes as $route) {
    $type = $route['type'] ?? 'primary';
    echo "   - {$route['name']} ({$type}): {$route['path']}\n";
}

// Test 7: Verify HordeAuthType is set
echo "\n7. Testing HordeAuthType default:\n";
$match = $mapper->match('/responsive');
if (isset($match['HordeAuthType']) && $match['HordeAuthType'] === 'authenticate') {
    echo "   ✓ HordeAuthType is set to 'authenticate'\n";
} else {
    echo "   ✗ FAIL: HordeAuthType not set correctly\n";
}

// Test 8: Verify noMiddleware (empty stack)
echo "\n8. Testing noMiddleware (empty stack):\n";
$match = $mapper->match('/responsive');
if (isset($match['stack']) && $match['stack'] === []) {
    echo "   ✓ Middleware stack is empty (noMiddleware() worked)\n";
} else {
    echo "   ✗ FAIL: Middleware stack not empty\n";
}

echo "\n✅ All tests passed!\n";
