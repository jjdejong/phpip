@php
$mdeps = $matter_dependencies->groupBy('role');
$adeps = $other_dependencies->groupBy('Dependency');
@endphp

<dl>
  <dt>Matter Dependencies (only the first few are shown)</dt>
  <dd>
  @forelse($mdeps as $role => $rmdeps)
    <p><b>{{ $role }}:</b>
    @foreach($rmdeps as $mal)
      <a href="/matter/{{$mal->matter_id}}" target="_blank">{{ $mal->matter->UID }}</a>
    @endforeach
    </p>
  @empty
    No dependencies
  @endforelse
  </dd>
  <dt>Inter-Actor Dependencies</dt>
  <dd>
  @forelse($adeps as $dep => $aadeps)
    <p><b>{{ $dep }}:</b>
    @foreach($aadeps as $other)
      <a href="/actor/{{$other->id}}" target="_blank">{{ $other->Actor }}</a>
    @endforeach
    </p>
  @empty
    No dependencies
  @endforelse
  </dd>
</dl>
