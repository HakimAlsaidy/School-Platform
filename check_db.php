<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Users: " . \App\Models\User::count() . PHP_EOL;
echo "Active Users: " . \App\Models\User::where('is_active', true)->count() . PHP_EOL;

$parentRole = \App\Models\Role::where('slug', 'parent')->first();
echo "Parent Role ID: " . $parentRole->id . PHP_EOL;
echo "Parent Users (active): " . \App\Models\User::where('role_id', $parentRole->id)->where('is_active', true)->count() . PHP_EOL;

echo "Messages: " . \App\Models\Message::count() . PHP_EOL;
echo "Announcements: " . \App\Models\Announcement::count() . PHP_EOL;
