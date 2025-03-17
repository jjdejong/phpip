<?php

namespace Database\Seeders;

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
use Illuminate\Database\Seeder;

class TranslationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $supportedLocales = ['en', 'fr', 'de', 'es'];
        
        foreach ($supportedLocales as $locale) {
            $this->command->info("Seeding translations for locale: $locale");
            
            // Seed EventName translations
            $eventNames = EventName::all();
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
                }
            }
            
            // Seed ClassifierType translations
            $classifierTypes = ClassifierType::all();
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
                }
            }
            
            // Seed MatterCategory translations
            $categories = Category::all();
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
                }
            }
            
            // Seed MatterType translations
            $matterTypes = MatterType::all();
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
                }
            }
            
            // Seed TaskRule translations
            $rules = Rule::all();
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
                }
            }
            
            // Seed ActorRole translations
            $roles = Role::all();
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
                }
            }
        }
    }
}