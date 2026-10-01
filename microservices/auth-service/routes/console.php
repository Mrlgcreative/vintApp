<?php

use Illuminate\Support\Facades\Artisan;

Artisan::command('auth:seed-roles', function () {
    $this->call('db:seed', ['--class' => 'RoleSeeder']);
})->purpose('Crée les rôles par défaut (admin, vendeur, expert, acheteur)');
