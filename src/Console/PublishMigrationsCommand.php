<?php

namespace Notificano\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class PublishMigrationsCommand extends Command
{
    protected $signature = 'notificano:publish-migrations';
    protected $description = 'Publish Notificano migration files with unique class names';

    public function handle()
    {
        $migrations = [
            [
                'stub' => __DIR__ . '/../../database/migrations/create_notifications_table.stub',
                'class' => 'CreateNotificationsTable',
                'suffix' => 'create_notifications_table',
            ],
            [
                'stub' => __DIR__ . '/../../database/migrations/add_avatar_to_users_table.stub',
                'class' => 'AddAvatarToUsersTable',
                'suffix' => 'add_avatar_to_users_table',
            ],
        ];

        foreach ($migrations as $migration) {
            $timestamp = date('Y_m_d_His');
            $target = database_path("migrations/{$timestamp}_{$migration['suffix']}.php");
            $stub = file_get_contents($migration['stub']);
            $stub = str_replace('{{ class }}', $migration['class'], $stub);
            file_put_contents($target, $stub);
            $this->info("Published migration: {$target}");
            sleep(1); // Ensure unique timestamps
        }

        // Run migrations automatically
        $this->info('Running migrations...');
        $this->call('migrate');
    }
}
