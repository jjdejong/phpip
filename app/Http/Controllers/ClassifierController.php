<?php

namespace App\Http\Controllers;

use App\Classifier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClassifierController extends Controller
{
    public function index()
    {
        //
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'matter_id' => 'required',
            'type_code' => 'required',
            'value' => 'required_without_all:lnk_matter_id,image',
            'image' => 'image|max:1024',
        ]);
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $request->merge(['value' => $file->getMimeType()]);
            $request->merge(['img' => $file->openFile()->fread($file->getSize())]);
        }
        $request->merge(['creator' => Auth::user()->login]);

        return Classifier::create($request->except(['_token', '_method', 'image']))->id;
    }

    public function show(Classifier $classifier)
    {
        //
    }

    public function update(Request $request, Classifier $classifier)
    {
        if ($classifier->type->main_display && ! $request->filled('value')) {
            $classifier->delete();
        } else {
            $request->merge(['updater' => Auth::user()->login]);
            $classifier->update($request->except(['_token', '_method']));
        }

        return $classifier;
    }

    public function destroy(Classifier $classifier)
    {
        $classifier->delete();

        return $classifier;
    }
}
