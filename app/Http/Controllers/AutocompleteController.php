<?php

namespace App\Http\Controllers;

use App\Models\Actor;
use App\Models\Category;
use App\Models\ClassifierType;
use App\Models\Country;
use App\Models\EventName;
use App\Models\Matter;
use App\Models\Role;
use App\Models\TemplateClass;
use App\Models\TemplateMember;
use App\Models\MatterType;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;

class AutocompleteController extends Controller
{
    public function matter(Request $request): JsonResponse
    {
        $results = Matter::with('filing')
            ->select('id as key', 'uid as value')
            ->whereLike('uid', "{$request->term}%")
            ->take(15)
            ->get()
            ->toArray();
            
        return response()->json($results);
    }

    public function newCaseref(Request $request): JsonResponse
    {
        $newref = Matter::whereLike('caseref', "{$request->term}%")
            ->max('caseref');

        $newref = $newref && $newref != $request->term
            ? ++$newref
            : strtoupper($request->term);

        return response()->json([['key' => $newref, 'value' => $newref]]);
    }

    public function eventName(Request $request, $is_task): JsonResponse
    {
        $query = EventName::whereJsonLike('name', $request->term)
            ->orWhereLike('code', "{$request->term}%")
            ->where('is_task', $is_task);

        if ($request->filled('category')) {
            $query->where(function($q) use ($request) {
                $q->whereNull('category')
                  ->orWhere('category', $request->category);
            });
        }

        $eventNames = $query->take(10)->get();
        $results = $eventNames->map(function ($item) {
            return [
                'key' => $item->code,
                'value' => $item->name
            ];
        })->toArray();

        return $this->formatResponse($results);
    }

    public function classifierType(Request $request, $main_display): JsonResponse
    {
        $query = ClassifierType::whereJsonLike('type', $request->term)
            ->where('main_display', $main_display)
            ->orderBy('type');

        $types = $query->take(10)->get();
        $results = $types->map(function ($item) {
            return [
                'key' => $item->code,
                'value' => $item->type
            ];
        })->toArray();

        return $this->formatResponse($results);
    }

    public function user(Request $request): JsonResponse
    {
        $results = User::select('name as value', 'login as key')
            ->whereLike('name', "{$request->term}%")
            ->orWhereLike('login', "{$request->term}%")
            ->take(10)
            ->get();

        return $this->formatResponse($results);
    }

    public function actor(Request $request, $create_option = null): JsonResponse
    {
        $list = Actor::select(DB::raw('coalesce(display_name, name) as value'), 'id as key')
            ->whereLike('name', "{$request->term}%")
            ->orWhereLike('display_name', "{$request->term}%")
            ->take(10)
            ->get();

        if ($list->count() < 5 && $create_option) {
            $list->push(['label' => "Create {$request->term}?", 'key' => 'create', 'value' => $request->term]);
        }

        return $this->formatResponse($list);
    }

    public function role(Request $request): JsonResponse
    {
        $query = Role::whereJsonLike('name', $request->term)
            ->orWhereLike('code', "{$request->term}%");

        $roles = $query->take(10)->get();
        $results = $roles->map(function ($item) {
            return [
                'key' => $item->code,
                'value' => $item->name,
                'shareable' => $item->shareable
            ];
        })->toArray();

        return $this->formatResponse($results);
    }

    public function dbrole(Request $request): JsonResponse
    {
        $query = Role::whereJsonLike('name', $request->term)
            ->whereIn('code', ['CLI', 'DBA', 'DBRW', 'DBRO']);

        $results = $query->get()->map(function ($item) {
            return [
                'key' => $item->code,
                'value' => $item->name
            ];
        })->toArray();

        return $this->formatResponse($results);
    }

    public function country(Request $request): JsonResponse
    {
        $results = Country::select('name as value', 'iso as key')
            ->whereLike('name', "{$request->term}%")
            ->orWhereLike('iso', "{$request->term}%")
            ->get();

        return $this->formatResponse($results);
    }

    public function category(Request $request): JsonResponse
    {
        $query = Category::whereJsonLike('category', $request->term)
            ->orWhereLike('code', "{$request->term}%");

        $categories = $query->take(10)->get();
        $results = $categories->map(function ($item) {
            return [
                'key' => $item->code,
                'value' => $item->category,
                'prefix' => $item->ref_prefix
            ];
        })->toArray();

        return $this->formatResponse($results);
    }

    public function type(Request $request): JsonResponse
    {
        $query = MatterType::whereJsonLike('type', $request->term);

        $types = $query->take(10)->get();
        $results = $types->map(function ($item) {
            return [
                'key' => $item->code,
                'value' => $item->type
            ];
        })->toArray();

        return $this->formatResponse($results);
    }

    public function templateCategory(Request $request): JsonResponse
    {
        $list = TemplateMember::select('category as value', 'category as key')
            ->whereLike('category', "{$request->term}%")
            ->distinct()
            ->get();

        if ($list->count() == 0) {
            $list->push(['label' => "Create {$request->term}", 'key' => $request->term, 'value' => $request->term]);
        }

        return $this->formatResponse($list);
    }

    public function templateClass(Request $request): JsonResponse
    {
        $results = TemplateClass::select('name as value', 'id as key')
            ->whereLike('name', "{$request->term}%")
            ->get();

        return $this->formatResponse($results);
    }

    public function templateStyle(Request $request): JsonResponse
    {
        $list = TemplateMember::select('style as value', 'style as key')
            ->whereLike('style', "{$request->term}%")
            ->distinct()
            ->get();

        if ($list->count() == 0) {
            $list->push(['label' => "Create {$request->term}", 'key' => $request->term, 'value' => $request->term]);
        }

        return $this->formatResponse($list);
    }

    protected function formatResponse($data): JsonResponse
    {
        // Ensure we're always returning an array, even for empty results
        return response()->json($data instanceof \Illuminate\Database\Eloquent\Collection ? $data->toArray() : (array) $data);
    }
}
