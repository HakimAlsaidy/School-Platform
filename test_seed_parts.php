<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Role;
use App\Models\Message;
use App\Models\Announcement;

echo "Checking existing data...\n";
echo "Users: " . User::count() . "\n";
echo "Messages before: " . Message::count() . "\n";

// إضافة الرسائل يدوياً
$parentRole = Role::where('slug', 'parent')->first();
$adminUser = User::where('email', 'admin@school.com')->first();
$parentUsers = User::where('role_id', $parentRole->id)->where('is_active', true)->get();

echo "AdminUser: " . ($adminUser ? $adminUser->id : 'NULL') . "\n";
echo "Parent Users: " . $parentUsers->count() . "\n";

$messageTemplates = [
    ['subject' => 'استفسار عن مستوى الطالب', 'content' => 'السلام عليكم'],
    ['subject' => 'طلب إذن غياب', 'content' => 'السلام عليكم'],
];

echo "Creating messages...\n";
$index = 0;
foreach ($parentUsers->take(2) as $parent) {
    echo "Creating message from user {$parent->id}...\n";
    Message::create([
        'sender_id' => $parent->id,
        'receiver_id' => $adminUser->id,
        'subject' => $messageTemplates[$index % 2]['subject'],
        'content' => $messageTemplates[$index % 2]['content'],
        'is_read' => false,
    ]);
    $index++;
    echo "Created!\n";
}

echo "\nMessages after: " . Message::count() . "\n";

// إضافة إعلان
echo "Creating announcement...\n";
Announcement::create([
    'title' => 'مرحباً',
    'content' => 'محتوى الإعلان',
    'target' => 'all',
    'is_pinned' => false,
    'author_id' => $adminUser->id,
]);
echo "Announcements: " . Announcement::count() . "\n";

echo "DONE!\n";
