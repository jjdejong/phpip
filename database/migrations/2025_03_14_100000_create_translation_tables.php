<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Event Name Translations
        Schema::create('event_name_translations', function (Blueprint $table) {
            $table->id();
            $table->string('code', 5);
            $table->string('locale', 5);
            $table->string('name', 45);
            $table->string('notes', 160)->nullable();
            $table->timestamps();
            
            $table->foreign('code')->references('code')->on('event_name')->onDelete('cascade');
            $table->unique(['code', 'locale']);
        });
        
        // Classifier Type Translations
        Schema::create('classifier_type_translations', function (Blueprint $table) {
            $table->id();
            $table->string('code', 5);
            $table->string('locale', 5);
            $table->string('type', 45);
            $table->string('notes', 160)->nullable();
            $table->timestamps();
            
            $table->foreign('code')->references('code')->on('classifier_type')->onDelete('cascade');
            $table->unique(['code', 'locale']);
        });
        
        // Matter Category Translations
        Schema::create('matter_category_translations', function (Blueprint $table) {
            $table->id();
            $table->string('code', 5);
            $table->string('locale', 5);
            $table->string('category', 45);
            $table->timestamps();
            
            $table->foreign('code')->references('code')->on('matter_category')->onDelete('cascade');
            $table->unique(['code', 'locale']);
        });
        
        // Matter Type Translations
        Schema::create('matter_type_translations', function (Blueprint $table) {
            $table->id();
            $table->string('code', 5);
            $table->string('locale', 5);
            $table->string('type', 45);
            $table->timestamps();
            
            $table->foreign('code')->references('code')->on('matter_type')->onDelete('cascade');
            $table->unique(['code', 'locale']);
        });
        
        // Task Rules Translations
        Schema::create('task_rules_translations', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('task_rule_id');
            $table->string('locale', 5);
            $table->string('detail', 45)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->foreign('task_rule_id')->references('id')->on('task_rules')->onDelete('cascade');
            $table->unique(['task_rule_id', 'locale']);
        });
        
        // Actor Role Translations
        Schema::create('actor_role_translations', function (Blueprint $table) {
            $table->id();
            $table->string('code', 5);
            $table->string('locale', 5);
            $table->string('name', 45);
            $table->string('notes', 160)->nullable();
            $table->timestamps();
            
            $table->foreign('code')->references('code')->on('actor_role')->onDelete('cascade');
            $table->unique(['code', 'locale']);
        });
        
        // Add language column to actor table if it doesn't exist
        if (!Schema::hasColumn('actor', 'language')) {
            Schema::table('actor', function (Blueprint $table) {
                $table->string('language', 5)->default('en')->after('nationality');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_name_translations');
        Schema::dropIfExists('classifier_type_translations');
        Schema::dropIfExists('matter_category_translations');
        Schema::dropIfExists('matter_type_translations');
        Schema::dropIfExists('task_rules_translations');
        Schema::dropIfExists('actor_role_translations');
        
        if (Schema::hasColumn('actor', 'language')) {
            Schema::table('actor', function (Blueprint $table) {
                $table->dropColumn('language');
            });
        }
    }
};