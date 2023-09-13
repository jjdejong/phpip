<?php

namespace App\Http\Controllers;

use App\TemplateMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Actor;

class TemplateMemberController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public $languages = ['fr' => 'Français',
                              'en' => 'English',
                              'de' => 'Deutsch'];

    public function index(Request $request)
    {
        $Summary  = $request->summary;
        $Style  = $request->style;
        $Language  = $request->language;
        $Class = $request->class;
        $Format = $request->format;
        $Category = $request->category;
        $template_members = TemplateMember::query() ;
        if (!is_null($Summary)) {
            $template_members = $template_members->where('summary', 'LIKE', "%$Summary%");
        }
        if (!is_null($Category)) {
            $template_members = $template_members->where('category', 'LIKE', "$Category%");
        }
        if (!is_null($Language)) {
            $template_members = $template_members->where('language', 'LIKE', "$Language%");
        }
        if (!is_null($Class)) {
            $template_members = $template_members->whereHas('class', function ($query) use ($Class) {
                $query->where('name', 'LIKE', "$Class%");
            });
        }
        if (!is_null($Format)) {
                $template_members = $template_members->where('format', 'like', $Format.'%');
        }
        if (!is_null($Style)) {
            $template_members = $template_members->where('style', 'LIKE', "$Style%");
        }

        $template_members = $template_members->orderBy('summary')->simplePaginate(config('renewal.general.paginate') == 0 ? 25 : intval(config('renewal.general.paginate')));
        $template_members->appends($request->input())->links();
        return view('template-members.index', compact('template_members'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $table = new Actor ;
        $tableComments = $table->getTableComments('template_members');
        $languages = $this->languages;
        return view('template-members.create', compact('tableComments', 'languages'));
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
            'class_id' => 'required',
            'language' => 'required'
        ]);
        $request->merge([ 'creator' => Auth::user()->login ]);
        $a = TemplateMember::create($request->except(['_token', '_method']));
        return $a;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\TemplateMember  $templateMember
     * @return \Illuminate\Http\Response
     */
    public function show(TemplateMember $templateMember)
    {
        $table = new Actor;
        $tableComments = $table->getTableComments('template_members');
        $templateMember->with(['class','style','language']);
        $languages = $this->languages;
        return view('template-members.show', compact('templateMember', 'languages', 'tableComments'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\TemplateMember  $templateMember
     * @return \Illuminate\Http\Response
     */
    public function edit(TemplateMember $templateMember)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\TemplateMember  $templateMember
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TemplateMember $templateMember)
    {
        $request->merge([ 'updater' => Auth::user()->login ]);
        $templateMember->update($request->except(['_token', '_method']));
        return $templateMember;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\TemplateMember  $templateMember
     * @return \Illuminate\Http\Response
     */
    public function destroy(TemplateMember $templateMember)
    {
        $templateMember->delete();
        return response()->json(['success' => 'Template deleted']);
    }
}
