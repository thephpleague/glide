<?php

/**
 * Generate a documentation image using the Glide API.
 *
 * Usage:
 *   php scripts/generate-image.php "kayaks.jpg?w=500&blur=5"
 *   php scripts/generate-image.php "kayaks.jpg?h=500&flip=v"
 *
 * Source images are read from docs/source-images/.
 * Generated images are written to docs/images/.
 *
 * The output filename is built from sorted params:
 *   kayaks.jpg?w=500&blur=5  → docs/images/kayaks-blur-5_w-500.jpg
 *   logo.png?w=400&bg=black  → docs/images/logo-bg-black_w-400.png
 *   kayaks.jpg?w=500&fm=gif  → docs/images/kayaks-fm-gif_w-500.gif
 *
 * Legacy v0.3 params (rect, fit=crop&crop=<pos>) are translated automatically.
 */

declare(strict_types=1);

use League\Glide\ServerFactory;

require __DIR__.'/../vendor/autoload.php';

/**
 * Translate legacy v0.3 Glide params to current equivalents.
 *
 * - rect → crop
 * - fit=crop & crop=<position> → fit=crop-<position>
 */
function translateLegacyParams(array $params): array
{
    // rect → crop
    if (isset($params['rect'])) {
        $params['crop'] = $params['rect'];
        unset($params['rect']);
    }

    // fit=crop & crop=left → fit=crop-left
    if (isset($params['fit'], $params['crop'])
        && 'crop' === $params['fit']
        && !preg_match('/^\d/', $params['crop'])
    ) {
        $params['fit'] = 'crop-'.$params['crop'];
        unset($params['crop']);
    }

    return $params;
}

/**
 * Build a descriptive filename from the source image name and sorted params.
 *
 * Examples:
 *   kayaks.jpg + [w=>500, blur=>5]  → kayaks-blur-5_w-500.jpg
 *   kayaks.jpg + [fm=>gif, w=>500]  → kayaks-fm-gif_w-500.gif
 *   kayaks.jpg + []                 → kayaks.jpg
 */
function buildOutputFilename(string $sourceImage, array $params): string
{
    $info = pathinfo($sourceImage);
    $base = $info['filename'];
    $ext = $info['extension'] ?? 'jpg';

    // fm param changes the output extension
    if (isset($params['fm'])) {
        $ext = $params['fm'];
    }

    if (empty($params)) {
        return $base.'.'.$ext;
    }

    ksort($params);

    $parts = [];
    foreach ($params as $key => $value) {
        $safeValue = (string) $value;
        // Dots in values → 'p' (e.g. gam=.9 → gam-p9)
        $safeValue = str_replace('.', 'p', $safeValue);
        // Commas → '_' (e.g. border=10,5000,overlay → border-10_5000_overlay)
        $safeValue = str_replace(',', '_', $safeValue);
        // Negative values → 'neg' (e.g. bri=-25 → bri-neg25)
        $safeValue = str_replace('-', 'neg', $safeValue);

        $parts[] = $key.'-'.$safeValue;
    }

    return $base.'-'.implode('_', $parts).'.'.$ext;
}

/**
 * Generate a single Glide image from a filename with query params.
 *
 * @param string $input     e.g. "kayaks.jpg?w=500&blur=5"
 * @param string $sourceDir Directory containing source images
 * @param string $outputDir Directory to write generated images
 *
 * @return string The output filename (relative to $outputDir)
 */
function generateImage(string $input, string $sourceDir, string $outputDir): string
{
    // Split filename and query string
    $parts = explode('?', $input, 2);
    $sourceImage = $parts[0];
    $queryString = $parts[1] ?? '';

    // Parse query params
    parse_str($queryString, $params);

    // Translate legacy v0.3 params
    $params = translateLegacyParams($params);

    // Resolve source file
    $sourcePath = $sourceDir.'/'.$sourceImage;
    if (!file_exists($sourcePath)) {
        throw new RuntimeException("Source image not found: {$sourcePath}");
    }

    // Build output filename
    $outputFilename = buildOutputFilename($sourceImage, $params);
    $outputPath = $outputDir.'/'.$outputFilename;

    // Skip if already generated
    if (file_exists($outputPath)) {
        return $outputFilename;
    }

    // Create Glide API with watermarks pointing to source-images dir
    $factory = new ServerFactory([
        'watermarks' => $sourceDir,
    ]);
    $api = $factory->getApi();

    // Read source and process
    $source = file_get_contents($sourcePath);
    $result = $api->run($source, $params);

    // Write output
    if (!is_dir($outputDir)) {
        mkdir($outputDir, 0755, true);
    }
    file_put_contents($outputPath, $result);

    return $outputFilename;
}

// CLI entry point
if (!isset($argv[1])) {
    echo "Usage: php generate-image.php \"kayaks.jpg?h=500&flip=v\"\n";
    exit(1);
}

$sourceDir = __DIR__.'/../docs/source-images';
$outputDir = __DIR__.'/../docs/images';

try {
    $filename = generateImage($argv[1], $sourceDir, $outputDir);
    echo "docs/images/{$filename}\n";
} catch (RuntimeException $e) {
    echo "Error: {$e->getMessage()}\n";
    exit(1);
}
