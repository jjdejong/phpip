@php
$mdeps = $matter_dependencies->groupBy('role');
$adeps = $other_dependencies->groupBy('Dependency');
@endphp

<p class="font-weight-bolder my-2">Matter Dependencies (only the first few are shown)</p>
@forelse($mdeps as $role => $rmdeps)
  <div class="card m-1">
    <div class="card-header p-0">
      <b>{{ $role }}</b>
    </div>
    <div class="card-body p-1 align-middle">
      @foreach($rmdeps as $mal)
        <a class="badge badge-primary" href="/matter/{{$mal->matter_id}}" target="_blank">{{ $mal->matter->uid }}</a>
      @endforeach
    </div>
  </div>
@empty
  No dependencies
@endforelse
<p class="font-weight-bolder my-2">Inter-Actor Dependencies</p>
@forelse($adeps as $dep => $aadeps)
  <div class="card m-1">
    <div class="card-header p-0">
      <b>{{ $dep }}</b>
    </div>
    <div class="card-body p-1">
      @foreach($aadeps as $other)
        <a href="/actor/{{$other->id}}" target="_blank">{{ $other->Actor }}</a>
      @endforeach
    </div>
  </div>
@empty
  No dependencies
@endforelse
