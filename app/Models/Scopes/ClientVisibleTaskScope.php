<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

/**
 * Hides internal tasks from client users.
 *
 * Applied to every Task query, so the dashboard lists and counts, the matter
 * page, its task and renewal views, matter info and route-bound /task/{task}
 * all show a client only the codes in config('client.visible_task_codes').
 * Staff and unauthenticated contexts (console commands, queues) see everything.
 *
 * Only Eloquent queries on Task are covered; raw queries on the task table
 * must apply the same restriction themselves if a client can reach them.
 */
class ClientVisibleTaskScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        $user = Auth::user();

        if ($user && $user->isClient()) {
            $builder->whereIn($model->qualifyColumn('code'), config('client.visible_task_codes', []));
        }
    }
}
