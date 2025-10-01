# phpIP Frontend Architecture Documentation

## Design Philosophy

phpIP follows a **minimal dependency, native JavaScript** approach:
- Lightweight interactions using vanilla JavaScript
- Data-driven configuration via HTML5 data attributes
- No heavy framework dependencies (React, Vue, Angular)
- Progressive enhancement pattern
- Bootstrap 5 for UI components

## Core Helper Functions

Located in [public/js/main.js](../public/js/main.js)

### AJAX Operations

#### `fetchREST(url, method, body)`
Performs REST operations with automatic CSRF token handling and error management.

**Usage:**
```javascript
// POST request
const formData = new FormData(myForm);
fetchREST('/resource', 'POST', formData)
  .then(data => {
    if (data.errors) {
      // Handle validation errors
    } else {
      // Success
    }
  });

// PUT request (for updates)
const params = new URLSearchParams();
params.append('field_name', 'new_value');
fetchREST('/resource/123', 'PUT', params)
  .then(data => { /* handle response */ });

// DELETE request
fetchREST('/resource/123', 'DELETE')
  .then(data => { /* handle response */ });
```

**Error Handling:**
- `500`: Shows alert with error text
- `419`: Token expired (prompts page refresh)
- Returns JSON response for processing

#### `reloadPart(url, partId)`
Fetches HTML from a URL and updates a specific element by ID.

**Usage:**
```javascript
// Refresh a panel after changes
reloadPart(window.location.href, 'actorPanel');
```

**How it works:**
1. Fetches full HTML page from URL
2. Parses response as DOM
3. Extracts element with matching `partId`
4. Replaces current element's innerHTML

#### `fetchInto(url, element)`
Simple helper to load HTML into an element.

**Usage:**
```javascript
fetchInto('/matter/123/events', ajaxModal.querySelector('.modal-body'));
```

### Utility Functions

#### `debounce(func, wait, immediate)`
Simple debounce implementation for input events.

**Usage:**
```javascript
input.addEventListener('input', debounce(e => {
  // This fires after 300ms of no input
  performSearch(e.target.value);
}, 300));
```

#### `submitModalForm(target, form, after, submitButton)`
Standardized form submission for modal forms.

**Parameters:**
- `target`: URL to POST to
- `form`: Form element
- `after`: Action after success: `'reloadModal'`, `'reloadPartial'`, or `null` (reload page)
- `submitButton`: Button element (optional, for spinner)

**Usage:**
```javascript
submitModalForm('/event', addEventForm, 'reloadModal', e.target);
```

## Data Attribute Patterns

### Resource Identification: `data-resource`

Identifies the REST endpoint for a resource.

**Usage in HTML:**
```html
<tr data-resource="/task/123">
  <td><input class="noformat" name="due_date" value="2024-01-15"></td>
</tr>
```

When the input changes, system automatically POSTs to `/task/123` with the field update.

**Files using this pattern:** 33 files across the application

### Autocomplete: `data-ac` and `data-actarget`

Configures autocomplete behavior on input fields.

**Attributes:**
- `data-ac`: URL endpoint returning JSON suggestions
- `data-actarget`: Hidden input name to populate with selected key
- `data-aclength`: Minimum characters before triggering (default: 1)

**Usage:**
```html
<!-- Hidden field stores the ID -->
<input type="hidden" name="actor_id">
<!-- Visible field shows the name -->
<input type="text"
       data-ac="/actor/autocomplete/1"
       data-actarget="actor_id"
       placeholder="Actor Name">
```

**Expected JSON Response:**
```json
[
  {"key": "123", "value": "John Doe", "label": "John Doe (optional display)"},
  {"key": "create", "value": "Create new..."}
]
```

**Special Keys:**
- `key: 'create'`: Triggers create-on-the-fly logic (see [main.js:461-508](../public/js/main.js#L461-L508))

**Custom Event:** Dispatches `acCompleted` event with selected item details

**Files using this pattern:** 28 files

### Modal Triggers: `data-bs-toggle` and `data-bs-target`

Bootstrap modal triggers with AJAX content loading.

**Usage:**
```html
<a href="/matter/123/events"
   data-bs-toggle="modal"
   data-bs-target="#ajaxModal"
   data-size="modal-lg"
   title="Event History">View Events</a>
```

**Behavior:**
- `href`: URL to fetch modal content from
- `title`: Sets modal title
- `data-size`: Optional modal size class (`modal-sm`, `modal-lg`)

**Implementation:** See [main.js:58-66](../public/js/main.js#L58-L66)

### Panel Loading: `data-panel`

Loads content into a side panel instead of a modal.

**Usage:**
```html
<a href="/actor/123"
   data-panel="ajaxPanel"
   title="Actor Details">View Actor</a>
```

**Files using this pattern:** Throughout index views

## Inline Editing System

### The `.noformat` Class

Applied to input fields that should:
1. Look like plain text when not focused
2. Auto-save changes to server on blur
3. Show visual feedback during editing

**Usage:**
```html
<tr data-resource="/event/456">
  <td>
    <input type="text"
           class="form-control noformat"
           name="event_date"
           value="2024-01-15">
  </td>
  <td>
    <input type="checkbox"
           class="noformat"
           name="done"
           checked>
  </td>
</tr>
```

**Behavior:**
1. On `input`: Field border turns blue (`border-info`)
2. On `change`:
   - Automatically PUTs to `data-resource` URL
   - Field border turns green (`is-valid`) on success
   - Field border turns red (`border-danger`) on error
   - For checkboxes: value sent as 1/0

**Implementation:** [main.js:369-423](../public/js/main.js#L369-L423)

**Read-only Users:** CSS prevents interaction:
```css
input.noformat, [contenteditable] {
  pointer-events: none;
}
```

### Contenteditable Fields

For non-input elements that need inline editing.

**Usage:**
```html
<dd data-resource="/classifier/789"
    data-name="value"
    contenteditable>
  Patent Title Text
</dd>
```

**Behavior:**
1. On `focusin`: Stores initial content
2. On `input`: Border turns blue
3. On `focusout`: If changed, PUTs to resource

**Implementation:** [main.js:664-674](../public/js/main.js#L664-L674)

**Files using contenteditable:** 12 files

## Modal System

### Global Modal: `#ajaxModal`

Single reusable modal defined in [layouts/app.blade.php](../resources/views/layouts/app.blade.php).

**Structure:**
```html
<div class="modal" id="ajaxModal">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Ajax title placeholder</h5>
      </div>
      <div class="modal-body">
        <div class="spinner-border"></div>
      </div>
      <div class="modal-footer">
        <div id="footerAlert" class="alert"></div>
      </div>
    </div>
  </div>
</div>
```

**Lifecycle:**
1. **On show** ([main.js:58-66](../public/js/main.js#L58-L66)):
   - Fetches content from trigger's `href`
   - Sets modal title from trigger's `title`
   - Applies size class from `data-size`
   - Loads content into `.modal-body`

2. **On hide** (view-specific):
   - Example: [matter-show.js:146-162](../public/js/matter-show.js#L146-L162)
   - Refreshes affected panels based on URL path
   - Clears `contentSrc` variable

3. **On hidden** ([main.js:442-448](../public/js/main.js#L442-L448)):
   - Resets modal to default state
   - Clears title, content, alerts
   - Removes size classes

**Modal Content Views:**
- Should be partial views (forms/tables)
- Can include forms with `.noformat` fields
- Can trigger nested modals if needed

### Panel Refresh Pattern

After modal closes, refresh the affected main page section:

```javascript
ajaxModal.addEventListener('hide.bs.modal', e => {
  switch (contentSrc.split('/')[5]) {
    case 'events':
      reloadPart(window.location.href, 'multiPanel');
      break;
    case 'roleActors':
      reloadPart(window.location.href, 'actorPanel');
      break;
  }
});
```

## Common Patterns

### Pattern 1: Add Form in Collapse

**Example:** Title addition in [matter/show.blade.php:113-129](../resources/views/matter/show.blade.php#L113-L129)

```html
<a data-bs-toggle="collapse" href="#addTitleCollapse">+</a>
<div id="addTitleCollapse" class="collapse">
  <form id="addTitleForm">
    <input type="hidden" name="matter_id" value="123">
    <input type="text" name="value" placeholder="Value">
    <button type="button" id="addTitleSubmit">✓</button>
  </form>
</div>
```

**JavaScript:**
```javascript
document.getElementById('addTitleSubmit').onclick = e => {
  const formData = new FormData(addTitleForm);
  fetchREST('/classifier', 'POST', formData)
    .then(data => {
      if (data.errors) {
        processSubmitErrors(data.errors, addTitleForm);
      } else {
        reloadPart(window.location.href, 'titlePanel');
      }
    });
};
```

### Pattern 2: Table with Inline Add Row

**Example:** Event list in [matter/events.blade.php:22-39](../resources/views/matter/events.blade.php#L22-L39)

```html
<table>
  <thead>
    <tr>
      <th>Event <a data-bs-toggle="collapse" href="#addEventRow">+</a></th>
    </tr>
    <tr id="addEventRow" class="collapse">
      <td colspan="5">
        <form id="addEventForm">
          <!-- Form fields -->
          <button id="addEventSubmit">✓</button>
        </form>
      </td>
    </tr>
  </thead>
  <tbody>
    @foreach ($events as $event)
    <tr data-resource="/event/{{ $event->id }}">
      <td><input class="noformat" name="event_date" value="{{ $event->date }}"></td>
    </tr>
    @endforeach
  </tbody>
</table>
```

### Pattern 3: Click-to-Reveal Inline Form

**Example:** Template selection in tasks ([main.js:91-108](../public/js/main.js#L91-L108))

```javascript
if (e.target.matches('.chooseTemplate')) {
  var form_tr = document.getElementById('selectTrForm');
  if (form_tr == null) {
    var select = document.createElement('tr');
    select.setAttribute('id', 'selectTrForm');
    currentTr.parentNode.insertBefore(select, currentTr.nextSibling);
    fetchInto(e.target.dataset.url, select);
  } else {
    form_tr.remove();
  }
}
```

### Pattern 4: Drag-and-Drop Reordering

**Example:** Role actors ordering ([main.js:733-770](../public/js/main.js#L733-L770))

```html
<tbody id="sortableList">
  <tr data-resource="/actor-pivot/123" data-n="1" draggable="true">
    <td><input type="hidden" name="display_order" value="1"></td>
    <td>Actor Name</td>
  </tr>
</tbody>
```

**JavaScript** handles dragstart, dragover, drop, and dragend events to reorder and update.

## Event System

### Custom Events

#### `acCompleted`
Fired when autocomplete selection is made.

**Detail:** `{key, value, label, ...other fields from JSON}`

**Usage:**
```javascript
input.addEventListener('acCompleted', event => {
  const selectedItem = event.detail;
  console.log('Selected:', selectedItem.key, selectedItem.value);
});
```

#### `xhrsent`
Fired after successful inline edit submission.

**Usage:**
```javascript
// Refresh list when a field in the panel changes
ajaxPanel.addEventListener('xhrsent', e => {
  refreshList();
});
```

### Global Event Delegation

All interactions use event delegation on `#app` element:
- `click` → Button actions, delete confirmations
- `change` → `.noformat` field updates
- `input` → Visual feedback, autocomplete triggers
- `focusout` → Contenteditable saves

**Benefits:**
- Works with dynamically added content
- Single listener per event type
- Clear separation of concerns

## File Organization

### JavaScript Files

- **[main.js](../public/js/main.js)** (828 lines)
  - Core helper functions
  - Global event listeners
  - Autocomplete system
  - Modal management
  - Inline editing logic

- **[tables.js](../public/js/tables.js)** (65 lines)
  - List filtering with URL params
  - Debounced search
  - Clear filter buttons
  - Used by index views

- **[matter-show.js](../public/js/matter-show.js)** (247 lines)
  - Actor popover forms
  - Title addition
  - Panel refresh coordination
  - File drop zone

- **[matter-index.js](../public/js/matter-index.js)**
  - Matter list filtering
  - Export functionality
  - View toggles

- **[home.js](../public/js/home.js)**
  - Dashboard filtering
  - Task statistics

- **[renewal-index.js](../public/js/renewal-index.js)**
  - Renewal list management

- **[actor-index.js](../public/js/actor-index.js)**
  - Actor list functionality

- **[user-index.js](../public/js/user-index.js)**
  - User management

## Best Practices

### When Creating New Features

1. **Use existing patterns** - Check this documentation first
2. **Leverage data attributes** - Configure via HTML, not JS
3. **Use event delegation** - Attach to `#app`, not individual elements
4. **Follow the inline edit pattern** - Use `.noformat` for editable fields
5. **Reuse the modal system** - Don't create new modals
6. **Keep JS minimal** - Let the server handle logic

### Autocomplete Guidelines

**Controller response format:**
```php
public function autocomplete(Request $request) {
    $term = $request->input('term');
    $results = Model::where('name', 'like', "%{$term}%")
        ->limit(10)
        ->get()
        ->map(fn($item) => [
            'key' => $item->id,
            'value' => $item->name,
            'label' => $item->display_name, // Optional
            // Additional fields as needed
        ]);
    return response()->json($results);
}
```

**View usage:**
```html
<input type="hidden" name="model_id">
<input type="text"
       data-ac="/model/autocomplete"
       data-actarget="model_id"
       data-aclength="2"
       placeholder="Type to search">
```

### Form Validation

**Server-side (Controller):**
```php
$validated = $request->validate([
    'field' => 'required|string|max:255'
]);

// On validation failure, Laravel returns:
// { "errors": { "field": ["Error message"] }, "message": "..." }
```

**Client-side handling:**
```javascript
fetchREST('/resource', 'POST', formData)
  .then(data => {
    if (data.errors) {
      processSubmitErrors(data.errors, form);
      footerAlert.innerHTML = data.message;
      footerAlert.classList.add('alert-danger');
    } else {
      // Success - redirect or reload
    }
  });
```

### Inline Edit Validation

When inline edits fail validation:

**In modals:**
- Error shown in `#footerAlert`

**In main page:**
- Field border turns red
- Field value shows error message

## Performance Considerations

### Debouncing
Always debounce frequent events (input, scroll, resize):
```javascript
input.addEventListener('input', debounce(handler, 300));
```

### Partial Updates
Use `reloadPart()` instead of full page reload when possible:
```javascript
// Good - only updates one section
reloadPart(url, 'actorPanel');

// Avoid - reloads entire page
location.reload();
```

### MutationObserver
Autocomplete uses MutationObserver to attach to dynamically added elements. Be mindful of DOM mutations in tight loops.

## Consistency Checklist

✅ **Global Patterns Are Consistent**
- `fetchREST()` used consistently (8 files)
- `reloadPart()` used for partial updates (8 files)
- `.noformat` inline editing (12 files)
- `data-ac` autocomplete (28 files)
- `data-resource` REST endpoints (33 files)
- `#ajaxModal` reused throughout (17 files)

✅ **Event Handling**
- Event delegation on `#app`
- Custom events documented
- Cleanup handled properly

✅ **Error Handling**
- CSRF protection automatic
- Token expiry detected
- Validation errors displayed consistently

## Areas for Improvement

### Documentation Gaps
- Some view-specific JS lacks inline comments
- Autocomplete JSON response format not enforced
- Custom event details not fully documented

### Potential Enhancements
1. **TypeScript migration** - Type safety for data structures
2. **Form validation library** - Client-side validation before submit
3. **Loading states** - Unified spinner/loader system
4. **Error boundaries** - Graceful degradation on errors
5. **Testing** - Add JS unit tests for core functions

## Alpine.js Integration Guide

Alpine.js (6kb) can complement your native JavaScript approach for specific reactive UI patterns. Use it **sparingly** and only where it provides clear benefits.

### When to Use Alpine.js

✅ **Good Use Cases:**

#### 1. Tab/View Toggling ([matter/index.blade.php](../resources/views/matter/index.blade.php))
**Current:** Manual class toggling with JavaScript ([matter-index.js:29-50](../public/js/matter-index.js#L29-L50))
```javascript
// Current: 22 lines of DOM manipulation
filterButtons.onclick = e => {
  switch (e.target.id) {
    case 'showStatus':
      for (td of document.getElementsByClassName('tab1')) {
        td.classList.remove('d-none');
      }
      // ... more DOM manipulation
```

**With Alpine:**
```html
<div x-data="{ tab: {{ Request::get('tab', 0) }} }">
  <div class="btn-group">
    <label @click="tab = 0" :class="tab === 0 ? 'active' : ''">Actor View</label>
    <label @click="tab = 1" :class="tab === 1 ? 'active' : ''">Status View</label>
  </div>

  <td :class="tab === 0 ? '' : 'd-none'" class="tab0">Actor data</td>
  <td :class="tab === 1 ? '' : 'd-none'" class="tab1">Status data</td>
</div>
```
**Benefit:** Eliminates 20+ lines of JS, declarative state management

#### 2. Filter Toggle States ([matter/index.blade.php](../resources/views/matter/index.blade.php))
**Current:** Multiple checkbox state handlers ([matter-index.js:51-74](../public/js/matter-index.js#L51-L74))

**With Alpine:**
```html
<div x-data="{
  showContainers: {{ Request::get('Ctnr') ? 'true' : 'false' }},
  includeDead: {{ Request::get('include_dead') ? 'true' : 'false' }}
}">
  <label :class="showContainers ? 'active' : ''"
         @click="showContainers = !showContainers; refreshList()">
    Show Containers
  </label>
</div>
```
**Benefit:** Cleaner state management for UI toggles

#### 3. Radio Button Visual States ([home.js](../public/js/home.js))
**Current:** Manual active class management ([home.js:30-47](../public/js/home.js#L30-L47))

**With Alpine:**
```html
<div x-data="{ selected: 'alltasks' }">
  <label @click="selected = 'alltasks'; refreshTasks(0)"
         :class="selected === 'alltasks' ? 'active' : ''">
    Everyone
  </label>
  <label @click="selected = 'mytasks'; refreshTasks(1)"
         :class="selected === 'mytasks' ? 'active' : ''">
    Mine
  </label>
</div>
```
**Benefit:** 15 lines of JS become 5 lines of HTML

#### 4. Collapsible Sections with State
**Example:** Classifier form ([matter/classifiers.blade.php:68-97](../resources/views/matter/classifiers.blade.php#L68-L97))

**With Alpine:**
```html
<div x-data="{ open: false, isImageType: false }">
  <a @click="open = !open">Add Classifier</a>

  <form x-show="open" x-transition>
    <input type="text" data-ac="/classifier-type/autocomplete"
           @acCompleted="isImageType = ($event.detail.value === 'Image')">

    <!-- Toggle input fields based on type -->
    <input x-show="!isImageType" name="value">
    <input x-show="isImageType" type="file" name="image">
  </form>
</div>
```
**Benefit:** Reactive form field visibility without JS event handlers

#### 5. Conditional Button States
**Example:** Sort buttons showing arrows

**With Alpine:**
```html
<div x-data="{ sortKey: '{{ Request::get('sortkey') }}', sortDir: 'asc' }">
  <button @click="sortKey = 'caseref'; sortDir = sortDir === 'asc' ? 'desc' : 'asc'"
          :class="sortKey === 'caseref' ? 'active' : ''"
          x-html="sortKey === 'caseref' ? (sortDir === 'asc' ? '↑' : '↓') : '↕'">
  </button>
</div>
```

### When NOT to Use Alpine.js

❌ **Don't Use For:**

1. **Inline editing (`.noformat` fields)** - Your current system works perfectly
2. **Autocomplete** - Complex custom implementation, keep as-is
3. **Modal system** - Already well-abstracted
4. **AJAX operations** - `fetchREST()` and `reloadPart()` are ideal
5. **Form submissions** - `submitModalForm()` handles validation well
6. **Drag-and-drop** - Current implementation is complete

### Integration Strategy

**Phase 1: Install Alpine via NPM**
```bash
yarn add alpinejs
# or
npm install alpinejs
```

**Phase 2: Import in Vite Entry Point**
Edit [resources/js/app.js](../resources/js/app.js):
```javascript
import './bootstrap';
import '../sass/app.scss';

// Import Alpine
import Alpine from 'alpinejs';
window.Alpine = Alpine;
Alpine.start();
```

**Phase 3: Build Assets**
```bash
yarn build
# or for development
yarn dev
```

**Phase 4: Convert One View Toggle**
Start with [matter/index.blade.php](../resources/views/matter/index.blade.php) tab toggle (lines 26-31)

**Phase 5: Evaluate Results**
- Less JavaScript code?
- More maintainable?
- Performance acceptable?

If yes, continue. If no, keep current approach.

### Alpine + Current Patterns

Alpine **complements** your architecture:

```html
<!-- Alpine handles UI state -->
<div x-data="{ expanded: false }">
  <button @click="expanded = !expanded">Toggle</button>

  <!-- Still use your existing patterns -->
  <form x-show="expanded">
    <input class="noformat" data-resource="/resource/123"> <!-- Inline editing still works -->
    <input data-ac="/autocomplete"> <!-- Autocomplete still works -->
    <button @click="submitModalForm('/endpoint', $el.form)">Save</button>
  </form>
</div>
```

### Specific Recommendations

**High Priority (Clear wins):**
1. ✅ Matter list tab toggle (Actor/Status views) - [matter/index.blade.php:26-31](../resources/views/matter/index.blade.php#L26-L31)
2. ✅ Dashboard radio button states - [home.js:30-47](../public/js/home.js#L30-L47)
3. ✅ Classifier form field toggling - [matter/classifiers.blade.php:87-89](../resources/views/matter/classifiers.blade.php#L87-L89)

**Medium Priority (Nice to have):**
4. Filter button active states - [matter-index.js:51-74](../public/js/matter-index.js#L51-L74)
5. Collapsible form sections throughout the app

**Low Priority (Current approach is fine):**
6. Sort button states - Works well with current JS

### Estimated Impact

**Before Alpine:**
- [matter-index.js](../public/js/matter-index.js): 96 lines
- [home.js](../public/js/home.js): 111 lines

**After Alpine (High Priority items):**
- Reduce to ~50 lines in matter-index.js
- Reduce to ~70 lines in home.js
- More declarative, easier to understand

**Total savings:** ~90 lines of imperative JavaScript replaced with ~30 lines of declarative HTML

## Migration Notes

**Recommended:**
- ✅ Alpine.js (6kb) - For reactive UI state only (see guide above)
- ✅ Keep all existing helper functions
- ✅ Keep inline editing system
- ✅ Keep autocomplete system
- ✅ Keep modal system

**Not recommended:**
- ❌ Livewire (violates minimal dependency principle)
- ❌ Full SPA frameworks (React/Vue) - too heavy for this use case
- ❌ jQuery (already eliminated, don't reintroduce)

## Troubleshooting

### Modal Not Loading Content
- Check browser console for 404/500 errors
- Verify `href` attribute on trigger element
- Ensure controller returns HTML (not JSON)

### Inline Edit Not Saving
- Check `data-resource` attribute exists on parent element
- Verify field has `.noformat` class
- Check browser console for CSRF token errors
- Confirm server route accepts PUT requests

### Autocomplete Not Working
- Verify `data-ac` URL returns JSON array
- Check `data-actarget` matches hidden input name
- Ensure JSON has `key` and `value` fields
- Check browser console for fetch errors

### Panel Not Refreshing
- Verify element ID matches `reloadPart()` call
- Ensure server returns full HTML page (not partial)
- Check that element ID exists in server response

## Contributing

When modifying frontend code:
1. Maintain native JavaScript approach
2. Update this documentation
3. Add inline comments for complex logic
4. Test with read-only users (permissions)
5. Verify CSRF protection works
6. Check browser console for errors
7. Test keyboard navigation (accessibility)
