<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Drop the existing users view
        DB::statement('DROP VIEW IF EXISTS users');
        
        // Create the updated users view with the language field
        DB::statement("
            CREATE VIEW users AS 
            SELECT 
                actor.id,
                actor.name,
                actor.login,
                actor.language,
                actor.password,
                actor.default_role,
                actor.company_id,
                actor.email,
                actor.phone,
                actor.notes,
                actor.creator,
                actor.created_at,
                actor.updated_at,
                actor.updater,
                actor.remember_token
            FROM actor 
            WHERE actor.login IS NOT NULL
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the updated view
        DB::statement('DROP VIEW IF EXISTS users');
        
        // Recreate the original view without the language field
        DB::statement("
            CREATE VIEW users AS 
            SELECT 
                actor.id,
                actor.name,
                actor.login,
                actor.password,
                actor.default_role,
                actor.company_id,
                actor.email,
                actor.phone,
                actor.notes,
                actor.creator,
                actor.created_at,
                actor.updated_at,
                actor.updater,
                actor.remember_token
            FROM actor 
            WHERE actor.login IS NOT NULL
        ");
    }
};