<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\TemplateMember;
use App\Actor;
use App\Matter;
use App\MatterActors;
use App\TemplateClass;
use App\Event;
use App\Task;
use Log;
use DB;
use Symfony\Component\Debug\Exception\FatalThrowableError;
use Illuminate\Support\Facades\Blade;
use LaravelGettext;

  function render($__php, $__data)
  {
      $obLevel = ob_get_level();
      ob_start();
      $__data['__env'] = app(\Illuminate\View\Factory::class);
      extract($__data, EXTR_SKIP);
      try {
          eval('?' . '>' . $__php);
      } catch (Exception $e) {
          while (ob_get_level() > $obLevel) ob_end_clean();
          throw $e;
      } catch (Throwable $e) {
          while (ob_get_level() > $obLevel) ob_end_clean();
          throw new FatalThrowableError($e);
      }
      return ob_get_clean();
  }

class DocumentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        LaravelGettext::setLocale(Auth::user()->language);
        $Notes  = $request->input('Notes');
        $Name = $request->input('Name');
        $template_classes = TemplateClass::query() ;
        if (!is_null($Name)) {
            $template_classes = $template_classes->where('name', 'like', $Name.'%');
        }
        if (!is_null($Notes)) {
            $template_classes = $template_classes->where('notes', 'like', $Notes.'%');
        }

        $template_classes = $template_classes->orderby('name')->simplePaginate( config('renewal.general.paginate') == 0 ? 25 : intval(config('renewal.general.paginate')) );
        $template_classes->appends($request->input())->links();
        return view('documents.index', compact('template_classes'));
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        LaravelGettext::setLocale(Auth::user()->language);
        $table = new Actor ;
        $tableComments = $table->getTableComments('template_classes');
        return view('documents.create', compact('tableComments'));
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
            'name' => 'required|max:55',
        ]);
        $request->merge([ 'creator' => Auth::user()->login ]);
        return TemplateClass::create($request->except(['_token', '_method']));
    }

    /**
     * Display the specified resource.
     *
     * @param  TemplateClass $class
     * @return \Illuminate\Http\Response
     */
    public function show(TemplateClass $class)
    {
        LaravelGettext::setLocale(Auth::user()->language);
        $table = new Actor;
        $tableComments = $table->getTableComments('template_classes');
        $class->with(['role']);
        return view('documents.show', compact('class', 'tableComments'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\TemplateClass $class
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TemplateClass $class)
    {
        $request->merge([ 'updater' => Auth::user()->login ]);
        $class->update($request->except(['_token', '_method']));
        return response()->json(['success' => 'Template class updated']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int  Role $role
     * @return \Illuminate\Http\Response
     */
    public function destroy(TemplateClass $class)
    {
        $class->delete();
        return response()->json(['success' => 'Template class deleted']);
    }

        /**
         * Return view to select a template.
         *
         * @param  \Illuminate\Http\Request  $request
         * @param  \App\Matter $matter
         * @return \Illuminate\Http\Response
         */

  public function select(Matter $matter, Request $request)
    {
        LaravelGettext::setLocale(Auth::user()->language);
    $template_id = $request->input('template_id');
    //limit to actors with email
    $contacts = MatterActors::where([['matter_id',$matter->id],['role_code','CNT']])->whereNotNull('email');
    if($contacts->count() === 0) {
      $contacts =  MatterActors::where([['matter_id',$matter->id]])->whereNotNull('email');
    }
    $contacts = $contacts->get();
    $table = new Actor;
    //TODO getTableComments is the same as in Rule.php. To render common
    $tableComments = $table->getTableComments('template_members');
    $filters =  $request->except(['page']);
    $members = new TemplateMember;
    $oldfilters = [];
    $view = 'documents.select';
    $event = null;
    $task = null;
    if (!empty($filters)) {
        foreach ($filters as $key => $value) {
            if ($value != '') {
                switch($key) {
                    case 'Category':
                        $members = $members->where('category', 'LIKE', "$value%");
                        $oldfilters["Category"] = $value;
                        break;
                    case 'Language':
                        $members = $members->where('language', 'LIKE', "$value%");
                        $oldfilters["Language"] = $value;
                        break;
                    case 'Name':
                        $members = $members->whereHas('class', function ($query) use ($value){
                          $query->where('name', 'LIKE', "$value%");
                        });
                        $oldfilters["Name"] = $value;
                        break;
                    case 'Summary':
                        $members = $members->where('summary', 'LIKE', "$value%");
                        $oldfilters["Name"] = $value;
                        break;
                    case 'Style':
                        $members = $members->where('style', 'LIKE', "$value%");
                        $oldfilters["Style"] = $value;
                        break;
                    case 'EventName':
                        $members = $members->whereHas('class', function ($query) use ($value){
                          $query->whereHas('eventNames',  function ($q2) use ($value){
                            $q2->where('event_name_code', '=', "$value");
                          });
                        });
                        $oldfilters["EventName"] = $value;
                        // specific view for within event window
                        $view = 'documents.select2';
                        break;
                    case 'Event':
                        $event = Event::where('id','=', "$value")->first();
                        break;
                    case 'Task':
                        $task = Task::where('id','=', "$value")->first();
                        $event = $task->trigger;
                        break;
                }
            }
        }
    }
    if ($view == 'documents.select') {
      //  We exclude members linked to any of event or task
          $members = $members->whereHas('class', function ($query) {
            $query->whereNotExists(function($query)
                {
                    $query->select(DB::raw(1))
                          ->from('event_class_lnk')
                          ->whereRaw('template_classes.id = event_class_lnk.template_class_id');
                });
          });
    }
    $members = $members->get();
    return view($view,compact('matter','members', 'contacts', 'tableComments','oldfilters','event','task'));
  }

  /*
    Prepare a mailto: href with template and data from the matter
    *
    * @param  \Illuminate\Http\Request  $request
    * @param  \App\TemplateMember $member
    * @return \Illuminate\Http\Response
  */
    public function mailto(TemplateMember $member, Request $request)
    {
        LaravelGettext::setLocale(Auth::user()->language);
      // Todo Add field for maually add an address
      $data =  array();
      $subject = Blade::compileString($member->subject);
      $blade = Blade::compileString($member->body);

      // Get contacts list
      $sendto_ids = array();
      $cc_ids = array();
      foreach($request->except(['page']) as $attribute => $value) {
        if (strpos($attribute, 'sendto') === 0) {
          $sendto_ids[] = substr($attribute, 7);
        }
        if (strpos($attribute, 'ccto') === 0 ) {
          $cc_ids[] = substr($attribute, 5);
        }
      }
      if (count($sendto_ids) != 0) {
        $mailto = "mailto:" . implode(',', Actor::whereIn('id', $sendto_ids)->pluck('email')->all());
        $sep = "?";
        $matter = Matter::where(['id'=>$request->matter_id])->first();
        $event = Event::where(['id'=>$request->event_id])->first();
        $task = Task::where(['id'=>$request->task_id])->first();
        $description = implode("\n",Matter::getDescription($request->matter_id, $member->language));
        if (count($cc_ids) != 0) {
            $mailto .= $sep . "cc=" . implode(',', Actor::whereIn('id', $cc_ids)->pluck('email')->all());
            $sep = "&";
        }
        if ($member->subject != "") {
          $content = render($subject,compact('description', 'matter','event','task'));
          if (is_array($content)) {
            if (array_key_exists('error', $content))
              return $content;
          } else {
            $mailto .= $sep."subject=".rawurlencode($content);
            $sep = "&";
          }
        }
        $content = render($blade,compact('description', 'matter','event','task'));
        if (is_array($content)) {
          if (array_key_exists('error', $content))
            return $content;
        } else {
          if ($member->format == 'HTML') {
              $mailto .= $sep . "html-body=" . rawurlencode($content);
          } else {
            $mailto .= $sep . "body=" . rawurlencode($content);
          }
          return json_encode(['mailto' => $mailto]);
        }
      }
      else {
        return json_encode(['message' => _i("You need to select at least one contact.")]);
      }
    }
}
