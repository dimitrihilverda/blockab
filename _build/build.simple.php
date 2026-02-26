<?php
/**
 * BlockAB Simple Install Script
 *
 * Copies all package files to a MODX installation.
 *
 * USAGE (from _build directory):
 *   php build.simple.php /path/to/modx
 *
 * The MODX base path should be the directory that contains both
 * the 'core' and 'public_html' (or 'assets') directories.
 *
 * EXAMPLES:
 *   php build.simple.php /var/www/html
 *   php build.simple.php /home/user/domains/example.com
 *
 * If your assets live directly under the base (not inside public_html), the
 * script will detect this automatically.
 *
 * @package blockab
 */

if (PHP_SAPI !== 'cli') {
    die('Run this script from the command line.');
}

// ── Resolve paths ────────────────────────────────────────────────────────────

$packageRoot = dirname(__DIR__) . '/';

if (!empty($argv[1])) {
    $modxBase = rtrim($argv[1], '/\\') . '/';
} else {
    echo "BlockAB Install Script\n";
    echo "======================\n\n";
    echo "Usage: php build.simple.php /path/to/modx\n\n";
    echo "The path should point to the MODX root that contains the 'core' folder.\n";
    exit(1);
}

if (!is_dir($modxBase . 'core/')) {
    echo "Error: '{$modxBase}core/' not found.\n";
    echo "Make sure you provide the correct MODX root path.\n";
    exit(1);
}

// Detect whether assets live in public_html or directly in the root
if (is_dir($modxBase . 'public_html/assets/')) {
    $assetsBase = $modxBase . 'public_html/';
} else {
    $assetsBase = $modxBase;
}

$targets = [
    'core'   => $modxBase  . 'core/components/blockab/',
    'assets' => $assetsBase . 'assets/components/blockab/',
];

$sources = [
    'core'   => $packageRoot . 'core/components/blockab/',
    'assets' => $packageRoot . 'assets/components/blockab/',
];

// ── Copy helper ──────────────────────────────────────────────────────────────

function recurseCopy($src, $dst) {
    if (!is_dir($src)) {
        echo "  Warning: source directory '$src' not found, skipping.\n";
        return 0;
    }
    @mkdir($dst, 0755, true);
    $count = 0;
    foreach (scandir($src) as $item) {
        if ($item === '.' || $item === '..') continue;
        if (is_dir($src . $item)) {
            $count += recurseCopy($src . $item . '/', $dst . $item . '/');
        } else {
            copy($src . $item, $dst . $item);
            $count++;
        }
    }
    return $count;
}

// ── Run ──────────────────────────────────────────────────────────────────────

echo "BlockAB Install Script\n";
echo "======================\n\n";
echo "MODX root : {$modxBase}\n";
echo "Core path : {$targets['core']}\n";
echo "Assets    : {$targets['assets']}\n\n";

echo "Copying core files...\n";
$n = recurseCopy($sources['core'], $targets['core']);
echo "  Done — $n files copied.\n\n";

echo "Copying asset files...\n";
$n = recurseCopy($sources['assets'], $targets['assets']);
echo "  Done — $n files copied.\n\n";

echo "Next steps:\n";
echo "  1. Run _build/install.sql in your database (replace the prefix if needed).\n";
echo "  2. Run _build/install.menu.sql in your database.\n";
echo "  3. Create snippets: BlockAB and BlockABConversion (see INSTALL.md step 3).\n";
echo "  4. Clear MODX cache.\n\n";
echo "Done!\n";
