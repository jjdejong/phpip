<?php

namespace App\Traits;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

/**
 * Trait for retrieving database table column comments.
 *
 * Provides functionality to fetch and cache column comments from the database schema,
 * useful for displaying field help text or generating form labels dynamically.
 */
trait HasTableComments
{
    /**
     * Get comments for all columns in the model's database table.
     *
     * Retrieves column comments from the database schema and caches them for 1 hour
     * to improve performance. The comments are returned as an associative array
     * with column names as keys.
     *
     * @return array Associative array of column names and their comments.
     */
    public function getTableComments()
    {
        return Cache::remember("table-comments-{$this->getTable()}", 3600, function () {
            $comments = [];
            foreach (Schema::getColumns($this->getTable()) as $column) {
                $comments[$column['name']] = $column['comment'];
            }
            return $comments;
        });
    }
}