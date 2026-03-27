#!/bin/bash
set -e

# VQ Healthy Deployment Script
# Usage: bash scripts/deploy.sh

DEPLOY_START=$(date '+%Y-%m-%d %H:%M:%S')
echo "=== VQ Healthy Deploy ==="
echo "Started: ${DEPLOY_START}"

# Pull latest code
echo ">> git pull..."
git pull origin main

# Install dependencies (production)
echo ">> composer install..."
composer install --no-dev --optimize-autoloader --no-interaction

# Run database migrations
echo ">> Running migrations..."
php scripts/migrate.php

# Set permissions
echo ">> Setting permissions..."
chmod -R 755 public/
chmod 640 .env
chmod 700 secrets.txt 2>/dev/null || true

DEPLOY_END=$(date '+%Y-%m-%d %H:%M:%S')
COMMIT_HASH=$(git rev-parse --short HEAD)

echo "=== Deploy complete ==="
echo "Commit: ${COMMIT_HASH}"
echo "Finished: ${DEPLOY_END}"

# Send deployment notification email
if command -v php &>/dev/null; then
    php -r "
    require_once __DIR__ . '/../vendor/autoload.php';
    \$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
    \$dotenv->load();
    require_once __DIR__ . '/../core/Mailer.php';
    Core\Mailer::send(
        'email4johnson@gmail.com',
        'VQ Healthy Deployed - ${COMMIT_HASH}',
        '<h2>VQ Healthy Deployed</h2>'
        . '<p><strong>Commit:</strong> ${COMMIT_HASH}</p>'
        . '<p><strong>Started:</strong> ${DEPLOY_START}</p>'
        . '<p><strong>Finished:</strong> ${DEPLOY_END}</p>'
        . '<p><strong>URL:</strong> <a href=\"https://vqhealthy.com\">https://vqhealthy.com</a></p>',
        'claude_health'
    );
    echo \"Deployment email sent.\n\";
    " 2>/dev/null || echo "Email notification skipped (Mailer not available)"
fi
