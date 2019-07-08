<div class="card">
  <div class="card-header">Matter Dependencies (only the first few are shown)</div>
  <div class="card-body">
  @forelse($matter_dependencies as $mal)
    <a href="/matter/{{$mal->matter_id}}" target="_blank">{{ $mal->matter->UID }}</a>
    ({{ $mal->role }})
  @empty
    No dependencies
  @endforelse
  </div>
</div>
<div class="card">
  <div class="card-header">Inter-Actor Dependencies</div>
  <div class="card-body">
  @forelse($other_dependencies as $other)
    <a href="/actor/{{$other->id}}" target="_blank">{{ $other->Actor }}</a>
    ({{ $other->Dependency }})
  @empty
    No dependencies
  @endforelse
  </div>
</div>
