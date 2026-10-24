@extends('settings.partials._layout')

@section('settings-content')

    <a href="{{ route('settings.overview') }}" class="settings-back">
        <i class="fa-solid fa-arrow-left"></i> Back to Settings
    </a>

    <div class="settings-panel__title">Audit Log</div>
    <div class="settings-panel__sub">
        Every login, change, and deletion in your school, timestamped and attributed.
    </div>

    {{-- Filters --}}
    <form method="GET" action="{{ route('settings.audit') }}"
        style="display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 18px; align-items: flex-end;">
        <div style="flex: 1; min-width: 200px;">
            <label
                style="display:block; font-size:11px; font-weight:700; color:#374151; margin-bottom:5px; text-transform:uppercase; letter-spacing:0.04em;">
                Search
            </label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="User or description…" style="width:100%; padding:10px 12px; border:1.5px solid var(--pts-grey-light);
                              border-radius:9px; font-size:13px; font-family:inherit; outline:none;">
        </div>

        <div style="width: 160px;">
            <label
                style="display:block; font-size:11px; font-weight:700; color:#374151; margin-bottom:5px; text-transform:uppercase; letter-spacing:0.04em;">
                Action
            </label>
            <select name="action" onchange="this.form.submit()" style="width:100%; padding:10px 12px; border:1.5px solid var(--pts-grey-light);
                               border-radius:9px; font-size:13px; font-family:inherit; outline:none; background:#fff;">
                <option value="">All actions</option>
                @foreach ($actions as $a)
                    <option value="{{ $a }}" @selected(request('action') === $a)>{{ ucfirst($a) }}</option>
                @endforeach
            </select>
        </div>

        <div style="width: 140px;">
            <label
                style="display:block; font-size:11px; font-weight:700; color:#374151; margin-bottom:5px; text-transform:uppercase; letter-spacing:0.04em;">
                From
            </label>
            <input type="date" name="from" value="{{ request('from') }}" style="width:100%; padding:10px 12px; border:1.5px solid var(--pts-grey-light);
                              border-radius:9px; font-size:13px; font-family:inherit; outline:none;">
        </div>

        <div style="width: 140px;">
            <label
                style="display:block; font-size:11px; font-weight:700; color:#374151; margin-bottom:5px; text-transform:uppercase; letter-spacing:0.04em;">
                To
            </label>
            <input type="date" name="to" value="{{ request('to') }}" style="width:100%; padding:10px 12px; border:1.5px solid var(--pts-grey-light);
                              border-radius:9px; font-size:13px; font-family:inherit; outline:none;">
        </div>

        <button type="submit" style="padding: 10px 18px; background: var(--pts-black); color: #fff; border: none;
                           border-radius: 9px; font-size: 13px; font-weight: 700; cursor: pointer;">
            Filter
        </button>

        @if (request()->hasAny(['search', 'action', 'from', 'to']))
            <a href="{{ route('settings.audit') }}"
                style="padding: 10px 12px; font-size: 12.5px; color: var(--pts-grey); text-decoration: none;">
                Clear
            </a>
        @endif
    </form>

    {{-- Log table --}}
    @if ($logs->isEmpty())
        <div style="padding: 50px 20px; text-align: center; background: var(--pts-bg); border-radius: 12px;">
            <i class="fa-solid fa-list-ul" style="font-size: 28px; color: var(--pts-grey-light);
                                                          display: block; margin-bottom: 12px;"></i>
            <div style="font-size: 14px; font-weight: 700; color: #111827; margin-bottom: 4px;">
                No activity recorded
            </div>
            <div style="font-size: 12.5px; color: var(--pts-grey);">
                @if (request()->hasAny(['search', 'action', 'from', 'to']))
                    Nothing matches your filters.
                @else
                    As users log in and make changes, entries will appear here.
                @endif
            </div>
        </div>
    @else
        @php
            $actionColors = [
                'create' => ['bg' => '#DCFCE7', 'fg' => '#166534'],
                'update' => ['bg' => '#DBEAFE', 'fg' => '#1E40AF'],
                'delete' => ['bg' => '#FEE2E2', 'fg' => '#991B1B'],
                'login' => ['bg' => '#E9F5DD', 'fg' => '#5E9A31'],
                'logout' => ['bg' => '#F3F4F6', 'fg' => '#374151'],
            ];
        @endphp

        <div style="border: 1px solid var(--pts-grey-light); border-radius: 12px; overflow: hidden;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr>
                        <th style="text-align:left; font-size:10.5px; text-transform:uppercase; letter-spacing:0.06em;
                                           color:var(--pts-grey); font-weight:700; padding:12px 16px; background:var(--pts-bg);
                                           border-bottom:1px solid var(--pts-grey-light); width:150px;">When</th>
                        <th style="text-align:left; font-size:10.5px; text-transform:uppercase; letter-spacing:0.06em;
                                           color:var(--pts-grey); font-weight:700; padding:12px 16px; background:var(--pts-bg);
                                           border-bottom:1px solid var(--pts-grey-light); width:90px;">Action</th>
                        <th style="text-align:left; font-size:10.5px; text-transform:uppercase; letter-spacing:0.06em;
                                           color:var(--pts-grey); font-weight:700; padding:12px 16px; background:var(--pts-bg);
                                           border-bottom:1px solid var(--pts-grey-light); width:170px;">User</th>
                        <th style="text-align:left; font-size:10.5px; text-transform:uppercase; letter-spacing:0.06em;
                                           color:var(--pts-grey); font-weight:700; padding:12px 16px; background:var(--pts-bg);
                                           border-bottom:1px solid var(--pts-grey-light);">Description</th>
                        <th style="text-align:left; font-size:10.5px; text-transform:uppercase; letter-spacing:0.06em;
                                           color:var(--pts-grey); font-weight:700; padding:12px 16px; background:var(--pts-bg);
                                           border-bottom:1px solid var(--pts-grey-light); width:120px;">IP</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($logs as $log)
                        @php $c = $actionColors[$log->action] ?? ['bg' => '#F3F4F6', 'fg' => '#374151']; @endphp
                        <tr>
                            <td style="padding: 11px 16px; border-bottom: 1px solid var(--pts-grey-light);
                                                   font-size: 12.5px; color: #374151;">
                                <div style="font-weight: 600;">{{ $log->created_at->format('d M Y') }}</div>
                                <div style="font-size: 11px; color: var(--pts-grey);">{{ $log->created_at->format('H:i:s') }}</div>
                            </td>
                            <td style="padding: 11px 16px; border-bottom: 1px solid var(--pts-grey-light);">
                                <span style="font-size: 10px; font-weight: 800; padding: 3px 8px; border-radius: 4px;
                                                         background: {{ $c['bg'] }}; color: {{ $c['fg'] }}; text-transform: uppercase;
                                                         letter-spacing: 0.05em;">
                                    {{ $log->action }}
                                </span>
                            </td>
                            <td style="padding: 11px 16px; border-bottom: 1px solid var(--pts-grey-light);
                                                   font-size: 12.5px; color: #111827; font-weight: 600;">
                                {{ $log->user_name ?? '—' }}
                            </td>
                            <td style="padding: 11px 16px; border-bottom: 1px solid var(--pts-grey-light);
                                                   font-size: 12.5px; color: #374151;">
                                {{ $log->description ?? '—' }}
                                @if ($log->modelLabel())
                                    <span style="font-size: 10.5px; color: var(--pts-grey);">
                                        ({{ $log->modelLabel() }} #{{ $log->model_id }})
                                    </span>
                                @endif
                            </td>
                            <td
                                style="padding: 11px 16px; border-bottom: 1px solid var(--pts-grey-light);
                                                   font-size: 11.5px; color: var(--pts-grey); font-family: 'Courier New', monospace;">
                                {{ $log->ip_address ?? '—' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 18px;">
            <div style="font-size: 12.5px; color: var(--pts-grey);">
                Showing {{ $logs->firstItem() }}–{{ $logs->lastItem() }} of {{ $logs->total() }}
            </div>

            <form method="POST" action="{{ route('settings.audit.clear') }}"
                onsubmit="return confirm('Permanently delete all audit log entries for this school? This cannot be undone.');">
                @csrf @method('DELETE')
                <button type="submit" style="padding: 9px 16px; background: #fff; border: 1.5px solid #FECACA;
                                       border-radius: 9px; font-size: 12.5px; font-weight: 700;
                                       color: #DC2626; cursor: pointer;">
                    <i class="fa-solid fa-trash"></i> Clear Log
                </button>
            </form>
        </div>

        <div style="margin-top: 18px;">
            {{ $logs->links() }}
        </div>
    @endif

@endsection