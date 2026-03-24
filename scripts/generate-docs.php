<?php
/**
 * PHPDoc Generator for Claude Health
 *
 * Scans all PHP files in app/ and core/, extracts class metadata,
 * and generates a professional HTML documentation page.
 *
 * Usage: php scripts/generate-docs.php
 *
 * @author J.J. Johnson <visionquest716@gmail.com>
 */

declare(strict_types=1);

$projectRoot = dirname(__DIR__);
$outputDir = $projectRoot . '/docs/phpdoc';
$outputFile = $outputDir . '/index.html';

// Ensure output directory exists
if (!is_dir($outputDir)) {
    mkdir($outputDir, 0755, true);
}

// Directories to scan
$scanDirs = [
    $projectRoot . '/core',
    $projectRoot . '/app/Controllers',
    $projectRoot . '/app/Models',
    $projectRoot . '/app/Middleware',
    $projectRoot . '/app/Helpers',
];

/**
 * Represents a parsed PHP class with its methods and metadata.
 */
class ParsedClass
{
    public string $name = '';
    public string $namespace = '';
    public string $fqcn = '';
    public string $filePath = '';
    public string $type = 'class'; // class, abstract class, interface
    public string $extends = '';
    public array $implements = [];
    public string $docblock = '';
    public array $properties = [];
    public array $methods = [];
    public array $constants = [];
}

/**
 * Represents a parsed method.
 */
class ParsedMethod
{
    public string $name = '';
    public string $visibility = 'public';
    public bool $isStatic = false;
    public bool $isAbstract = false;
    public string $returnType = '';
    public array $parameters = [];
    public string $docblock = '';
    public string $signature = '';
}

/**
 * Represents a parsed property.
 */
class ParsedProperty
{
    public string $name = '';
    public string $visibility = 'public';
    public bool $isStatic = false;
    public string $type = '';
    public string $defaultValue = '';
    public string $docblock = '';
}

// ──────────────────────────────────────────────
// Scanner
// ──────────────────────────────────────────────

$allClasses = [];
$allFunctions = [];

foreach ($scanDirs as $dir) {
    if (!is_dir($dir)) {
        continue;
    }
    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS)
    );

    foreach ($files as $file) {
        if ($file->getExtension() !== 'php') {
            continue;
        }

        $filePath = $file->getRealPath();
        $content = file_get_contents($filePath);

        // Extract namespace
        $namespace = '';
        if (preg_match('/^namespace\s+([^;]+);/m', $content, $nsMatch)) {
            $namespace = trim($nsMatch[1]);
        }

        // Check for standalone functions (helpers)
        if (strpos($filePath, 'Helpers') !== false || strpos($filePath, 'helpers') !== false) {
            preg_match_all(
                '/\/\*\*\s*\n(.*?)\*\/\s*\nfunction\s+(\w+)\s*\(([^)]*)\)\s*(?::\s*(\S+))?\s*\{/s',
                $content,
                $funcMatches,
                PREG_SET_ORDER
            );
            foreach ($funcMatches as $fm) {
                $allFunctions[] = [
                    'name' => $fm[2],
                    'params' => trim($fm[3]),
                    'returnType' => $fm[4] ?? '',
                    'docblock' => trim($fm[1]),
                    'file' => str_replace($projectRoot . '/', '', $filePath),
                ];
            }
            continue;
        }

        // Extract class definition
        if (!preg_match('/(abstract\s+)?class\s+(\w+)(?:\s+extends\s+(\w+))?(?:\s+implements\s+([^{]+))?\s*\{/s', $content, $classMatch)) {
            continue;
        }

        $parsed = new ParsedClass();
        $parsed->namespace = $namespace;
        $parsed->name = $classMatch[2];
        $parsed->fqcn = $namespace ? $namespace . '\\' . $classMatch[2] : $classMatch[2];
        $parsed->filePath = str_replace([$projectRoot . '/', $projectRoot . '\\'], '', $filePath);
        $parsed->type = !empty($classMatch[1]) ? 'abstract class' : 'class';
        $parsed->extends = $classMatch[3] ?? '';
        $parsed->implements = isset($classMatch[4]) ? array_map('trim', explode(',', $classMatch[4])) : [];

        // Extract class-level docblock
        if (preg_match('/\/\*\*\s*\n(.*?)\*\/\s*\n(?:abstract\s+)?class\s+' . preg_quote($parsed->name) . '/s', $content, $classDocMatch)) {
            $parsed->docblock = cleanDocblock($classDocMatch[1]);
        }

        // Extract properties
        preg_match_all(
            '/(?:\/\*\*\s*\n(.*?)\*\/\s*\n\s*)?(public|protected|private)\s+(static\s+)?(?:(\??\w+(?:\|[\w|]+)?)\s+)?\$(\w+)(?:\s*=\s*([^;]+))?;/s',
            $content,
            $propMatches,
            PREG_SET_ORDER
        );
        foreach ($propMatches as $pm) {
            $prop = new ParsedProperty();
            $prop->docblock = isset($pm[1]) ? cleanDocblock($pm[1]) : '';
            $prop->visibility = $pm[2];
            $prop->isStatic = !empty(trim($pm[3] ?? ''));
            $prop->type = $pm[4] ?? '';
            $prop->name = $pm[5];
            $prop->defaultValue = isset($pm[6]) ? trim($pm[6]) : '';
            $parsed->properties[] = $prop;
        }

        // Extract methods
        preg_match_all(
            '/(?:\/\*\*\s*\n(.*?)\*\/\s*\n\s*)?(public|protected|private)\s+(static\s+)?(abstract\s+)?function\s+(\w+)\s*\(([^)]*)\)\s*(?::\s*(\S+))?\s*(?:\{|;)/s',
            $content,
            $methodMatches,
            PREG_SET_ORDER
        );

        foreach ($methodMatches as $mm) {
            $method = new ParsedMethod();
            $method->docblock = isset($mm[1]) ? cleanDocblock($mm[1]) : '';
            $method->visibility = $mm[2];
            $method->isStatic = !empty(trim($mm[3] ?? ''));
            $method->isAbstract = !empty(trim($mm[4] ?? ''));
            $method->name = $mm[5];
            $method->returnType = $mm[7] ?? '';

            // Parse parameters
            $paramStr = trim($mm[6]);
            if ($paramStr !== '') {
                $method->parameters = parseParameters($paramStr);
            }

            // Build signature
            $sig = $method->visibility;
            if ($method->isStatic) $sig .= ' static';
            if ($method->isAbstract) $sig .= ' abstract';
            $sig .= ' function ' . $method->name . '(' . $paramStr . ')';
            if ($method->returnType) {
                $sig .= ': ' . $method->returnType;
            }
            $method->signature = $sig;

            $parsed->methods[] = $method;
        }

        $allClasses[] = $parsed;
    }
}

/**
 * Clean a docblock by removing leading asterisks and whitespace.
 */
function cleanDocblock(string $raw): string
{
    $lines = explode("\n", $raw);
    $cleaned = [];
    foreach ($lines as $line) {
        $line = preg_replace('/^\s*\*\s?/', '', $line);
        $cleaned[] = $line;
    }
    return trim(implode("\n", $cleaned));
}

/**
 * Parse a parameter string into structured data.
 */
function parseParameters(string $paramStr): array
{
    $params = [];
    // Split on commas not inside angle brackets or parentheses
    $parts = preg_split('/,\s*(?![^<]*>|[^(]*\))/', $paramStr);

    foreach ($parts as $part) {
        $part = trim($part);
        if ($part === '') continue;

        $param = ['type' => '', 'name' => '', 'default' => ''];

        // Match type, name, and optional default
        if (preg_match('/^(\??\w+(?:\|[\w|]+)?)\s+(\$\w+)(?:\s*=\s*(.+))?$/', $part, $pm)) {
            $param['type'] = $pm[1];
            $param['name'] = $pm[2];
            $param['default'] = $pm[3] ?? '';
        } elseif (preg_match('/^(\$\w+)(?:\s*=\s*(.+))?$/', $part, $pm)) {
            $param['name'] = $pm[1];
            $param['default'] = $pm[2] ?? '';
        } else {
            $param['name'] = $part;
        }

        $params[] = $param;
    }

    return $params;
}

// ──────────────────────────────────────────────
// Group classes by namespace
// ──────────────────────────────────────────────

$groups = [];
foreach ($allClasses as $cls) {
    $ns = $cls->namespace ?: 'Global';
    $groups[$ns][] = $cls;
}

// Sort namespaces: Core first, then alphabetical
uksort($groups, function ($a, $b) {
    if ($a === 'Core') return -1;
    if ($b === 'Core') return 1;
    return strcmp($a, $b);
});

// Sort classes within each group
foreach ($groups as &$classList) {
    usort($classList, fn($a, $b) => strcmp($a->name, $b->name));
}
unset($classList);

// ──────────────────────────────────────────────
// Generate HTML
// ──────────────────────────────────────────────

$generatedDate = date('F j, Y \a\t g:i A');
$totalClasses = count($allClasses);
$totalMethods = array_sum(array_map(fn($c) => count($c->methods), $allClasses));
$totalFunctions = count($allFunctions);

ob_start();
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Claude Health API Documentation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root {
            --ch-primary: #0d6efd;
            --ch-sidebar-bg: #f8f9fa;
            --ch-sidebar-width: 300px;
            --ch-code-bg: #f1f3f5;
        }
        [data-bs-theme="dark"] {
            --ch-sidebar-bg: #1a1d21;
            --ch-code-bg: #2b2d31;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        }
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            width: var(--ch-sidebar-width);
            background: var(--ch-sidebar-bg);
            border-right: 1px solid var(--bs-border-color);
            overflow-y: auto;
            z-index: 1000;
            padding: 1.5rem 0;
        }
        .sidebar .nav-link {
            padding: 0.25rem 1.5rem;
            font-size: 0.875rem;
            color: var(--bs-body-color);
            border-left: 3px solid transparent;
        }
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background: rgba(13, 110, 253, 0.08);
            border-left-color: var(--ch-primary);
            color: var(--ch-primary);
        }
        .sidebar .ns-header {
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--bs-secondary-color);
            padding: 0.75rem 1.5rem 0.25rem;
            margin-top: 0.5rem;
        }
        .main-content {
            margin-left: var(--ch-sidebar-width);
            padding: 2rem 3rem;
            max-width: 960px;
        }
        .class-card {
            border: 1px solid var(--bs-border-color);
            border-radius: 0.5rem;
            margin-bottom: 2rem;
            overflow: hidden;
        }
        .class-card-header {
            background: var(--ch-code-bg);
            padding: 1rem 1.25rem;
            border-bottom: 1px solid var(--bs-border-color);
        }
        .class-card-body {
            padding: 1.25rem;
        }
        .method-block {
            border: 1px solid var(--bs-border-color);
            border-radius: 0.375rem;
            margin-bottom: 1rem;
            overflow: hidden;
        }
        .method-header {
            background: var(--ch-code-bg);
            padding: 0.625rem 1rem;
            font-family: "SFMono-Regular", Consolas, "Liberation Mono", Menlo, monospace;
            font-size: 0.825rem;
            border-bottom: 1px solid var(--bs-border-color);
            word-break: break-all;
        }
        .method-body {
            padding: 0.75rem 1rem;
            font-size: 0.875rem;
        }
        .badge-visibility {
            font-size: 0.7rem;
            font-weight: 600;
        }
        .badge-static {
            font-size: 0.65rem;
        }
        .param-table {
            font-size: 0.825rem;
        }
        .param-table td, .param-table th {
            padding: 0.35rem 0.75rem;
        }
        .param-type {
            font-family: monospace;
            color: var(--ch-primary);
        }
        .param-name {
            font-family: monospace;
            font-weight: 600;
        }
        .search-box {
            padding: 0.75rem 1.5rem;
            position: sticky;
            top: 0;
            background: var(--ch-sidebar-bg);
            z-index: 10;
        }
        .hero-section {
            background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
            color: white;
            padding: 2.5rem 2rem;
            border-radius: 0.75rem;
            margin-bottom: 2rem;
        }
        .stat-card {
            text-align: center;
            padding: 1rem;
        }
        .stat-card .stat-number {
            font-size: 2rem;
            font-weight: 700;
            color: rgba(255,255,255,0.95);
        }
        .stat-card .stat-label {
            font-size: 0.8rem;
            color: rgba(255,255,255,0.7);
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .extends-badge {
            font-size: 0.75rem;
            font-weight: 500;
        }
        .file-path {
            font-size: 0.75rem;
            color: var(--bs-secondary-color);
            font-family: monospace;
        }
        .docblock-text {
            font-size: 0.875rem;
            color: var(--bs-secondary-color);
            line-height: 1.5;
        }
        .property-row {
            padding: 0.5rem 0;
            border-bottom: 1px solid var(--bs-border-color-translucent);
            font-size: 0.85rem;
        }
        .property-row:last-child {
            border-bottom: none;
        }
        @media (max-width: 991.98px) {
            .sidebar { display: none; }
            .main-content { margin-left: 0; padding: 1rem; }
        }
        .theme-toggle {
            cursor: pointer;
            background: none;
            border: none;
            font-size: 1.1rem;
            color: var(--bs-body-color);
        }
    </style>
</head>
<body>

<!-- Sidebar -->
<nav class="sidebar" id="sidebar">
    <div class="px-4 pb-3 d-flex align-items-center justify-content-between">
        <div>
            <h6 class="mb-0 fw-bold"><i class="bi bi-heart-pulse me-1"></i> Claude Health</h6>
            <small class="text-muted">API Documentation</small>
        </div>
        <button class="theme-toggle" onclick="toggleTheme()" title="Toggle dark mode">
            <i class="bi bi-moon-fill" id="themeIcon"></i>
        </button>
    </div>
    <div class="search-box">
        <input type="text" class="form-control form-control-sm" id="searchInput" placeholder="Search classes and methods...">
    </div>

    <nav class="nav flex-column" id="navList">
        <a class="nav-link" href="#overview"><i class="bi bi-house me-1"></i> Overview</a>
<?php if (!empty($allFunctions)): ?>
        <div class="ns-header">Helper Functions</div>
        <a class="nav-link" href="#helpers"><i class="bi bi-braces me-1"></i> Global Functions</a>
<?php endif; ?>
<?php foreach ($groups as $ns => $classes): ?>
        <div class="ns-header"><?= htmlspecialchars($ns) ?></div>
<?php foreach ($classes as $cls): ?>
        <a class="nav-link searchable" href="#<?= htmlspecialchars($cls->fqcn) ?>" data-search="<?= strtolower($cls->name . ' ' . $cls->namespace) ?>">
            <?php if ($cls->type === 'abstract class'): ?>
                <i class="bi bi-puzzle me-1"></i>
            <?php else: ?>
                <i class="bi bi-file-earmark-code me-1"></i>
            <?php endif; ?>
            <?= htmlspecialchars($cls->name) ?>
        </a>
<?php endforeach; ?>
<?php endforeach; ?>
    </nav>
</nav>

<!-- Main Content -->
<div class="main-content">

    <!-- Hero -->
    <section id="overview">
        <div class="hero-section">
            <h1 class="mb-1"><i class="bi bi-heart-pulse me-2"></i>Claude Health</h1>
            <p class="mb-3 opacity-75">API Documentation &mdash; HIPAA-Compliant Health Tracking Platform</p>
            <div class="row g-0">
                <div class="col stat-card">
                    <div class="stat-number"><?= $totalClasses ?></div>
                    <div class="stat-label">Classes</div>
                </div>
                <div class="col stat-card">
                    <div class="stat-number"><?= $totalMethods ?></div>
                    <div class="stat-label">Methods</div>
                </div>
                <div class="col stat-card">
                    <div class="stat-number"><?= $totalFunctions ?></div>
                    <div class="stat-label">Functions</div>
                </div>
                <div class="col stat-card">
                    <div class="stat-number"><?= count($groups) ?></div>
                    <div class="stat-label">Namespaces</div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title mb-3"><i class="bi bi-info-circle me-1"></i> About This Documentation</h5>
                <p class="mb-2">This documentation covers the Claude Health application's PHP backend, organized by namespace. The codebase follows an MVC architecture with a custom micro-framework.</p>
                <ul class="mb-2">
                    <li><strong>Core</strong> &mdash; Framework foundation: Router, Database (PDO singleton), Session, View engine, Encryption (AES-256-CBC), Middleware base, Mailer, and abstract Model/Controller</li>
                    <li><strong>App\Controllers</strong> &mdash; HTTP request handlers for authentication, dashboard, entries, medications, appointments, analytics, calculators, and admin</li>
                    <li><strong>App\Models</strong> &mdash; Data access layer with field-level encryption for HIPAA-protected health information (PHI)</li>
                    <li><strong>App\Middleware</strong> &mdash; Request filters for authentication, CSRF protection, guest-only routes, and admin authorization</li>
                </ul>
                <p class="text-muted mb-0"><small><i class="bi bi-shield-check me-1"></i> HIPAA compliant: All PHI fields are encrypted at rest using AES-256-CBC. Audit logging tracks every data access.</small></p>
            </div>
        </div>
    </section>

<?php if (!empty($allFunctions)): ?>
    <!-- Helper Functions -->
    <section id="helpers" class="mb-5">
        <h3 class="border-bottom pb-2 mb-3"><i class="bi bi-braces me-2"></i>Helper Functions</h3>
        <p class="text-muted mb-3">Global utility functions available throughout the application.</p>

<?php foreach ($allFunctions as $func): ?>
        <div class="method-block">
            <div class="method-header">
                <span class="text-primary">function</span> <strong><?= htmlspecialchars($func['name']) ?></strong>(<?= htmlspecialchars($func['params']) ?>)<?= $func['returnType'] ? ': ' . htmlspecialchars($func['returnType']) : '' ?>
            </div>
            <div class="method-body">
<?php
    $docLines = explode("\n", $func['docblock']);
    $description = '';
    foreach ($docLines as $dl) {
        $dl = trim($dl);
        if ($dl === '' || str_starts_with($dl, '@')) break;
        $description .= ($description ? ' ' : '') . preg_replace('/^\*\s*/', '', $dl);
    }
    if ($description):
?>
                <p class="docblock-text mb-1"><?= htmlspecialchars($description) ?></p>
<?php endif; ?>
                <span class="file-path"><?= htmlspecialchars($func['file']) ?></span>
            </div>
        </div>
<?php endforeach; ?>
    </section>
<?php endif; ?>

<?php foreach ($groups as $ns => $classes): ?>
    <!-- Namespace: <?= $ns ?> -->
    <section class="mb-5">
        <h3 class="border-bottom pb-2 mb-3"><i class="bi bi-folder me-2"></i><?= htmlspecialchars($ns) ?></h3>

<?php foreach ($classes as $cls): ?>
        <div class="class-card" id="<?= htmlspecialchars($cls->fqcn) ?>">
            <div class="class-card-header d-flex align-items-start justify-content-between">
                <div>
                    <h5 class="mb-1">
                        <?php if ($cls->type === 'abstract class'): ?>
                            <span class="badge bg-secondary me-1">abstract</span>
                        <?php endif; ?>
                        <?= htmlspecialchars($cls->name) ?>
                        <?php if ($cls->extends): ?>
                            <span class="extends-badge text-muted ms-2">extends <code><?= htmlspecialchars($cls->extends) ?></code></span>
                        <?php endif; ?>
                    </h5>
                    <span class="file-path"><i class="bi bi-file-earmark me-1"></i><?= htmlspecialchars($cls->filePath) ?></span>
                </div>
                <span class="badge bg-primary"><?= count(array_filter($cls->methods, fn($m) => $m->visibility === 'public')) ?> public methods</span>
            </div>
            <div class="class-card-body">

<?php if ($cls->docblock): ?>
                <p class="docblock-text mb-3"><?= nl2br(htmlspecialchars($cls->docblock)) ?></p>
<?php endif; ?>

<?php
    // Show properties (non-private only)
    $visibleProps = array_filter($cls->properties, fn($p) => $p->visibility !== 'private');
    if (!empty($visibleProps)):
?>
                <h6 class="text-muted mb-2"><i class="bi bi-database me-1"></i> Properties</h6>
                <div class="mb-3 ps-2">
<?php foreach ($visibleProps as $prop): ?>
                    <div class="property-row">
                        <span class="badge badge-visibility <?= $prop->visibility === 'public' ? 'bg-success' : 'bg-warning text-dark' ?>"><?= $prop->visibility ?></span>
                        <?php if ($prop->isStatic): ?><span class="badge badge-static bg-info text-dark">static</span><?php endif; ?>
                        <?php if ($prop->type): ?><code class="param-type"><?= htmlspecialchars($prop->type) ?></code><?php endif; ?>
                        <code class="param-name">$<?= htmlspecialchars($prop->name) ?></code>
                        <?php if ($prop->defaultValue): ?><span class="text-muted">= <?= htmlspecialchars($prop->defaultValue) ?></span><?php endif; ?>
                    </div>
<?php endforeach; ?>
                </div>
<?php endif; ?>

<?php
    // Show public methods
    $publicMethods = array_filter($cls->methods, fn($m) => $m->visibility === 'public');
    if (!empty($publicMethods)):
?>
                <h6 class="text-muted mb-2"><i class="bi bi-gear me-1"></i> Public Methods</h6>
<?php foreach ($publicMethods as $method): ?>
                <div class="method-block">
                    <div class="method-header">
                        <span class="badge badge-visibility bg-success me-1">public</span>
                        <?php if ($method->isStatic): ?><span class="badge badge-static bg-info text-dark me-1">static</span><?php endif; ?>
                        <?php if ($method->isAbstract): ?><span class="badge badge-static bg-warning text-dark me-1">abstract</span><?php endif; ?>
                        <strong><?= htmlspecialchars($method->name) ?></strong>(<?php
                            $paramParts = [];
                            foreach ($method->parameters as $p) {
                                $s = '';
                                if ($p['type']) $s .= '<span class="param-type">' . htmlspecialchars($p['type']) . '</span> ';
                                $s .= '<span class="param-name">' . htmlspecialchars($p['name']) . '</span>';
                                if ($p['default'] !== '') $s .= ' = ' . htmlspecialchars($p['default']);
                                $paramParts[] = $s;
                            }
                            echo implode(', ', $paramParts);
                        ?>)<?php if ($method->returnType): ?>: <span class="param-type"><?= htmlspecialchars($method->returnType) ?></span><?php endif; ?>
                    </div>
<?php
    // Parse docblock for description and @param tags
    $docLines = explode("\n", $method->docblock);
    $desc = '';
    $paramDocs = [];
    $returnDoc = '';
    foreach ($docLines as $dl) {
        $dl = trim($dl);
        if (preg_match('/@param\s+(\S+)\s+(\$\w+)\s*(.*)/i', $dl, $pdm)) {
            $paramDocs[$pdm[2]] = ['type' => $pdm[1], 'desc' => $pdm[3]];
        } elseif (preg_match('/@return\s+(\S+)\s*(.*)/i', $dl, $rdm)) {
            $returnDoc = $rdm[1] . ($rdm[2] ? ' - ' . $rdm[2] : '');
        } elseif (!str_starts_with($dl, '@') && $dl !== '' && $dl !== '*') {
            $desc .= ($desc ? ' ' : '') . $dl;
        }
    }
    if ($desc || !empty($method->parameters)):
?>
                    <div class="method-body">
<?php if ($desc): ?>
                        <p class="docblock-text mb-2"><?= htmlspecialchars($desc) ?></p>
<?php endif; ?>
<?php if (!empty($method->parameters)): ?>
                        <table class="table table-sm param-table mb-0">
                            <thead>
                                <tr>
                                    <th>Parameter</th>
                                    <th>Type</th>
                                    <th>Default</th>
                                    <th>Description</th>
                                </tr>
                            </thead>
                            <tbody>
<?php foreach ($method->parameters as $p): ?>
                                <tr>
                                    <td class="param-name"><?= htmlspecialchars($p['name']) ?></td>
                                    <td class="param-type"><?= htmlspecialchars($p['type'] ?: ($paramDocs[$p['name']]['type'] ?? '')) ?></td>
                                    <td><?= $p['default'] !== '' ? '<code>' . htmlspecialchars($p['default']) . '</code>' : '<span class="text-muted">&mdash;</span>' ?></td>
                                    <td class="text-muted"><?= htmlspecialchars($paramDocs[$p['name']]['desc'] ?? '') ?></td>
                                </tr>
<?php endforeach; ?>
                            </tbody>
                        </table>
<?php endif; ?>
<?php if ($returnDoc): ?>
                        <p class="mt-2 mb-0"><small><strong>Returns:</strong> <code><?= htmlspecialchars($returnDoc) ?></code></small></p>
<?php endif; ?>
                    </div>
<?php endif; ?>
                </div>
<?php endforeach; ?>
<?php endif; ?>

<?php
    // Show protected methods
    $protectedMethods = array_filter($cls->methods, fn($m) => $m->visibility === 'protected');
    if (!empty($protectedMethods)):
?>
                <details class="mt-3">
                    <summary class="text-muted" style="cursor:pointer;font-size:0.875rem;">
                        <i class="bi bi-lock me-1"></i> <?= count($protectedMethods) ?> protected method<?= count($protectedMethods) > 1 ? 's' : '' ?>
                    </summary>
                    <div class="mt-2">
<?php foreach ($protectedMethods as $method): ?>
                        <div class="method-block">
                            <div class="method-header">
                                <span class="badge badge-visibility bg-warning text-dark me-1">protected</span>
                                <?php if ($method->isStatic): ?><span class="badge badge-static bg-info text-dark me-1">static</span><?php endif; ?>
                                <strong><?= htmlspecialchars($method->name) ?></strong>(<?php
                                    $pp = [];
                                    foreach ($method->parameters as $p) {
                                        $s = '';
                                        if ($p['type']) $s .= htmlspecialchars($p['type']) . ' ';
                                        $s .= htmlspecialchars($p['name']);
                                        if ($p['default'] !== '') $s .= ' = ' . htmlspecialchars($p['default']);
                                        $pp[] = $s;
                                    }
                                    echo implode(', ', $pp);
                                ?>)<?php if ($method->returnType): ?>: <?= htmlspecialchars($method->returnType) ?><?php endif; ?>
                            </div>
                        </div>
<?php endforeach; ?>
                    </div>
                </details>
<?php endif; ?>

            </div>
        </div>
<?php endforeach; ?>
    </section>
<?php endforeach; ?>

    <!-- Footer -->
    <footer class="border-top pt-3 mt-5 text-muted">
        <div class="d-flex justify-content-between align-items-center">
            <small>
                <i class="bi bi-clock me-1"></i> Generated on <?= $generatedDate ?>
            </small>
            <small>
                <i class="bi bi-person me-1"></i> J.J. Johnson &lt;visionquest716@gmail.com&gt;
            </small>
        </div>
        <p class="mt-2 mb-0"><small class="text-muted">Claude Health &copy; <?= date('Y') ?> &mdash; HIPAA-Compliant Health Tracking Platform</small></p>
    </footer>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Search
    document.getElementById('searchInput').addEventListener('input', function () {
        const query = this.value.toLowerCase();
        document.querySelectorAll('#navList .searchable').forEach(function (link) {
            const text = link.getAttribute('data-search') || link.textContent.toLowerCase();
            link.style.display = text.includes(query) ? '' : 'none';
        });
    });

    // Theme toggle
    function toggleTheme() {
        const html = document.documentElement;
        const current = html.getAttribute('data-bs-theme');
        const next = current === 'dark' ? 'light' : 'dark';
        html.setAttribute('data-bs-theme', next);
        document.getElementById('themeIcon').className = next === 'dark' ? 'bi bi-sun-fill' : 'bi bi-moon-fill';
        localStorage.setItem('ch-doc-theme', next);
    }

    // Restore theme preference
    (function () {
        const saved = localStorage.getItem('ch-doc-theme');
        if (saved === 'dark') {
            document.documentElement.setAttribute('data-bs-theme', 'dark');
            document.getElementById('themeIcon').className = 'bi bi-sun-fill';
        }
    })();

    // Smooth scroll for nav links
    document.querySelectorAll('.sidebar .nav-link').forEach(function (link) {
        link.addEventListener('click', function (e) {
            const href = this.getAttribute('href');
            if (href && href.startsWith('#')) {
                e.preventDefault();
                const target = document.getElementById(href.substring(1)) || document.querySelector(href);
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            }
        });
    });
</script>
</body>
</html>
<?php
$html = ob_get_clean();

file_put_contents($outputFile, $html);

echo "Documentation generated successfully!\n";
echo "  Output: {$outputFile}\n";
echo "  Classes: {$totalClasses}\n";
echo "  Methods: {$totalMethods}\n";
echo "  Functions: {$totalFunctions}\n";
echo "  Namespaces: " . count($groups) . "\n";
