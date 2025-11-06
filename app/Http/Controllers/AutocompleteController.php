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

/**
 * Provides autocomplete endpoints for UI form fields.
 *
 * Returns formatted JSON responses for typeahead/autocomplete widgets across the
 * application. All methods return arrays of {key, value} objects, some with additional
 * metadata fields.
 */
class AutocompleteController extends Controller
{
    /**
     * Autocomplete matters by UID.
     *
     * @param Request $request Contains 'term' parameter
     * @return JsonResponse Array of {key: id, value: uid} objects
     */
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

    /**
     * Suggest next available case reference based on prefix.
     *
     * @param Request $request Contains 'term' parameter (prefix)
     * @return JsonResponse Suggested next reference number
     */
    public function newCaseref(Request $request): JsonResponse
    {
        $newref = Matter::whereLike('caseref', "{$request->term}%")
            ->max('caseref');

        $newref = $newref && $newref != $request->term
            ? ++$newref
            : strtoupper($request->term);

        return response()->json([['key' => $newref, 'value' => $newref]]);
    }

    /**
     * Autocomplete event names filtered by is_task flag and optional category.
     *
     * @param Request $request Contains 'term' and optional 'category' parameters
     * @param int $is_task Whether to filter for task events (1) or regular events (0)
     * @return JsonResponse Array of {key: code, value: name} objects
     */
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

    /**
     * Autocomplete classifier types filtered by main_display flag.
     *
     * @param Request $request Contains 'term' parameter
     * @param int $main_display Filter by main_display flag
     * @return JsonResponse Array of {key: code, value: type} objects
     */
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

    /**
     * Autocomplete users by name or login.
     *
     * @param Request $request Contains 'term' parameter
     * @return JsonResponse Array of {key: login, value: name} objects
     */
    public function user(Request $request): JsonResponse
    {
        $results = User::select('name as value', 'login as key')
            ->whereLike('name', "{$request->term}%")
            ->orWhereLike('login', "{$request->term}%")
            ->take(10)
            ->get();

        return $this->formatResponse($results);
    }

    /**
     * Autocomplete actors with optional create suggestion.
     *
     * @param Request $request Contains 'term' parameter
     * @param string|null $create_option If set, adds create option when results are few
     * @return JsonResponse Array of {key: id, value: display_name/name} objects
     */
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

    /**
     * Autocomplete roles by name or code.
     *
     * @param Request $request Contains 'term' parameter
     * @return JsonResponse Array of {key: code, value: name, shareable} objects
     */
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

    /**
     * Autocomplete database roles (filtered to CLI, DBA, DBRW, DBRO).
     *
     * @param Request $request Contains 'term' parameter
     * @return JsonResponse Array of {key: code, value: name} objects
     */
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

    /**
     * Autocomplete countries by name or ISO code.
     *
     * @param Request $request Contains 'term' parameter
     * @return JsonResponse Array of {key: iso, value: name} objects
     */
    public function country(Request $request): JsonResponse
    {
        $query = Country::whereJsonLike('name', $request->term)
            ->orWhereLike('iso', "{$request->term}%");

        $countries = $query->take(10)->get();
        $results = $countries->map(function ($country) {
            return [
                'key' => $country->iso,
                'value' => $country->name
            ];
        })->toArray();

        return $this->formatResponse($results);
    }

    /**
     * Autocomplete categories by name or code.
     *
     * @param Request $request Contains 'term' parameter
     * @return JsonResponse Array of {key: code, value: category, prefix: ref_prefix} objects
     */
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

    /**
     * Autocomplete matter types.
     *
     * @param Request $request Contains 'term' parameter
     * @return JsonResponse Array of {key: code, value: type} objects
     */
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

    /**
     * Autocomplete template categories with create option.
     *
     * @param Request $request Contains 'term' parameter
     * @return JsonResponse Array of {key: category, value: category} objects
     */
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

    /**
     * Autocomplete template classes by name.
     *
     * @param Request $request Contains 'term' parameter
     * @return JsonResponse Array of {key: id, value: name} objects
     */
    public function templateClass(Request $request): JsonResponse
    {
        $results = TemplateClass::select('name as value', 'id as key')
            ->whereLike('name', "{$request->term}%")
            ->get();

        return $this->formatResponse($results);
    }

    /**
     * Autocomplete template styles with create option.
     *
     * @param Request $request Contains 'term' parameter
     * @return JsonResponse Array of {key: style, value: style} objects
     */
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

    /**
     * Format response data ensuring it's always an array.
     *
     * @param mixed $data Data to format
     * @return JsonResponse
     */
    protected function formatResponse($data): JsonResponse
    {
        // Ensure we're always returning an array, even for empty results
        return response()->json($data instanceof \Illuminate\Database\Eloquent\Collection ? $data->toArray() : (array) $data);
    }
}
