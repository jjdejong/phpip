<?php

namespace App\Http\Controllers;

use App\Models\Classifier;
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
        \Log::info('ClassifierController store called');
        try {
            $this->validate($request, [
                'matter_id' => 'required',
                'type_code' => 'required',
                'value' => 'required_without_all:lnk_matter_id,image',
                'image' => 'image|max:1024',
            ]);

            $data = [
                'matter_id' => $request->input('matter_id'),
                'type_code' => $request->input('type_code'),
                'creator' => Auth::user()->login,
            ];

            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $data['value'] = $file->getMimeType();
                $data['img'] = file_get_contents($file->getRealPath());

                // Check if image classifier already exists for this matter
                $existing = Classifier::where('matter_id', $data['matter_id'])
                    ->where('type_code', 'IMG')
                    ->first();

                if ($existing) {
                    // Update existing image
                    $existing->update([
                        'value' => $data['value'],
                        'img' => $data['img'],
                        'updater' => Auth::user()->login,
                    ]);
                    $classifier = $existing;
                } else {
                    // Create new image classifier
                    $classifier = Classifier::create($data);
                }
            } else {
                if ($request->filled('value')) {
                    $data['value'] = $request->input('value');
                }
                if ($request->filled('url')) {
                    $data['url'] = $request->input('url');
                }
                if ($request->filled('lnk_matter_id')) {
                    $data['lnk_matter_id'] = $request->input('lnk_matter_id');
                }

                $classifier = Classifier::create($data);
            }

            $classifierId = $classifier->id;

            // Clear request to avoid binary serialization errors
            $request->replace([]);

            return response($classifierId, 200)->header('Content-Type', 'text/plain');
        } catch (\Exception $e) {
            \Log::error('Classifier store failed: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response('Error: ' . $e->getMessage(), 500)->header('Content-Type', 'text/plain');
        }
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
        $id = $classifier->id;
        $classifier->delete();

        return response($id, 200)->header('Content-Type', 'text/plain');
    }

    public function showImage(Classifier $classifier)
    {
        return response($classifier->img)
            ->header('Content-Type', $classifier->value);
    }
}
