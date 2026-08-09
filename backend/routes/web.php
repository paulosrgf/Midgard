<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return ['Laravel' => app()->version()];
});

require __DIR__.'/auth.php';

Route::get('/setup-db-prod', function () {
    try {
        \Illuminate\Support\Facades\Artisan::call('migrate:fresh', ['--force' => true, '--seed' => true]);
        return 'Banco de dados montado e populado com sucesso em Produção!';
    } catch (\Throwable $e) {
        return 'Erro ao migrar: ' . $e->getMessage() . ' no arquivo ' . $e->getFile() . ' linha ' . $e->getLine();
    }
});
