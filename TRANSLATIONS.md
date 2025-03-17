# Using the Translation System

## Setup Instructions

1. **Run the migration to create translation tables**:
   ```
   php artisan migrate
   ```
   This will:
   - Create translation tables for each entity type
   - Update the `users` view to include the `language` field from the `actor` table
   - Modify the `actor.language` column from CHAR(2) to CHAR(5) to accommodate longer locale codes like 'en_GB'

2. **Seed initial translations** (optional):
   ```
   php artisan db:seed --class=TranslationsSeeder
   ```
   This will create translations for all supported locales (en, fr, de, es) using the original text.

## User Language Preference

- Users can set their preferred language in their user profile.
- The language options include:
  - **English (British)** - `en_GB`: English UI with European date format (DD/MM/YYYY)
  - **English (American)** - `en_US`: English UI with American date format (MM/DD/YYYY)
  - **Français** - `fr`: French UI and European date format
  - **Deutsch** - `de`: German UI and European date format
  - **Español** - `es`: Spanish UI and European date format

- The system separates:
  - The UI language (using the language part, e.g., "en" from "en_GB")
  - The date/number formatting (using the full locale, e.g., "en_GB") 
  
- If no language preference is set, the app will fall back to the default locale set in `config/app.php`.

## Managing Translations

1. **Access the Translation Management System**:
   - Navigate to `/translations` in your browser.
   - Only users with admin privileges can access this.

2. **Select an Entity Type**:
   - Choose which type of data you want to translate (event_name, classifier_type, etc.)

3. **Browse Entities**:
   - View the list of entities for the selected type
   - You can switch the display language using the dropdown

4. **Edit Translations**:
   - Click "Edit Translations" for an entity
   - Add or modify translations for each supported language
   - Save changes

## Adding Support for New Languages

1. **Add new translations via the UI**:
   - Simply enter translations for the new language in the translation editor

2. **Programmatically add a new language**:
   ```
   php artisan translations:seed new_locale_code
   ```
   This will create translation entries for the specified locale.

## Implementation Details

- The translation system uses Laravel's locale setting which is automatically set based on the user's language preference.
- Model attributes are transformed through accessors that check for translations in the user's preferred language.
- If no translation exists, the original text is displayed.

## Technical Notes

- The implementation uses a database-driven translation approach rather than Laravel's file-based translations.
- Models with translations use the `HasTranslations` trait to provide translation functionality.
- Each entity that needs translation has a corresponding translation table and model.
- When accessing the original text values in code, always use `$model->getRawOriginal('field')` rather than `$model->getOriginal('field')` to bypass the accessor which would try to translate the value.