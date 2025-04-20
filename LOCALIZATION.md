# Localization Guide

## Quick Start

### Setting User Language
Users can set their preferred language in their profile:
- English (British) - `en_GB` (date format DD/MM/YYYY)
- English (American) - `en_US` (date format MM/DD/YYYY)
- Français - `fr`
- Deutsch - `de`

### Translation Updates

When new translations are available after a phpIP update, run:

```bash
# Normal update - preserves your customizations
php artisan translations:refresh

# Complete reset to "official" translations
php artisan translations:refresh --force
```

### Adding your Custom Translations

1. **UI Elements**: Add translations to language files in `/lang/`:
   - `en.json` for English
   - `fr.json` for French
   - `de.json` for German

2. **Database Content**: Use the admin interface to add/modify translations for:
   - Matter categories
   - Event names
   - Actor roles
   - Task rules
   - etc.

### Managing Database Translations via Admin Interface

The admin interface displays and allows editing of database content (like Matter Categories, Event Names, etc.) based on your currently selected user language.

-   **Viewing:** Content is shown in your profile's language. If a translation doesn't exist for your language, it may fall back to a default language (e.g., English).
-   **Editing/Adding:** When you modify or add content through the admin interface, the changes are saved **only for the language currently set in your user profile**.

To add or edit translations for a different language:

1.  Go to your user profile settings.
2.  Change your language to the desired target language (e.g., change from English to French).
3.  Navigate back to the relevant admin section (e.g., Matter Categories).
4.  You will now see the content in the newly selected language.
5.  Make the necessary additions or modifications. These changes will be saved for that specific language.
6.  Repeat the process for any other languages you need to manage.

## Overview for developers

The application uses two translation systems:

1. **UI Translations**
   - Menu items, buttons, messages
   - Stored in JSON files
   - Easy to customize

2. **Core Data Translations**
   - Business data (categories, types, events, etc.)
   - Stored in database
   - Preserved during updates

## Tables with Translations

| Content Type     | Examples                    |
|-----------------|----------------------------|
| Matter Categories| Patent, Trademark, Design  |
| Event Names     | Filed, Published, Granted  |
| Actor Roles     | Owner, Agent, Inventor     |
| Task Rules      | Reminders, Actions         |

---

## Developer Documentation

<details>
<summary>Technical implementation details (click to expand)</summary>

### 1. UI Translation System

Uses Laravel's built-in localization:
```php
// In PHP files
echo __('Welcome to phpIP');

// In Blade templates
{{ __('Search Results') }}
```

File structure:
```
/lang/
  ├── en.json
  ├── fr.json
  └── de.json
```

### 2. Database Translation System

Uses JSON columns with spatie/laravel-translatable:

```json
{
    "en": "English text",
    "fr": "French text",
    "de": "German text"
}
```

#### Tables and Columns

| Table            | Column    | Usage                    |
|------------------|-----------|--------------------------|
| actor_role       | name      | Role names              |
| classifier_type  | type      | Classifier types        |
| event_name       | name      | Event names             |
| matter_category  | category  | Matter categories       |
| matter_type      | type      | Matter types           |
| task_rules      | detail    | Task rule details      |

#### Development Notes

1. **Querying Translated Content:**
```php
// Get translation in current locale
$category->category 

// Get specific translation
$category->getTranslation('category', 'fr')

// Check if translation exists
$category->hasTranslation('category', 'de')

// Filtering by translation (case sensitive)
$patents = MatterCategory::where('category->en', 'Patent')->get();
$brevets = MatterCategory::where('category->fr', 'Brevet')->get();

// Using raw SQL operator (case sensitive)
$patents = MatterCategory::whereRaw("category->>'$.en' = ?", ['Patent'])->get();

// Case-insensitive matching
$matters = MatterCategory::whereRaw(
    "category->>'$.en' COLLATE utf8mb4_0900_ai_ci LIKE ?", 
    ['patent%']
)->get();

// This can be simplified using the whereJsonLike() macro defined in AppServiceProvider (starts with 'patent'):
$matters = MatterCategory::whereJsonLike('category', 'patent')->get();
```

2. **Adding New Translations:**
```php
$category->setTranslation('category', 'en', 'Patent')
        ->setTranslation('category', 'fr', 'Brevet')
        ->save();
```

3. **Direct SQL Updates:**
```sql
UPDATE table_name 
SET column_name = JSON_SET(
    COALESCE(column_name, '{}'),
    '$.en', 'English text',
    '$.fr', 'French text',
    '$.de', 'German text'
)
WHERE id = X;
```

4. **Indexing:**
- Functional indexes for each locale
- Collation: utf8mb4_0900_ai_ci
- Format: `idx_table_column_locale`

5. **Fallback Behavior:**
- Missing translation → English
- Missing English → null
- Configure in `config/app.php`

#### Performance Considerations

1. **Indexes:**
- Use functional indexes on specific locales for faster lookups.
- Example query using functional index:
    ```php
    // Using JSON functions (requires specific index)
    ->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(category, '$.en')) = ?", ['Patent'])

    // Using Laravel's shorthand (may leverage index depending on DB/version)
    ->where('category->en', 'Patent')
    ```

2. **Caching:**
- Cache frequently accessed translations
- Use Laravel's cache system

3. **Bulk Operations:**
- Use `translations:refresh` for updates
- Batch process manual updates

</details>
