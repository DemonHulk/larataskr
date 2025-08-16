@extends('layouts.app')
@section('content')

    <form method="GET" action="{{ route('tasks.index') }}" class="inline" style="margin-bottom:12px;">
        <label>Project:&nbsp;</label>
        <select name="project_id" onchange="this.form.submit()">
            <option value="">(All / no project)</option>
            @foreach($projects as $p)
                <option value="{{ $p->id }}" @selected($projectId === $p->id)>{{ $p->name }}</option>
            @endforeach
        </select>
        @if($projectId)
            <a href="{{ route('tasks.index') }}" style="margin-left:8px;text-decoration:underline;">Clear</a>
        @endif
    </form>

    <form method="POST" action="{{ route('projects.store') }}" class="inline" style="margin-left:12px;">
        @csrf
        <input type="text" name="name" placeholder="New project" required>
        <button>Create project</button>
    </form>

    {{-- Create task --}}
    <form method="POST" action="{{ route('tasks.store') }}" class="inline" style="margin-top:16px; margin-bottom:16px; display:block;">
        @csrf
        <input type="text" name="name" placeholder="Task name" required>
        <select name="project_id">
            <option value="">(no project)</option>
            @foreach($projects as $p)
                <option value="{{ $p->id }}" @selected($projectId === $p->id)>{{ $p->name }}</option>
            @endforeach
        </select>
        <button>Add</button>
    </form>

    @error('name')<div class="muted">Error: {{ $message }}</div>@enderror

    {{-- Task list with drag & drop --}}
    <ul id="task-list" data-project-id="{{ $projectId }}">
        @forelse($tasks as $task)
            <li class="task-item" data-id="{{ $task->id }}">
                <span class="handle">☰</span>
                <form method="POST" action="{{ route('tasks.update', $task) }}" class="inline">
                    @csrf @method('PATCH')
                    <input type="text" name="name" value="{{ $task->name }}" required>
                    <select name="project_id">
                        <option value="">(no project)</option>
                        @foreach($projects as $p)
                            <option value="{{ $p->id }}" @selected($task->project_id === $p->id)>{{ $p->name }}</option>
                        @endforeach
                    </select>
                    <button>Save</button>
                </form>
                <span class="muted">#{{ $task->priority }}</span>
                <form method="POST" action="{{ route('tasks.destroy', $task) }}" class="right">
                    @csrf @method('DELETE')
                    <button style="background:#ef4444">Delete</button>
                </form>
            </li>
        @empty
            <li>No tasks for this filter.</li>
        @endforelse
    </ul>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const list = document.getElementById('task-list');
            if (!list || !window.Sortable) return;

            const projectId = list.dataset.projectId || null;

            new window.Sortable(list, {
                handle: '.handle',
                animation: 150,
                ghostClass: 'drag-ghost',
                onEnd: async () => {
                    const ids = [...list.querySelectorAll('.task-item')].map(li => Number(li.dataset.id));
                    try {
                        await fetch('{{ route('tasks.reorder') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({ order: ids, project_id: projectId ? Number(projectId) : null })
                        });
                        [...list.querySelectorAll('.task-item')].forEach((li, idx) => {
                            const badge = li.querySelector('.muted');
                            if (badge) badge.textContent = `#${idx + 1}`;
                        });
                    } catch (e) {
                        alert('Could not reorder. Please try again.');
                    }
                }
            });
        });
    </script>
@endsection
