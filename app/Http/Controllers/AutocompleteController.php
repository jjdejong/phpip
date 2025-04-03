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
            ->where('uid', 'like', "{$request->term}%")
            ->take(15)
            ->get()
            ->toArray();
            
        return response()->json($results);
    }

    public function newCaseref(Request $request): JsonResponse
    {
        $newref = Matter::where('caseref', 'like', "{$request->term}%")
            ->max('caseref');

        $newref = $newref && $newref != $request->term
            ? ++$newref
            : strtoupper($request->term);

        return response()->json([['key' => $newref, 'value' => $newref]]);
    }

    public function eventName(Request $request, $is_task): JsonResponse
    {
        $query = EventName::select('name as value', 'code as key')
            ->where([
                ['name', 'like', "{$request->term}%"],
                ['is_task', $is_task],
            ]);

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        return $this->formatResponse($query->take(10)->get());
    }

    public function classifierType(Request $request, $main_display): JsonResponse
    {
        $results = ClassifierType::select('type as value', 'code as key')
            ->where('type', 'like', "{$request->term}%")
            ->where('main_display', $main_display)
            ->orderBy('type')
            ->take(10)
            ->get();

        return $this->formatResponse($results);
    }

    public function user(Request $request): JsonResponse
    {
        $results = User::select('name as value', 'login as key')
            ->where('name', 'like', "{$request->term}%")
            ->orWhere('login', 'like', "{$request->term}%")
            ->take(10)
            ->get();

        return $this->formatResponse($results);
    }

    public function actor(Request $request, $create_option = null): JsonResponse
    {
        $list = Actor::select(DB::raw('coalesce(display_name, name) as value'), 'id as key')
            ->where('name', 'like', "{$request->term}%")
            ->orWhere('display_name', 'like', "{$request->term}")
            ->take(10)
            ->get();

        if ($list->count() < 5 && $create_option) {
            $list->push(['label' => "Create {$request->term}?", 'key' => 'create', 'value' => $request->term]);
        }

        return $this->formatResponse($list);
    }

    public function role(Request $request): JsonResponse
    {
        $results = Role::select('name as value', 'code as key', 'shareable')
            ->where('name', 'like', "{$request->term}%")
            ->orWhere('code', 'like', "{$request->term}%")
            ->get();

        return $this->formatResponse($results);
    }

    public function dbrole(Request $request): JsonResponse
    {
        $results = Role::select('name as value', 'code as key')
            ->where('name', 'like', "{$request->term}%")
            ->whereIn('code', ['CLI', 'DBA', 'DBRW', 'DBRO'])
            ->get();

        return $this->formatResponse($results);
    }

    public function country(Request $request): JsonResponse
    {
        $results = Country::select('name as value', 'iso as key')
            ->where('name', 'like', "{$request->term}%")
            ->orWhere('iso', 'like', "{$request->term}%")
            ->get();

        return $this->formatResponse($results);
    }

    public function category(Request $request): JsonResponse
    {
        $results = Category::select('category as value', 'code as key', 'ref_prefix as prefix')
            ->where('category', 'like', "{$request->term}%")
            ->orWhere('code', 'like', "{$request->term}%")
            ->get();

        return $this->formatResponse($results);
    }

    public function type(Request $request): JsonResponse
    {
        $results = MatterType::select('type as value', 'code as key')
            ->where('type', 'like', "{$request->term}%")
            ->orWhere('code', 'like', "{$request->term}%")
            ->get();

        return $this->formatResponse($results);
    }

    public function templateCategory(Request $request): JsonResponse
    {
        $list = TemplateMember::select('category as value', 'category as key')
            ->where('category', 'like', "{$request->term}%")
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
            ->where('name', 'like', "{$request->term}%")
            ->get();

        return $this->formatResponse($results);
    }

    public function templateStyle(Request $request): JsonResponse
    {
        $list = TemplateMember::select('style as value', 'style as key')
            ->where('style', 'like', "{$request->term}%")
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
