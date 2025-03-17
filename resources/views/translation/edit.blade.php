@extends('layouts.app')

@section('content')
<legend class="alert alert-dark d-flex justify-content-between py-2 mb-1">
    <span>{{ __('Edit Translations for') }} {{ __(str_replace('_', ' ', ucfirst($type))) }}</span>
    
    <a href="{{ route('translations.list', $type) }}" class="btn btn-primary">
        {{ __('Back to List') }}
    </a>
</legend>

<div class="row">
    <div class="col-md-8">
        <form method="POST" action="{{ route('translations.update', ['type' => $type, 'id' => $entity->getKey()]) }}">
            @csrf
            @method('PUT')
            
            <div class="card border-primary mb-4">
                <div class="card-header bg-primary text-light">
                    {{ __('Original Values') }}
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-sm">
                        <thead class="table-light">
                            <tr>
                                @foreach($fields as $field)
                                    <th>{{ __(ucfirst($field)) }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                @foreach($fields as $field)
                                    <td>{{ $entity->getRawOriginal($field) }}</td>
                                @endforeach
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            
            @if(count($languages) == 0)
                <div class="alert alert-warning">
                    No languages are available for translation.
                </div>
            @endif
            
            @foreach($languages as $locale)
                <div class="card border-info mb-4">
                    <div class="card-header bg-info text-light d-flex justify-content-between align-items-center">
                        <span>{{ __('Translation') }}: 
                            @if($locale == 'en')
                                English
                            @elseif($locale == 'fr')
                                Français
                            @elseif($locale == 'de')
                                Deutsch
                            @elseif($locale == 'es')
                                Español
                            @elseif($locale == '')
                                [Empty Locale]
                            @else
                                {{ $locale }}
                            @endif
                        </span>
                        
                        <div>
                            @if($locale == config('app.locale') || (explode('_', config('app.locale'))[0] == $locale))
                                <span class="badge bg-light text-dark">{{ __('Default Language') }}</span>
                            @endif
                            
                            @if(!isset($translations[$locale]))
                                <span class="badge bg-warning text-dark ms-2">{{ __('New') }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach($fields as $field)
                                <div class="col-md-{{ $field === 'notes' ? '12' : '6' }} mb-3">
                                    <label for="{{ $locale }}_{{ $field }}" class="form-label">{{ __(ucfirst($field)) }}</label>
                                    @if($field === 'notes')
                                        <textarea
                                            name="translations[{{ $locale }}][{{ $field }}]"
                                            id="{{ $locale }}_{{ $field }}"
                                            class="form-control"
                                            rows="3"
                                        >{{ isset($translations[$locale]) ? $translations[$locale]->$field : '' }}</textarea>
                                    @else
                                        <input
                                            type="text"
                                            name="translations[{{ $locale }}][{{ $field }}]"
                                            id="{{ $locale }}_{{ $field }}"
                                            class="form-control"
                                            value="{{ isset($translations[$locale]) ? $translations[$locale]->$field : '' }}"
                                        >
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach
            
            <div class="d-flex justify-content-center mb-4">
                <button type="submit" class="btn btn-lg btn-primary px-5">
                    {{ __('Save All Translations') }}
                </button>
            </div>
        </form>
    </div>
    
    <div class="col-md-4">
        <div class="card border-info mb-4 sticky-top" style="top: 20px;">
            <div class="card-header bg-info text-light">
                {{ __('Translation Information') }}
            </div>
            <div class="card-body">
                <h5>{{ __(str_replace('_', ' ', ucfirst($type))) }}</h5>
                <p>{{ __('ID') }}: <strong>{{ $entity->getKey() }}</strong></p>
                
                <hr>
                
                <h6>{{ __('Fields for Translation') }}</h6>
                <ul class="list-group list-group-flush mb-3">
                    @foreach($fields as $field)
                        <li class="list-group-item">
                            <strong>{{ __(ucfirst($field)) }}</strong>
                        </li>
                    @endforeach
                </ul>
                
                <hr>
                
                <h6>{{ __('Languages') }}</h6>
                <ul class="list-group list-group-flush mb-3">
                    @foreach($languages as $language)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @if($language == 'en')
                                English
                            @elseif($language == 'fr')
                                Français
                            @elseif($language == 'de')
                                Deutsch
                            @elseif($language == 'es')
                                Español
                            @else
                                {{ $language }}
                            @endif
                            @if($language == config('app.locale') || (explode('_', config('app.locale'))[0] == $language))
                                <span class="badge bg-primary rounded-pill">{{ __('Default') }}</span>
                            @endif
                        </li>
                    @endforeach
                </ul>
                
                <a href="{{ route('translations.list', $type) }}" class="btn btn-outline-primary w-100">
                    {{ __('Back to List') }}
                </a>
            </div>
        </div>
    </div>
</div>
@endsection