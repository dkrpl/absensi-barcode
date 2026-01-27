<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class BackupDatabase extends Command
{
    protected $signature = 'db:backup';
    protected $description = 'Backup database';

    public function handle()
    {
        $filename = "backup-" . date('Y-m-d-H-i-s') . ".sql";

        $command = sprintf(
            'mysqldump --user=%s --password=%s --host=%s %s > %s',
            config('database.connections.mysql.username'),
            config('database.connections.mysql.password'),
            config('database.connections.mysql.host'),
            config('database.connections.mysql.database'),
            storage_path('app/backups/' . $filename)
        );

        exec($command);

        $this->info('Database backup created: ' . $filename);
    }
}
