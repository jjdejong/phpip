@extends('layouts.app')

@section('content')
<legend class="alert alert-dark d-flex justify-content-between py-2 mb-1">
    {{ __('Manage Translations') }}
</legend>

<div class="card border-primary">
    <div class="card-header bg-primary text-light py-2">
        {{ __('Select an entity type to manage translations:') }}
    </div>
    <div class="card-body p-0">
        <div class="row">
            <div class="col-6">
                <div class="list-group list-group-flush">
                    @php
                        $types = array_keys($entities);
                        $halfCount = ceil(count($types) / 2);
                        $firstHalf = array_slice($types, 0, $halfCount);
                    @endphp
                    
                    @foreach($firstHalf as $type)
                        <a href="{{ route('translations.list', $type) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            {{ __(str_replace('_', ' ', ucfirst($type))) }}
                            <span class="badge bg-primary rounded-pill">{{ $entities[$type] }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
            <div class="col-6">
                <div class="list-group list-group-flush">
                    @php
                        $secondHalf = array_slice($types, $halfCount);
                    @endphp
                    
                    @foreach($secondHalf as $type)
                        <a href="{{ route('translations.list', $type) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            {{ __(str_replace('_', ' ', ucfirst($type))) }}
                            <span class="badge bg-primary rounded-pill">{{ $entities[$type] }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<div class="mt-3 alert alert-info" role="alert">
    {{ __('Select an entity type to manage its translations. Each item can be translated into multiple languages.') }}
</div>
@endsection