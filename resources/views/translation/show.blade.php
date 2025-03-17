<div data-resource="/translations/{{ $type }}/{{ $type === 'task_rules' ? $entity->id : $entity->code }}" class="reload-part">
    <div id="footerAlert" class="alert d-none mb-2"></div>
    
    <div class="alert alert-info py-1 mb-2">
        <small>{{ __('Editing translations in') }}: 
            @if(Auth::user()->language == 'en' || explode('_', Auth::user()->language)[0] == 'en')
                English
            @elseif(Auth::user()->language == 'fr')
                Français
            @elseif(Auth::user()->language == 'de')
                Deutsch
            @elseif(Auth::user()->language == 'es')
                Español
            @else
                {{ Auth::user()->language }}
            @endif
        </small>
    </div>
    
    <div class="card border-primary mb-2">
        <div class="card-header bg-primary text-light py-2">
            {{ __('Original Values') }}
            @if($type === 'task_rules' && $entity->taskInfo)
                <small class="d-block text-light opacity-75">{{ __('Task') }}: {{ $entity->taskInfo->name }}</small>
            @endif
        </div>
        <div class="card-body p-2">
            <table class="table table-bordered table-sm mb-0">
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
    
    @foreach($languages as $locale)
        <div class="card border-info mb-2">
            <div class="card-header bg-info text-light py-2 d-flex justify-content-between align-items-center">
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
            <div class="card-body p-2">
                @foreach($fields as $field)
                    <div class="mb-2">
                        <label for="{{ $locale }}_{{ $field }}" class="form-label fw-bold">{{ __(ucfirst($field)) }}</label>
                        @if($field === 'notes')
                            <textarea
                                name="translations[{{ $locale }}][{{ $field }}]"
                                id="{{ $locale }}_{{ $field }}"
                                class="form-control form-control-sm noformat"
                                rows="2"
                                data-locale="{{ $locale }}"
                            >{{ isset($translations[$locale]) ? $translations[$locale]->$field : '' }}</textarea>
                        @else
                            <input
                                type="text"
                                name="translations[{{ $locale }}][{{ $field }}]"
                                id="{{ $locale }}_{{ $field }}"
                                class="form-control form-control-sm noformat"
                                value="{{ isset($translations[$locale]) ? $translations[$locale]->$field : '' }}"
                                data-locale="{{ $locale }}"
                            >
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endforeach
</div>

<script>
// Override the change event handler for translation fields only
app.addEventListener('change', function(e) {
    if (e.target.matches('.noformat') && e.target.hasAttribute('data-locale')) {
        // Get the locale and field name
        const locale = e.target.getAttribute('data-locale');
        const fieldName = e.target.name.match(/\[([^\]]+)\]$/)[1];
        
        // Create the params
        let params = new URLSearchParams();
        params.append(`translations[${locale}][${fieldName}]`, e.target.value);
        
        // Get the resource from the closest ancestor with data-resource
        let resource = e.target.closest('[data-resource]').dataset.resource;
        
        // Show processing indicator
        e.target.classList.add('border-info');
        
        // Send the update
        fetchREST(resource, 'PUT', params)
            .then(data => {
                console.log('Translation update response:', data);
                if (data.errors) {
                    // Error response
                    e.target.classList.remove('border-info', 'is-valid');
                    e.target.classList.add('border-danger');
                    console.error('Error updating translation:', data.errors);
                } else if (data.message) {
                    // Error with message
                    e.target.classList.remove('border-info', 'is-valid');
                    e.target.classList.add('border-danger');
                    console.error('Error updating translation:', data.message);
                } else {
                    // Success - entity was returned
                    e.target.classList.remove('border-info', 'border-danger', 'is-invalid');
                    e.target.classList.add('is-valid');
                    
                    // Remove the valid indicator after 2 seconds
                    setTimeout(() => {
                        e.target.classList.remove('is-valid');
                    }, 2000);
                    
                    // Trigger an event to refresh the list if needed
                    var event = new Event('xhrsent', { bubbles: true });
                    e.target.dispatchEvent(event);
                }
            })
            .catch(error => {
                e.target.classList.remove('border-info', 'is-valid');
                e.target.classList.add('border-danger');
                console.error('Error updating translation:', error);
            });
    }
});
</script>
</div>