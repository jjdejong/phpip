<div>
    <span>Matter Dependencies</span> 
    <table class="table table-striped table-hover table-sm">
      <thead>
        <tr>
            <td><b>Ref</b></td>
            <td><b>Role</b></td>
        </tr>
      </thead>
        <div>
            @foreach($matter_dependencies as $mal)
            <tr>
                <td>
                    <a href="/matter/{{$mal->matter_id}}" target="_blank">{{ $mal->matter->UID }}</a>
                </td>
                <td>{{ $mal->role }}</td>
            </tr>
            @endforeach
        </div>
    </table>
</div>

<div>
    <span>Other Actor Dependencies</span> 
    <table class="table table-striped table-hover table-sm">
      <thead>
        <tr >
            <td><b>Actor</b></td>
            <td><b>Dependency</b></td>
        </tr>
      </thead>
        @foreach($other_dependencies as $other)
        <tr>
            <td>
                <a href="/actor/{{$other->id}}" target="_blank">{{ $other->Actor }}</a>
            </td>
            <td>{{ $other->Dependency }}</td>
        </tr>
        @endforeach
    </table>
</div>

