@foreach($entities as $entity)
    <tr class="reveal-hidden">
        <td class="col-3">
            <a href="/translations/{{ $type }}/{{ $type === 'task_rules' ? $entity->id : $entity->code }}" data-panel="ajaxPanel" title="Translation">
                @if($type === 'task_rules')
                    {{ $entity->id }}
                @else
                    {{ $entity->code }}
                @endif
            </a>
        </td>
        @if($type === 'task_rules')
            <td class="col-3">
                {{ $entity->taskInfo ? $entity->taskInfo->name : 'No task assigned' }}
            </td>
            <td class="col-6">
                {{ $entity->detail }}
            </td>
        @else
            <td class="col-9">
                @if($type === 'event_name')
                    {{ $entity->name }}
                @elseif($type === 'classifier_type')
                    {{ $entity->type }}
                @elseif($type === 'matter_category')
                    {{ $entity->category }}
                @elseif($type === 'matter_type')
                    {{ $entity->type }}
                @elseif($type === 'actor_role')
                    {{ $entity->name }}
                @endif
            </td>
        @endif
    </tr>
@endforeach
<tr>
    @if($type === 'task_rules')
        <td colspan="3">
            {{ $entities->links() }}
        </td>
    @else
        <td colspan="2">
            {{ $entities->links() }}
        </td>
    @endif
</tr>