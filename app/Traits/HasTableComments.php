<?php

namespace App\Traits;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

trait HasTableComments
{
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