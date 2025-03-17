<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\ClassifierType;
use App\Models\EventName;
use App\Models\MatterType;
use App\Models\Role;
use App\Models\Rule;
use App\Models\Translations\ActorRoleTranslation;
use App\Models\Translations\ClassifierTypeTranslation;
use App\Models\Translations\EventNameTranslation;
use App\Models\Translations\MatterCategoryTranslation;
use App\Models\Translations\MatterTypeTranslation;
use App\Models\Translations\TaskRuleTranslation;
use Illuminate\Console\Command;

class SeedTranslations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'translations:seed {locale?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed translation tables with data from base tables';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $locale = $this->argument('locale') ?? config('app.fallback_locale');
        $this->info("Seeding translations for locale: $locale");

        // Seed EventName translations
        $this->info('Seeding EventName translations...');
        $eventNames = EventName::all();
        $count = 0;
        foreach ($eventNames as $eventName) {
            $exists = EventNameTranslation::where('code', $eventName->code)
                ->where('locale', $locale)
                ->exists();
                
            if (!$exists) {
                EventNameTranslation::create([
                    'code' => $eventName->code,
                    'locale' => $locale,
                    'name' => $eventName->getRawOriginal('name') ?? '',
                    'notes' => $eventName->getRawOriginal('notes') ?? '',
                ]);
                $count++;
            }
        }
        $this->info("  Created $count EventName translations");

        // Seed ClassifierType translations
        $this->info('Seeding ClassifierType translations...');
        $classifierTypes = ClassifierType::all();
        $count = 0;
        foreach ($classifierTypes as $classifierType) {
            $exists = ClassifierTypeTranslation::where('code', $classifierType->code)
                ->where('locale', $locale)
                ->exists();
                
            if (!$exists) {
                ClassifierTypeTranslation::create([
                    'code' => $classifierType->code,
                    'locale' => $locale,
                    'type' => $classifierType->getRawOriginal('type') ?? '',
                    'notes' => $classifierType->getRawOriginal('notes') ?? '',
                ]);
                $count++;
            }
        }
        $this->info("  Created $count ClassifierType translations");

        // Seed MatterCategory translations
        $this->info('Seeding MatterCategory translations...');
        $categories = Category::all();
        $count = 0;
        foreach ($categories as $category) {
            $exists = MatterCategoryTranslation::where('code', $category->code)
                ->where('locale', $locale)
                ->exists();
                
            if (!$exists) {
                MatterCategoryTranslation::create([
                    'code' => $category->code,
                    'locale' => $locale,
                    'category' => $category->getRawOriginal('category') ?? '',
                ]);
                $count++;
            }
        }
        $this->info("  Created $count MatterCategory translations");

        // Seed MatterType translations
        $this->info('Seeding MatterType translations...');
        $matterTypes = MatterType::all();
        $count = 0;
        foreach ($matterTypes as $matterType) {
            $exists = MatterTypeTranslation::where('code', $matterType->code)
                ->where('locale', $locale)
                ->exists();
                
            if (!$exists) {
                MatterTypeTranslation::create([
                    'code' => $matterType->code,
                    'locale' => $locale,
                    'type' => $matterType->getRawOriginal('type') ?? '',
                ]);
                $count++;
            }
        }
        $this->info("  Created $count MatterType translations");

        // Seed TaskRule translations
        $this->info('Seeding TaskRule translations...');
        $rules = Rule::all();
        $count = 0;
        foreach ($rules as $rule) {
            $exists = TaskRuleTranslation::where('task_rule_id', $rule->id)
                ->where('locale', $locale)
                ->exists();
                
            if (!$exists) {
                TaskRuleTranslation::create([
                    'task_rule_id' => $rule->id,
                    'locale' => $locale,
                    'detail' => $rule->getRawOriginal('detail') ?? '',
                    'notes' => $rule->getRawOriginal('notes') ?? '',
                ]);
                $count++;
            }
        }
        $this->info("  Created $count TaskRule translations");

        // Seed ActorRole translations
        $this->info('Seeding ActorRole translations...');
        $roles = Role::all();
        $count = 0;
        foreach ($roles as $role) {
            $exists = ActorRoleTranslation::where('code', $role->code)
                ->where('locale', $locale)
                ->exists();
                
            if (!$exists) {
                ActorRoleTranslation::create([
                    'code' => $role->code,
                    'locale' => $locale,
                    'name' => $role->getRawOriginal('name') ?? '',
                    'notes' => $role->getRawOriginal('notes') ?? '',
                ]);
                $count++;
            }
        }
        $this->info("  Created $count ActorRole translations");

        $this->info('Translation seeding complete!');
    }
}