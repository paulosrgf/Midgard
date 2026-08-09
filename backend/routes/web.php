<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return ['Laravel' => app()->version()];
});

require __DIR__.'/auth.php';

use Illuminate\Support\Facades\Artisan;

Route::get('/setup-db-prod', function () {
    try {
        // Roda as migrations e o seeder ignorando os alertas de produção
        Artisan::call('migrate:fresh', ['--force' => true, '--seed' => true]);
        return 'Banco de dados montado e populado com sucesso em Produção!';
    } catch (\Exception $e) {
        return 'Erro ao migrar: ' . $e->getMessage();
    }
});
