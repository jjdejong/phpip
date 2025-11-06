<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasTableComments;

/**
 * TemplateClass Model
 *
 * Represents document template categories or classes, used for organizing
 * document templates by type (letters, forms, reports, etc.).
 *
 * Database table: template_classes
 *
 * Key relationships:
 * - Has many template members (individual templates)
 * - Belongs to a default role (who can use these templates)
 * - Many-to-many with rules (templates for tasks from rules)
 * - Many-to-many with event names (templates for events)
 *
 * Business logic:
 * - Template classes organize templates into logical groups
 * - Can restrict template access by role
 * - Links templates to events and rules for automatic document generation
 * - Templates can be ODT (LibreOffice) or DOCX (Word) format
 */
class TemplateClass extends Model
{
    use HasTableComments;

    /**
     * Attributes that are not mass assignable.
     *
     * @var array<string>
     */
    protected $guarded = ['created_at', 'updated_at'];

    /**
     * Get the default role that can use templates in this class.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function role()
    {
        return $this->belongsTo(Role::class, 'default_role', 'code');
    }

    /**
     * Get the rules associated with this template class.
     *
     * Templates can be linked to rules for automatic document generation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function rules()
    {
        return $this->belongsToMany(Rule::class, 'rule_class_lnk', 'template_class_id', 'task_rule_id');
    }

    /**
     * Get the event names associated with this template class.
     *
     * Templates can be linked to event types for context-aware document generation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function eventNames()
    {
        return $this->belongsToMany(EventName::class, 'event_class_lnk', 'template_class_id', 'event_name_code');
    }
}
