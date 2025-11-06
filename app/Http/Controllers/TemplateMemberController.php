<?php

namespace App\Http\Controllers;

use App\Models\TemplateMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Manages template members (individual document templates).
 *
 * Template members are specific document instances within a template class,
 * containing Blade template markup for generating correspondence in different
 * languages and formats.
 */
class TemplateMemberController extends Controller
{
    /**
     * Supported template languages.
     *
     * @var array
     */
    public $languages = ['fr' => 'Français',
        'en' => 'English',
        'de' => 'Deutsch'];

    /**
     * Display a paginated list of template members with filtering.
     *
     * @param Request $request Filter parameters
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
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

        $query = $template_members->orderBy('summary');

        if ($request->wantsJson()) {
            return response()->json($query->get());
        }

        $template_members = $query->simplePaginate(config('renewal.general.paginate') == 0 ? 25 : intval(config('renewal.general.paginate')));
        $template_members->appends($request->input())->links();

        return view('template-members.index', compact('template_members'));
    }

    /**
     * Show the form for creating a new template member.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $table = new TemplateMember;
        $tableComments = $table->getTableComments();
        $languages = $this->languages;

        return view('template-members.create', compact('tableComments', 'languages'));
    }

    /**
     * Store a newly created template member.
     *
     * @param Request $request Template member data including class_id and language
     * @return TemplateMember The created template member
     */
    public function store(Request $request)
    {
        $request->validate([
            'class_id' => 'required',
            'language' => 'required',
        ]);
        $request->merge(['creator' => Auth::user()->login]);
        $a = TemplateMember::create($request->except(['_token', '_method']));

        return $a;
    }

    /**
     * Display the specified template member.
     *
     * @param TemplateMember $templateMember The template member to display
     * @return \Illuminate\Http\Response
     */
    public function show(TemplateMember $templateMember)
    {
        $tableComments = $templateMember->getTableComments();
        $templateMember->with(['class', 'style', 'language']);
        $languages = $this->languages;

        return view('template-members.show', compact('templateMember', 'languages', 'tableComments'));
    }

    /**
     * Show the form for editing the specified template member.
     *
     * @param TemplateMember $templateMember The template member to edit
     * @return void
     */
    public function edit(TemplateMember $templateMember)
    {
        //
    }

    /**
     * Update the specified template member.
     *
     * @param Request $request Updated template member data
     * @param TemplateMember $templateMember The template member to update
     * @return TemplateMember The updated template member
     */
    public function update(Request $request, TemplateMember $templateMember)
    {
        $request->merge(['updater' => Auth::user()->login]);
        $templateMember->update($request->except(['_token', '_method']));

        return $templateMember;
    }

    /**
     * Remove the specified template member from storage.
     *
     * @param TemplateMember $templateMember The template member to delete
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(TemplateMember $templateMember)
    {
        $templateMember->delete();

        return response()->json(['success' => 'Template deleted']);
    }
}
