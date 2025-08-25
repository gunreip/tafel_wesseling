<?php

use Illuminate\Support\Facades\Schedule;

/**
 * 🔧 Regelmäßige Wartungsjobs
 */

// 1) Queue-Worker regelmäßig neu starten (lädt frischen Code/Config)
Schedule::command('queue:restart')
    ->hourly()
    ->name('queue:restart');

// 2) Alte Batch-Daten aufräumen (falls du Bus/Batches nutzt)
Schedule::command('queue:prune-batches --hours=48')
    ->dailyAt('02:15')
    ->name('queue:prune-batches');

// 3) Abgelaufene Password-Resets löschen (Core-Command)
Schedule::command('auth:clear-resets')
    ->dailyAt('03:00')
    ->name('auth:clear-resets');

// Hinweis: Bei withoutOverlapping() immer vorher .name('…') vergeben (Laravel 11+ fordert das). Für obige Core-Commands ist withoutOverlapping() i. d. R. nicht nötig.

/**
 * 🧩 Hier kannst du später deine projekt-spezifischen Jobs ergänzen,
 *     z. B. Reports, Mails, Backups, Importer etc.
 *
 * Beispiel (Call-Closure):
 *
 * Schedule::call(function () {
 *     // ... dein Code ...
 * })->dailyAt('04:00')->name('custom:daily-report');
 */
// DB-Backup (nur Datenbank)
Schedule::command('backup:run --only-db')
    ->dailyAt('17:30')
    ->name('backup:run-db');

// Ältere Backups aufräumen
Schedule::command('backup:clean')
    ->dailyAt('17:45')
    ->name('backup:clean');

// Monitoring (prüft Frische & Health der Backups)
Schedule::command('backup:monitor')
    ->dailyAt('18:00')
    ->name('backup:monitor');
