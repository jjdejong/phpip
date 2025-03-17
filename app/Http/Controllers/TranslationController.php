<?php

namespace App\Http\Controllers;

use App\Models\Actor;
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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class TranslationController extends Controller
{
    /**
     * Display a list of available entity types for translation.
     */
    public function index()
    {
        Gate::authorize('admin');
        
        // Get user's preferred language
        $locale = Auth::user()->getLanguage();
        // Extract the language part (without region) for UI translations
        $uiLanguage = explode('_', $locale)[0];
        // Set application locale to the language part for UI translations
        app()->setLocale($uiLanguage);
        // Store full locale for date/number formatting
        session(['formatting_locale' => $locale]);
        
        $entities = [
            'event_name' => EventName::count(),
            'classifier_type' => ClassifierType::count(),
            'matter_category' => Category::count(),
            'matter_type' => MatterType::count(),
            'task_rules' => Rule::count(),
            'actor_role' => Role::count(),
        ];
        
        return view('translation.index', compact('entities'));
    }
    
    /**
     * Display a list of entities for the selected type.
     */
    public function listEntities(Request $request, string $type)
    {
        Gate::authorize('admin');
        
        // Get list of available languages
        $languages = Actor::distinct('language')
            ->whereNotNull('language')
            ->pluck('language')
            ->toArray();
            
        // Filter out English variants and add a single 'en' entry
        $filteredLanguages = [];
        $hasEnglish = false;
        
        foreach ($languages as $language) {
            // Skip empty language entries
            if (empty($language)) {
                continue;
            }
            
            $baseLanguage = explode('_', $language)[0];
            
            if ($baseLanguage === 'en') {
                $hasEnglish = true;
            } else {
                $filteredLanguages[] = $language;
            }
        }
        
        // Add a single 'en' entry for all English variants
        if ($hasEnglish || in_array(config('app.locale'), ['en', 'en_GB', 'en_US'])) {
            $filteredLanguages[] = 'en';
        }
        
        // Make sure app locale is available
        if (!in_array(config('app.locale'), $filteredLanguages) && 
            explode('_', config('app.locale'))[0] !== 'en') {
            $filteredLanguages[] = config('app.locale');
        }
        
        // Ensure we have at least these standard languages
        $standardLanguages = ['en', 'fr', 'de', 'es'];
        foreach ($standardLanguages as $lang) {
            if (!in_array($lang, $filteredLanguages)) {
                $filteredLanguages[] = $lang;
            }
        }
        
        $languages = $filteredLanguages;
        
        // Get the UI language (for interface elements)
        $interfaceLocale = $request->interface_locale ?? Auth::user()->getLanguage();
        $uiLanguage = explode('_', $interfaceLocale)[0]; // Just the language part for UI
        app()->setLocale($uiLanguage); // Set for UI translations
        session(['formatting_locale' => $interfaceLocale]); // For date formatting
        
        // Get the content language (for displaying translations)
        $contentLocale = $request->content_locale ?? $request->locale ?? Auth::user()->getLanguage(true);
        
        // Force locale to display translations in the selected language
        $previousLocale = app()->getLocale();
        app()->setLocale($contentLocale);
        
        // Store the selected locales for the view
        $selectedUiLocale = $interfaceLocale;
        $selectedContentLocale = $contentLocale;
        
        // IMPORTANT: Store the content locale in the session so accessors can use it
        session(['content_locale' => $contentLocale]);
        
        // This is crucial: we need to clear the model cache to ensure translations are loaded with the current locale
        app('db')->flushQueryLog();
        
        // Apply filters based on request
        $Code = $request->input('Code');
        $Text = $request->input('Text');
        $TaskName = $request->input('TaskName');
        
        switch ($type) {
            case 'event_name':
                $entities = EventName::query();
                
                // Apply filters
                if (!is_null($Code)) {
                    $entities = $entities->where('code', 'like', $Code.'%');
                }
                if (!is_null($Text)) {
                    $entities = $entities->where('name', 'like', '%'.$Text.'%');
                }
                
                $entities = $entities->orderBy('code')->paginate(25);
                
                // Force model hydration to happen with the current locale
                $entities->map(function($item) use ($contentLocale) {
                    // Update the cached name property
                    $item->name = $item->getTranslation('name', $contentLocale);
                    return $item;
                });
                break;
            case 'classifier_type':
                $entities = ClassifierType::query();
                
                // Apply filters
                if (!is_null($Code)) {
                    $entities = $entities->where('code', 'like', $Code.'%');
                }
                if (!is_null($Text)) {
                    $entities = $entities->where('type', 'like', '%'.$Text.'%');
                }
                
                $entities = $entities->orderBy('code')->paginate(25);
                
                $entities->map(function($item) use ($contentLocale) {
                    // Update the cached type property
                    $item->type = $item->getTranslation('type', $contentLocale);
                    return $item;
                });
                break;
            case 'matter_category':
                $entities = Category::query();
                
                // Apply filters
                if (!is_null($Code)) {
                    $entities = $entities->where('code', 'like', $Code.'%');
                }
                if (!is_null($Text)) {
                    $entities = $entities->where('category', 'like', '%'.$Text.'%');
                }
                
                $entities = $entities->orderBy('code')->paginate(25);
                
                $entities->map(function($item) use ($contentLocale) {
                    // Update the cached category property
                    $item->category = $item->getTranslation('category', $contentLocale);
                    return $item;
                });
                break;
            case 'matter_type':
                $entities = MatterType::query();
                
                // Apply filters
                if (!is_null($Code)) {
                    $entities = $entities->where('code', 'like', $Code.'%');
                }
                if (!is_null($Text)) {
                    $entities = $entities->where('type', 'like', '%'.$Text.'%');
                }
                
                $entities = $entities->orderBy('code')->paginate(25);
                
                $entities->map(function($item) use ($contentLocale) {
                    // Update the cached type property
                    $item->type = $item->getTranslation('type', $contentLocale);
                    return $item;
                });
                break;
            case 'task_rules':
                $entities = Rule::with('taskInfo');
                
                // Apply filters
                if (!is_null($Code)) {
                    $entities = $entities->where('id', 'like', $Code.'%');
                }
                if (!is_null($Text)) {
                    $entities = $entities->where('detail', 'like', '%'.$Text.'%');
                }
                if (!is_null($TaskName)) {
                    $entities = $entities->whereHas('taskInfo', function($query) use ($TaskName) {
                        $query->where('name', 'like', '%'.$TaskName.'%');
                    });
                }
                
                $entities = $entities->orderBy('id')->paginate(25);
                
                $entities->map(function($item) use ($contentLocale) {
                    // Update the cached detail property
                    $item->detail = $item->getTranslation('detail', $contentLocale);
                    return $item;
                });
                break;
            case 'actor_role':
                $entities = Role::query();
                
                // Apply filters
                if (!is_null($Code)) {
                    $entities = $entities->where('code', 'like', $Code.'%');
                }
                if (!is_null($Text)) {
                    $entities = $entities->where('name', 'like', '%'.$Text.'%');
                }
                
                $entities = $entities->orderBy('code')->paginate(25);
                
                $entities->map(function($item) use ($contentLocale) {
                    // Update the cached name property
                    $item->name = $item->getTranslation('name', $contentLocale);
                    return $item;
                });
                break;
            default:
                abort(404);
        }
        
        // Make sure pagination links maintain all parameters
        $entities->appends($request->input())->links();
        
        // Restore the UI locale after loading entities with content locale
        app()->setLocale($previousLocale);
        
        // Check if this is an AJAX request
        if ($request->ajax() || $request->wantsJson()) {
            return view('translation.list_tbody', compact(
                'type', 
                'entities'
            ));
        }
        
        return view('translation.list', compact(
            'type', 
            'entities', 
            'languages', 
            'selectedUiLocale', 
            'selectedContentLocale'
        ));
    }
    
    /**
     * Edit a specific entity's translations.
     */
    public function edit(Request $request, string $type, string $id)
    {
        Gate::authorize('admin');
        
        // Get list of available languages
        $languages = Actor::distinct('language')
            ->whereNotNull('language')
            ->pluck('language')
            ->toArray();
            
        // Filter out English variants and add a single 'en' entry
        $filteredLanguages = [];
        $hasEnglish = false;
        
        foreach ($languages as $language) {
            // Skip empty language entries
            if (empty($language)) {
                continue;
            }
            
            $baseLanguage = explode('_', $language)[0];
            
            if ($baseLanguage === 'en') {
                $hasEnglish = true;
            } else {
                $filteredLanguages[] = $language;
            }
        }
        
        // Add a single 'en' entry for all English variants
        if ($hasEnglish || in_array(config('app.locale'), ['en', 'en_GB', 'en_US'])) {
            $filteredLanguages[] = 'en';
        }
        
        // Make sure app locale is available
        if (!in_array(config('app.locale'), $filteredLanguages) && 
            explode('_', config('app.locale'))[0] !== 'en') {
            $filteredLanguages[] = config('app.locale');
        }
        
        // Ensure we have at least these standard languages
        $standardLanguages = ['en', 'fr', 'de', 'es'];
        foreach ($standardLanguages as $lang) {
            if (!in_array($lang, $filteredLanguages)) {
                $filteredLanguages[] = $lang;
            }
        }
        
        $languages = $filteredLanguages;
        
        // Get user's preferred language
        $locale = Auth::user()->getLanguage();
        // Extract the language part (without region) for UI translations
        $uiLanguage = explode('_', $locale)[0];
        // Set application locale to the language part for UI translations
        app()->setLocale($uiLanguage);
        // Store full locale for date/number formatting
        session(['formatting_locale' => $locale]);
        
        // Get the entity based on type and ID
        switch ($type) {
            case 'event_name':
                $entity = EventName::where('code', $id)->firstOrFail();
                $translations = $entity->translations()->get();
                $fields = ['name', 'notes'];
                break;
            case 'classifier_type':
                $entity = ClassifierType::where('code', $id)->firstOrFail();
                $translations = $entity->translations()->get();
                $fields = ['type', 'notes'];
                break;
            case 'matter_category':
                $entity = Category::where('code', $id)->firstOrFail();
                $translations = $entity->translations()->get();
                $fields = ['category'];
                break;
            case 'matter_type':
                $entity = MatterType::where('code', $id)->firstOrFail();
                $translations = $entity->translations()->get();
                $fields = ['type'];
                break;
            case 'task_rules':
                $entity = Rule::with('taskInfo')->findOrFail($id);
                $translations = $entity->translations()->get();
                $fields = ['detail', 'notes'];
                break;
            case 'actor_role':
                $entity = Role::where('code', $id)->firstOrFail();
                $translations = $entity->translations()->get();
                $fields = ['name', 'notes'];
                break;
            default:
                abort(404);
        }
        
        // Normalize the translations array, especially for English variants
        $normalizedTranslations = collect();
        foreach ($translations as $translation) {
            $locale = $translation->locale;
            $baseLanguage = explode('_', $locale)[0];
            
            // If it's an English variant, normalize to 'en'
            if ($baseLanguage === 'en') {
                $locale = 'en';
            }
            
            // Add to normalized collection
            $normalizedTranslations->put($locale, $translation);
        }
        
        $translations = $normalizedTranslations->keyBy('locale');
        
        // Both regular and AJAX requests should return the same view now
        // as we're always using show.blade.php which doesn't have layout elements
        return view('translation.show', compact('type', 'entity', 'translations', 'languages', 'fields'));
    }
    
    /**
     * Update the translations for a specific entity.
     */
    public function update(Request $request, string $type, string $id)
    {
        Gate::authorize('admin');
        
        // Validate the request
        $request->validate([
            'translations' => 'required|array',
        ]);
        
        // Get the entity based on type and ID
        switch ($type) {
            case 'event_name':
                $entity = EventName::where('code', $id)->firstOrFail();
                $foreignKey = 'code';
                $translationClass = EventNameTranslation::class;
                break;
            case 'classifier_type':
                $entity = ClassifierType::where('code', $id)->firstOrFail();
                $foreignKey = 'code';
                $translationClass = ClassifierTypeTranslation::class;
                break;
            case 'matter_category':
                $entity = Category::where('code', $id)->firstOrFail();
                $foreignKey = 'code';
                $translationClass = MatterCategoryTranslation::class;
                break;
            case 'matter_type':
                $entity = MatterType::where('code', $id)->firstOrFail();
                $foreignKey = 'code';
                $translationClass = MatterTypeTranslation::class;
                break;
            case 'task_rules':
                $entity = Rule::findOrFail($id);
                $foreignKey = 'task_rule_id';
                $translationClass = TaskRuleTranslation::class;
                break;
            case 'actor_role':
                $entity = Role::where('code', $id)->firstOrFail();
                $foreignKey = 'code';
                $translationClass = ActorRoleTranslation::class;
                break;
            default:
                abort(404);
        }
        
        // Update or create translations for each language
        foreach ($request->translations as $locale => $fields) {
            // Skip empty translations
            $hasContent = false;
            foreach ($fields as $value) {
                if (!empty($value)) {
                    $hasContent = true;
                    break;
                }
            }
            
            if (!$hasContent) {
                continue;
            }
            
            // Handle English variants - normalize to 'en'
            $normalizedLocale = $locale;
            $baseLanguage = explode('_', $locale)[0];
            if ($baseLanguage === 'en') {
                $normalizedLocale = 'en';
            }
            
            // Find existing translation
            $translation = $translationClass::where($foreignKey, $id)
                ->where(function($query) use ($normalizedLocale, $baseLanguage) {
                    // For English, find any English variant
                    if ($normalizedLocale === 'en') {
                        $query->where('locale', 'en')
                              ->orWhere('locale', 'en_GB')
                              ->orWhere('locale', 'en_US');
                    } else {
                        $query->where('locale', $normalizedLocale);
                    }
                })
                ->first();
                
            $data = [
                $foreignKey => $id,
                'locale' => $normalizedLocale,
                ...$fields
            ];
            
            if ($translation) {
                $translation->update($data);
            } else {
                $translationClass::create($data);
            }
        }
        
        // Check if the request is AJAX (XHR)
        if ($request->ajax() || $request->wantsJson()) {
            return $entity; // Just return the entity for successful updates, matching the app pattern
        }
        
        return redirect()->route('translations.list', $type)
            ->with('success', 'Translations updated successfully');
    }
}