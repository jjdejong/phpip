@extends('layouts.app')

@section('content')
<legend class="alert alert-dark d-flex justify-content-between py-2 mb-1">
    <span>{{ __('Manage') }} {{ __(str_replace('_', ' ', ucfirst($type))) }} {{ __('Translations') }}</span>
    
    <div class="d-flex align-items-center">
        <div class="me-3 d-flex align-items-center">
            <form method="GET" action="{{ route('translations.list', $type) }}" class="d-flex align-items-center">
                <small class="me-2">{{ __('UI:') }}</small>
                <select name="interface_locale" id="interface_locale" class="form-select form-select-sm me-2" style="width: auto;" onchange="this.form.submit()">
                    @foreach($languages as $language)
                        <option value="{{ $language }}" {{ $selectedUiLocale == $language ? 'selected' : '' }}>
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
                        </option>
                    @endforeach
                </select>
                
                <input type="hidden" name="content_locale" value="{{ $selectedContentLocale }}">
            </form>
        </div>
        
        <a href="{{ route('translations.index') }}" class="btn btn-primary btn-sm">
            {{ __('Back to Types') }}
        </a>
    </div>
</legend>

<div class="row">
    <div class="col-7">
        <div class="card border-primary p-1" style="max-height: 640px; overflow: auto;">
            @if (session('success'))
                <div class="alert alert-success m-2" role="alert">
                    {{ session('success') }}
                </div>
            @endif
            
            <div id="filter" class="card-header bg-primary text-light py-2">
                <div class="row">
                    <div class="col-3">{{ __('ID') }}
                        <input class="form-control form-control-sm mt-1" data-source="/translations/{{ $type }}" name="Code" placeholder="Filter ID">
                    </div>
                    @if($type === 'task_rules')
                        <div class="col-3">{{ __('Task Name') }}
                            <input class="form-control form-control-sm mt-1" data-source="/translations/{{ $type }}" name="TaskName" placeholder="Filter Task">
                        </div>
                        <div class="col-6">{{ __('Translated Text') }} ({{ $selectedContentLocale }})
                            <input class="form-control form-control-sm mt-1" data-source="/translations/{{ $type }}" name="Text" placeholder="Filter Text">
                        </div>
                    @else
                        <div class="col-9">{{ __('Translated Text') }} ({{ $selectedContentLocale }})
                            <input class="form-control form-control-sm mt-1" data-source="/translations/{{ $type }}" name="Text" placeholder="Filter Text">
                        </div>
                    @endif
                </div>
            </div>
            
            <div class="card-body p-0">
                <table class="table table-striped table-hover table-sm mb-0">
                    <tbody id="tableList">
                        @include('translation.list_tbody')
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="mt-2 d-flex justify-content-between align-items-center">
            <a href="{{ route('translations.index') }}" class="btn btn-outline-primary btn-sm">
                {{ __('Back to Translation Types') }}
            </a>
            
            <form method="GET" action="{{ route('translations.list', $type) }}" class="d-flex align-items-center">
                <small class="me-2 text-nowrap">{{ __('Content language:') }}</small>
                <select name="content_locale" id="content_locale" class="form-select form-select-sm" style="width: auto;" onchange="this.form.submit()">
                    @foreach($languages as $language)
                        <option value="{{ $language }}" {{ $selectedContentLocale == $language ? 'selected' : '' }}>
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
                        </option>
                    @endforeach
                </select>
                
                <input type="hidden" name="interface_locale" value="{{ $selectedUiLocale }}">
            </form>
        </div>
    </div>
    
    <div class="col-5">
        <div class="card border-info">
            <div class="card-header bg-info text-light">
                {{ __('Translation Information') }}
            </div>
            <div class="card-body p-2" id="ajaxPanel">
                <div class="alert alert-info" role="alert">
                    {{ __('Click on an item to view and edit translations') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="{{ asset('js/tables.js') }}" defer></script>
@endsection