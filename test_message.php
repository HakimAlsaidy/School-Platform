<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Role;
use App\Models\Message;
use App\Models\Announcement;

try {
    $parentRole = Role::where('slug', 'parent')->first();
    $adminUser = User::where('email', 'admin@school.com')->first();
    $parentUsers = User::where('role_id', $parentRole->id)->where('is_active', true)->get();
    
    echo "Admin User ID: " . $adminUser->id . PHP_EOL;
    echo "Parent Users count: " . $parentUsers->count() . PHP_EOL;
    
    // إنشاء رسالة اختبارية
    $msg = Message::create([
        'sender_id' => $parentUsers->first()->id,
        'receiver_id' => $adminUser->id,
        'subject' => 'رسالة اختبار',
        'content' => 'محتوى الرسالة',
        'is_read' => false,
    ]);
    
    echo "Message created with ID: " . $msg->id . PHP_EOL;
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . PHP_EOL;
    echo "File: " . $e->getFile() . ":" . $e->getLine() . PHP_EOL;
}
