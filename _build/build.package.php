<?php
/**
 * Build BlockAB Transport Package (Simple Method)
 * Creates a ZIP file that can be installed via MODX Package Manager
 *
 * @package blockab
 */

$mtime = microtime();
$mtime = explode(" ", $mtime);
$mtime = $mtime[1] + $mtime[0];
$tstart = $mtime;
set_time_limit(0);

/* Define package */
define('PKG_NAME', 'BlockAB');
define('PKG_NAME_LOWER', 'blockab');
define('PKG_VERSION', '1.0.0');
define('PKG_RELEASE', 'beta1');

/* Define paths */
$root = dirname(dirname(__FILE__)) . '/';
$sources = array(
    'root' => $root,
    'build' => $root . '_build/',
    'core' => $root . 'core/components/' . PKG_NAME_LOWER . '/',
    'assets' => $root . 'assets/components/' . PKG_NAME_LOWER . '/',
    'docs' => $root,
);

$outputDir = 'C:/Projecten/moving-in.nl/core/packages/';
$packageName = PKG_NAME_LOWER . '-' . PKG_VERSION . '-' . PKG_RELEASE . '.transport.zip';
$packagePath = $outputDir . $packageName;

echo "<pre>\n";
echo "Building " . PKG_NAME . " " . PKG_VERSION . "-" . PKG_RELEASE . "\n";
echo "=================================================\n\n";

// Remove old package if exists
if (file_exists($packagePath)) {
    unlink($packagePath);
    echo "Removed old package\n";
}

// Create temporary directory
$tempDir = sys_get_temp_dir() . '/blockab_build_' . time() . '/';
mkdir($tempDir, 0755, true);
echo "Created temp directory: $tempDir\n\n";

// Function to recursively copy directory
function recurseCopy($src, $dst) {
    $dir = opendir($src);
    @mkdir($dst, 0755, true);

    $count = 0;
    while (($file = readdir($dir)) !== false) {
        if ($file != '.' && $file != '..' && $file != '.git' && $file != '.gitignore') {
            if (is_dir($src . '/' . $file)) {
                $count += recurseCopy($src . '/' . $file, $dst . '/' . $file);
            } else {
                copy($src . '/' . $file, $dst . '/' . $file);
                $count++;
            }
        }
    }
    closedir($dir);
    return $count;
}

// Copy core files
echo "Copying core files...\n";
$coreCount = recurseCopy($sources['core'], $tempDir . 'core/components/' . PKG_NAME_LOWER . '/');
echo "  Copied $coreCount core files\n\n";

// Copy assets files (if they exist)
if (is_dir($sources['assets'])) {
    echo "Copying assets files...\n";
    $assetsCount = recurseCopy($sources['assets'], $tempDir . 'assets/components/' . PKG_NAME_LOWER . '/');
    echo "  Copied $assetsCount assets files\n\n";
}

// Copy documentation
echo "Copying documentation...\n";
$docs = array('README.md', 'INSTALL.md', 'GEBRUIKERSHANDLEIDING.md', 'MIGX_CONFIGURATION.md', 'LICENSE');
$docCount = 0;
foreach ($docs as $doc) {
    if (file_exists($sources['docs'] . $doc)) {
        copy($sources['docs'] . $doc, $tempDir . $doc);
        $docCount++;
    }
}
echo "  Copied $docCount documentation files\n\n";

// Create install scripts directory
$installDir = $tempDir . '_install/';
mkdir($installDir, 0755, true);

// Copy SQL install scripts
echo "Copying install scripts...\n";
$sqlFiles = glob($sources['build'] . '*.sql');
foreach ($sqlFiles as $sqlFile) {
    copy($sqlFile, $installDir . basename($sqlFile));
}
echo "  Copied " . count($sqlFiles) . " SQL files\n\n";

// Create package info file
$packageInfo = array(
    'name' => PKG_NAME,
    'version' => PKG_VERSION,
    'release' => PKG_RELEASE,
    'description' => 'A/B Testing for MIGX Blocks - Test individual content blocks instead of entire pages',
    'author' => 'Moving-in.nl',
    'license' => 'GPLv2',
    'readme' => 'README.md',
    'instructions' => 'INSTALL.md',
    'changelog' => 'See README.md for changelog',
);

file_put_contents($tempDir . 'package.json', json_encode($packageInfo, JSON_PRETTY_PRINT));
echo "Created package.json\n\n";

// Create installation instructions
$installInstructions = <<<'EOT'
# BlockAB Installation Instructions

## Automatic Installation via Package Manager

1. Upload this package to MODX via **Installer > Download Extras**
2. Click **Install** on the BlockAB package
3. Follow the installation wizard

## Manual Installation

If automatic installation fails, follow these steps:

### 1. Copy Files

Extract the ZIP and copy:
- `core/components/blockab/` → `{your_modx}/core/components/blockab/`
- `assets/components/blockab/` → `{your_modx}/assets/components/blockab/`

### 2. Run SQL Scripts

Execute these SQL scripts in order (via phpMyAdmin or command line):

1. `_install/install.sql` - Creates database tables
2. `_install/install.menu.sql` - Creates menu entry and settings
3. `_install/install.snippets.sql` - Installs snippets

**Important:** Replace `mdx_` with your MODX table prefix if different!

### 3. Clear Cache

In MODX Manager:
- Go to **Manage > Clear Cache**
- Clear all caches

### 4. Verify Installation

1. Check if **Components > BlockAB** menu appears
2. Try creating a test
3. Check if snippets exist: BlockAB, BlockABConversion, BlockABGetTestGroups, BlockABGetVariants

## Template Integration

See `MIGX_CONFIGURATION.md` for instructions on:
- Adding A/B test fields to MIGX
- Integrating BlockAB in your templates
- Setting up conversion tracking

## User Guide

See `GEBRUIKERSHANDLEIDING.md` (Dutch) for:
- Complete user manual
- Step-by-step A/B test setup
- Best practices
- Troubleshooting

## Support

For issues or questions:
- Check the error log: **Reports > Error Log** in MODX
- Review documentation files
- Contact your developer

---

**Package:** BlockAB
**Version:** 1.0.0-beta1
**Author:** Moving-in.nl
**License:** GPLv2
EOT;

file_put_contents($installDir . 'README.txt', $installInstructions);
echo "Created installation instructions\n\n";

// Create ZIP archive
echo "Creating ZIP archive...\n";

$zip = new ZipArchive();
if ($zip->open($packagePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
    die("Cannot create ZIP file\n");
}

// Add all files from temp directory
$files = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($tempDir),
    RecursiveIteratorIterator::LEAVES_ONLY
);

foreach ($files as $file) {
    if (!$file->isDir()) {
        $filePath = $file->getRealPath();
        $relativePath = substr($filePath, strlen($tempDir));
        $zip->addFile($filePath, $relativePath);
    }
}

$fileCount = $zip->numFiles;
$zip->close();

echo "  Added $fileCount files to ZIP\n\n";

// Clean up temp directory
function deleteDirectory($dir) {
    if (!file_exists($dir)) return true;
    if (!is_dir($dir)) return unlink($dir);
    foreach (scandir($dir) as $item) {
        if ($item == '.' || $item == '..') continue;
        if (!deleteDirectory($dir . DIRECTORY_SEPARATOR . $item)) return false;
    }
    return rmdir($dir);
}

deleteDirectory($tempDir);
echo "Cleaned up temp directory\n\n";

// Calculate file size
$fileSize = filesize($packagePath);
$fileSizeMB = round($fileSize / 1024 / 1024, 2);

echo "=================================================\n";
echo "Package created successfully!\n\n";
echo "Location: $packagePath\n";
echo "Size: $fileSizeMB MB\n\n";

$tend = microtime();
$tend = explode(" ", $tend);
$tend = $tend[1] + $tend[0];
$totalTime = sprintf("%2.4f s", ($tend - $tstart));

echo "Build time: $totalTime\n";
echo "\n=================================================\n";
echo "\nYou can now:\n";
echo "1. Upload this package via MODX Package Manager\n";
echo "2. Or manually extract and follow _install/README.txt\n\n";

echo "</pre>";
