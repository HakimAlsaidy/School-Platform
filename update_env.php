<?php
// Script to rebrand the platform from SchoolPla to Edulink (إيدو لينك) - now fully migrated
$envFile = __DIR__ . '/.env';
$content = file_get_contents($envFile);

// 1. Ensure APP_NAME is set to إيدو لينك
$content = preg_replace('/^APP_NAME=.*$/m', 'APP_NAME=إيدو لينك', $content);

// 2. Add/set APP_DOMAIN
if (preg_match('/^APP_DOMAIN=.*$/m', $content)) {
    $content = preg_replace('/^APP_DOMAIN=.*$/m', 'APP_DOMAIN=edulink.test', $content);
} else {
    $content = "APP_DOMAIN=edulink.test\n" . $content;
}

// 3. Update DB_DATABASE
$content = preg_replace('/^DB_DATABASE=.*$/m', 'DB_DATABASE=edulink', $content);

// 4. Update MAIL_FROM_NAME/APP name references are handled by APP_NAME

file_put_contents($envFile, $content);

// Verify all relevant keys
echo "=== .env updated ===\n";
$lines = file($envFile);
foreach ($lines as $line) {
    if (preg_match('/^(APP_NAME|APP_DOMAIN|DB_DATABASE)=/', trim($line))) {
        echo trim($line) . "\n";
    }
}
