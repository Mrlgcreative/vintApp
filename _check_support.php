<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "ALL SUPPORT CHATS:" . PHP_EOL;
$chats = \App\Models\SupportChat::all();
foreach ($chats as $c) {
    echo "id=" . $c->id . " | ref=" . $c->reference . " | admin_id=" . ($c->admin_id ?? "NULL") . " | status=" . $c->status . " | user_id=" . $c->user_id . PHP_EOL;
}
echo PHP_EOL . "Total: " . $chats->count() . PHP_EOL;

echo PHP_EOL . "SUPPORT AGENTS:" . PHP_EOL;
$agents = \App\Models\SupportAgent::with('user')->get();
foreach ($agents as $a) {
    echo "id=" . $a->id . " | user_id=" . $a->user_id . " | user=" . $a->user->name . " | active=" . $a->is_active . PHP_EOL;
}

echo PHP_EOL . "ROLE SUPPORT USERS:" . PHP_EOL;
$rows = \Illuminate\Support\Facades\DB::table('role_user')
    ->join('roles', 'role_user.role_id', '=', 'roles.id')
    ->where('roles.slug', 'support')
    ->join('users', 'role_user.user_id', '=', 'users.id')
    ->select('users.id', 'users.name')
    ->get();
foreach ($rows as $r) {
    echo "user_id=" . $r->id . " | name=" . $r->name . PHP_EOL;
}
