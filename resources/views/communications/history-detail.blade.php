@extends('layouts.app')

@section('title', 'Communication Detail')
@section('page-title', 'Communication Detail')

@section('styles')
    <style>
        .comms-wrap {
            width: 100%;
        }

        .comms-head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 20px;
        }

        .comms-head h1 {
            font-size: 20px;
            font-weight: 800;
            color: #111827;
        }

        .comms-head p {
            font-size: 13px;
            color: var(--pts-grey);
            margin-top: 4px;
        }

        .panel {
            background: #fff;
            border: 1px solid var(--pts-grey-light);
            border-radius: 14px;
            padding: 24px;
        }

        .btn {
            padding: 11px 22px;
            border-radius: 9px;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            border: none;
            font-family: inherit;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }

        .btn-secondary {
            background: #fff;
            color: #374151;
            border: 1.5px solid var(--pts-grey-light);
        }

        .btn-secondary:hover {
            border-color: var(--pts-green);
            color: var(--pts-green-dark);
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }

        .data-table th {
            text-align: left;
            font-size: 11.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--pts-grey);
            padding: 10px 12px;
            border-bottom: 1.5px solid var(--pts-grey-light);
        }

        .data-table td {
            padding: 12px;
            border-bottom: 1px solid var(--pts-grey-light);
            color: #111827;
            vertical-align: middle;
        }

        .data-table tbody tr:last-child td {
            border-bottom: none;
        }

        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 6px;
            font-size: 11.5px;
            font-weight: 700;
            line-height: 1.4;
        }

        .badge-success {
            background: var(--pts-green-light);
            color: var(--pts-green-dark);
        }

        .badge-danger {
            background: #fee2e2;
            color: #b91c1c;
        }

        .badge-warning {
            background: #fef3c7;
            color: #92400e;
        }

        .badge-secondary {
            background: #f3f4f6;
            color: #4b5563;
        }

        .text-muted {
            color: var(--pts-grey);
        }
    </style>
@endsection

@section('content')

    <div class="comms-wrap">

        <div class="comms-head">
            <div>
                <h1>Send Detail</h1>
                <p>{{ $subject }} &middot; {{ \Carbon\Carbon::parse($batchKey)->format('d M Y, H:i') }}</p>
            </div>
            <a href="{{ route('communications.history') }}" class="btn btn-secondary">
                <i class="fa-solid fa-arrow-left"></i> Back to history
            </a>
        </div>

        <div class="panel">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Recipient</th>
                        <th>Status</th>
                        <th>Sent</th>
                        <th>Error</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($rows as $row)
                        <tr>
                            <td>{{ $row->recipient }}</td>
                            <td>
                                @php
                                    $badge = match ($row->status) {
                                        'delivered', 'sent' => 'badge-success',
                                        'failed', 'bounced' => 'badge-danger',
                                        'queued' => 'badge-warning',
                                        default => 'badge-secondary',
                                    };
                                @endphp
                                <span class="badge {{ $badge }}">{{ ucfirst($row->status) }}</span>
                            </td>
                            <td>{{ $row->sent_at ? \Carbon\Carbon::parse($row->sent_at)->format('d M Y, H:i') : '—' }}</td>
                            <td class="text-muted">{{ $row->error ?: '—' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    </div>

@endsection