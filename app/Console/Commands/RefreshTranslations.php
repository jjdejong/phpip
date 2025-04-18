<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Database\Seeders\TranslatedAttributesSeeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RefreshTranslations extends Command
{
    protected $signature = 'translations:refresh {--force : Force refresh without preserving customizations}';
    protected $description = 'Refresh translations from the seeder while preserving any user customizations';

    public function handle()
    {
        $this->info('Starting translations refresh...');
        Log::info('Translation refresh initiated via command');

        $tables = [
            'actor_role' => ['key' => 'code', 'column' => 'name'],
            'classifier_type' => ['key' => 'code', 'column' => 'type'],
            'event_name' => ['key' => 'code', 'column' => 'name'],
            'matter_category' => ['key' => 'code', 'column' => 'category'],
            'matter_type' => ['key' => 'code', 'column' => 'type'],
            'task_rules' => ['key' => 'id', 'column' => 'detail']
        ];

        // Backup existing translations if not using force
        $backups = [];
        if (!$this->option('force')) {
            foreach ($tables as $table => $config) {
                $backups[$table] = DB::table($table)
                    ->whereNotNull($config['column'])
                    ->get([$config['key'], $config['column']])
                    ->mapWithKeys(function ($item) use ($config) {
                        return [$item->{$config['key']} => json_decode($item->{$config['column']}, true)];
                    })
                    ->toArray();
            }
        }

        // Run the seeder
        $seeder = new TranslatedAttributesSeeder();
        $seeder->run();

        // Restore user customizations if not using force
        if (!$this->option('force')) {
            foreach ($tables as $table => $config) {
                if (empty($backups[$table])) continue;

                foreach ($backups[$table] as $key => $translations) {
                    if (!is_array($translations)) continue;

                    // Merge with new translations, preserving user customizations
                    $current = DB::table($table)
                        ->where($config['key'], $key)
                        ->value($config['column']);
                    
                    if ($current) {
                        $currentArray = json_decode($current, true);
                        if (is_array($currentArray)) {
                            // Preserve user customizations while adding any new translations
                            $merged = array_merge($currentArray, $translations);
                            
                            DB::table($table)
                                ->where($config['key'], $key)
                                ->update([
                                    $config['column'] => json_encode($merged, 
                                        JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT
                                    )
                                ]);
                        }
                    }
                }
                $this->info("Restored customizations for {$table}");
            }
        }

        $this->info('Translations refresh completed successfully.');
        Log::info('Translation refresh completed');
    }
}