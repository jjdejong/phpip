<?php

namespace App\Http\Controllers;

use App\Dactor;
use App\Actor;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Response;

class DactorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $Actor  = $request->input('Actor');
        $Role = $request->input('Role');
        $Country = $request->input('Country');
        $Category = $request->input('Category');
        $Client = $request->input('Client');
        $dactor = new Dactor;

        if (! is_null($Actor)) {
            $dactor = $dactor->whereHas('actor', function ($q) use ($Actor) {
                $q->where('name', 'like', $Actor.'%');
            });
        }
        if (! is_null($Role)) {
            $dactor = $dactor->whereHas('roleInfo', function ($q) use ($Role) {
                $q->where('name', 'like', $Role.'%');
            });
        }
        if (! is_null($Country)) {
            $dactor = $dactor->whereHas('country', function ($q) use ($Country) {
                $q->where('name', 'like', $Country.'%');
            });
        }
        if (! is_null($Category)) {
            $dactor = $dactor->whereHas('category', function ($q) use ($Category) {
                $q->where('category', 'like', $Category.'%');
            });
        }
        if (! is_null($Client)) {
            $dactor = $dactor->whereHas('client', function ($q) use ($Client) {
                $q->where('name', 'like', $Client.'%');
            });
        }
        $dactors = $dactor->with(['roleInfo:code,name','actor:id,name','client:id,name','category:code,category','country:iso,name'])->get();
        return view('dactor.index', compact('dactors'));
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $table = new Actor ;
        $tableComments = $table->getTableComments('default_actor');
        return view('dactor.create', compact('tableComments'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'actor_id' => 'required',
            'role' => 'required'
        ]);
        return Dactor::create($request->except(['_token', '_method']));
    }

    /**
     * Display the specified resource.
     *
     * @param  Role $dactor
     * @return \Illuminate\Http\Response
     */
    public function show(Dactor $dactor)
    {
        $table = new Actor;
        $tableComments = $table->getTableComments('default_actor');
        $dactor->with(['roleInfo:code,name','actor:id,name','client:id,name','category:code,category','country:iso,name'])->get();
        return view('dactor.show', compact('dactor', 'tableComments'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string $code
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Dactor $dactor)
    { 
        if($request->has('actor_id') && is_null($request->actor_id)) {
            return response(json_encode(['message' => "The given data was invalid",
                                                                'errors' =>['actor_id' => ['The actor is required']]]), 422);
        }
        if($request->has('role') && is_null($request->role)) {
            return response(json_encode(['message' => "The given data was invalid",
                                                                'errors' =>['role' => ['The role is required']]]), 422);
        }
        $request->merge([ 'updater' => Auth::user()->login ]);
        $dactor->update($request->except(['_token', '_method']));
        return response()->json(['success' => 'Entry updated']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int  Dactor $dactor
     * @return \Illuminate\Http\Response
     */
    public function destroy(Dactor $dactor)
    {
        $dactor->delete();
        return response()->json(['success' => 'Entry deleted']);
    }
}
