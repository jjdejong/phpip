<?php

namespace App\Http\Controllers;

use App\Actor;
use App\MatterType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MatterTypeController extends Controller
{
    public function index(Request $request)
    {
        $Code = $request->input('Code');
        $Type = $request->input('Type');
        $type = MatterType::query();
        if (! is_null($Code)) {
            $type = $type->where('code', 'like', $Code.'%');
        }
        if (! is_null($Type)) {
            $type = $type->where('type', 'like', $Type.'%');
        }

        $matter_types = $type->get();

        return view('type.index', compact('matter_types'));
    }

    public function create()
    {
        $table = new Actor;
        $tableComments = $table->getTableComments('matter_type');

        return view('type.create', compact('tableComments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|unique:matter_type|max:5',
            'type' => 'required|max:45',
        ]);
        $request->merge(['creator' => Auth::user()->login]);

        return MatterType::create($request->except(['_token', '_method']));
    }

    public function show(MatterType $type)
    {
        App::setLocale(Auth::user()->language);
        $table = new Actor;
        $tableComments = $table->getTableComments('matter_type');

        return view('type.show', compact('type', 'tableComments'));
    }

    public function update(Request $request, MatterType $type)
    {
        $request->merge(['updater' => Auth::user()->login]);
        $type->update($request->except(['_token', '_method']));

        return $type;
    }

    public function destroy(MatterType $type)
    {
        $type->delete();

        return $type;
    }
}
