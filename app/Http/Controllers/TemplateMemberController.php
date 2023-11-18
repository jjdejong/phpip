<?php

namespace App\Http\Controllers;

use App\Actor;
use App\TemplateMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TemplateMemberController extends Controller
{
    public $languages = ['fr' => 'Français',
        'en' => 'English',
        'de' => 'Deutsch'];

    public function index(Request $request)
    {
        $Summary = $request->summary;
        $Style = $request->style;
        $Language = $request->language;
        $Class = $request->class;
        $Format = $request->format;
        $Category = $request->category;
        $template_members = TemplateMember::query();
        if (! is_null($Summary)) {
            $template_members = $template_members->where('summary', 'LIKE', "%$Summary%");
        }
        if (! is_null($Category)) {
            $template_members = $template_members->where('category', 'LIKE', "$Category%");
        }
        if (! is_null($Language)) {
            $template_members = $template_members->where('language', 'LIKE', "$Language%");
        }
        if (! is_null($Class)) {
            $template_members = $template_members->whereHas('class', function ($query) use ($Class) {
                $query->where('name', 'LIKE', "$Class%");
            });
        }
        if (! is_null($Format)) {
            $template_members = $template_members->where('format', 'like', $Format.'%');
        }
        if (! is_null($Style)) {
            $template_members = $template_members->where('style', 'LIKE', "$Style%");
        }

        $template_members = $template_members->orderBy('summary')->simplePaginate(config('renewal.general.paginate') == 0 ? 25 : intval(config('renewal.general.paginate')));
        $template_members->appends($request->input())->links();

        return view('template-members.index', compact('template_members'));
    }

    public function create()
    {
        $table = new Actor;
        $tableComments = $table->getTableComments('template_members');
        $languages = $this->languages;

        return view('template-members.create', compact('tableComments', 'languages'));
    }

    public function store(Request $request)
    {
        LaravelGettext::setLocale(Auth::user()->language);
        $request->validate([
            'class_id' => 'required',
            'language' => 'required',
        ]);
        $request->merge(['creator' => Auth::user()->login]);
        $a = TemplateMember::create($request->except(['_token', '_method']));

        return $a;
    }

    public function show(TemplateMember $templateMember)
    {
        LaravelGettext::setLocale(Auth::user()->language);
        $table = new Actor;
        $tableComments = $table->getTableComments('template_members');
        $templateMember->with(['class', 'style', 'language']);
        $languages = $this->languages;

        return view('template-members.show', compact('templateMember', 'languages', 'tableComments'));
    }

    public function edit(TemplateMember $templateMember)
    {
        //
    }

    public function update(Request $request, TemplateMember $templateMember)
    {
        $request->merge(['updater' => Auth::user()->login]);
        $templateMember->update($request->except(['_token', '_method']));

        return $templateMember;
    }

    public function destroy(TemplateMember $templateMember)
    {
        $templateMember->delete();

        return response()->json(['success' => 'Template deleted']);
    }
}
