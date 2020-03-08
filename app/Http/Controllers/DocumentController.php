<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\TemplateMember;
use App\Actor;
use App\Matter;
use App\MatterActors;
use Log;
use Symfony\Component\Debug\Exception\FatalThrowableError;
use Illuminate\Support\Facades\Blade;

  function render($__php, $__data)
  {
      $obLevel = ob_get_level();
      ob_start();
      extract($__data, EXTR_SKIP);
      try {
        Log::debug($__php);
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
  public function index(Matter $matter, Request $request) {
    $template_id = $request->input('template_id');
    //limit to actors with email
    $contacts = MatterActors::where([['matter_id',$matter->id],['role_code','CNT']])->whereNotNull('email');
    if($contacts->count() === 0) {
      $contacts =  MatterActors::where([['matter_id',$matter->id],['role_code','CLI']])->whereNotNull('email');
    }
    $contacts = $contacts->get();
    $table = new Actor;
    //TODO getTableComments is the same as in Rule.php. To render common
    $tableComments = $table->getTableComments('template_members');
    $filters =  $request->except(['page']);
    $members = new TemplateMember;
    $oldfilters = [];
    if (!empty($filters)) {
        foreach ($filters as $key => $value) {
            if ($value != '') {
                switch($key) {
                    case 'Category':
                        $members = $members->whereHas('category', function ($query) use ($value){
                          $query->where('category', 'LIKE', "$value%");
                        });
                        $oldfilters["Category"] = $value;
                        break;
                    case 'Language':
                        $members = $members->whereHas('language', function ($query) use ($value){
                          $query->where('language', 'LIKE', "$value%");
                        });
                        $oldfilters["Language"] = $value;
                        break;
                    case 'Name':
                        $members = $members->whereHas('class', function ($query) use ($value){
                          $query->where('name', 'LIKE', "$value%");
                        });
                        $oldfilters["Name"] = $value;
                        break;
                    case 'Style':
                        $members = $members->whereHas('style', function ($query) use ($value){
                          $query->where('style', 'LIKE', "$value%");
                        });
                        $oldfilters["Style"] = $value;
                        break;
                }
            }
        }
    }
    $members = $members->get();
    return view('documents.select',compact('matter','members', 'contacts', 'tableComments','oldfilters'));
  }

  /*
    Prepare a mailto: href with template and data from the matter

  */
    public function mailto(TemplateMember $member, Request $request) {
      // Todo Add field for maually add an address
      $data =  array();
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
        if (count($cc_ids) != 0) {
            $mailto .= $sep . "cc=" . implode(',', Actor::whereIn('id', $cc_ids)->pluck('email')->all());
            $sep = "&";
        }
        if ($member->subject != "") {
          $mailto .= $sep."subject=".rawurlencode($member->subject);
          $sep = "&";
        }
        $data['matter']  = Matter::where(['id'=>$request->matter_id])->first();
        $data['description'] = implode("\n",Matter::getDescription($request->matter_id, $member->language->code));
        Log::debug($data['description']);
        $mailto .= $sep . "body=" . rawurlencode(render($blade, compact('data')));
        Log::debug($mailto);
        return json_encode(['mailto' => $mailto]);
      }
      else {
        return json_encode(['message' =>"You need to select at least one contact."]);
      }
    }
}
