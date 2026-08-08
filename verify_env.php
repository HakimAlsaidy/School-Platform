<?php
// Verify .env branding values
$envFile = __DIR__ . '/.env';
$content = file_get_contents($envFile);

echo "--- Current branding values ---\n";
foreach (explode("\n", $content) as $line) {
    $line = trim($line);
    if (preg_match('/^(APP_NAME|APP_DOMAIN|DB_DATABASE|MAIL_FROM_NAME)=/', $line)) {
        echo $line . "\n";
    }
}
echo "--- End ---\n";
