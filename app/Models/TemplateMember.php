<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasTableComments;

/**
 * TemplateMember Model
 *
 * Represents individual document templates that can be used to generate
 * correspondence, forms, reports, and other documents. Each template is
 * a member of a template class.
 *
 * Database table: template_members
 *
 * Key relationships:
 * - Belongs to a template class
 *
 * Business logic:
 * - Templates are stored as ODT or DOCX files
 * - Templates use merge fields (placeholders) that are replaced with matter data
 * - Templates can be used for letters, forms, official documents, reports, etc.
 * - Template selection is context-aware based on event type, task type, or rule
 * - Templates support mail merge functionality for bulk document generation
 */
class TemplateMember extends Model
{
    use HasTableComments;

    /**
     * Attributes that are not mass assignable.
     *
     * @var array<string>
     */
    protected $guarded = ['created_at', 'updated_at'];

    /**
     * Get the template class this template belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function class()
    {
        return $this->belongsTo(TemplateClass::class);
    }
}
