@extends('layouts.app')

@section('title', 'Communication History')
@section('page-title', 'Communication History')

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
            margin-bottom: 18px;
        }

        .filter-bar {
            display: flex;
            flex-wrap: wrap;
            align-items: flex-end;
            gap: 12px;
            margin-bottom: 16px;
        }

        .filter-field {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .filter-field label {
            font-size: 12.5px;
            font-weight: 600;
            color: #374151;
        }

        .filter-field select,
        .filter-field input[type="date"] {
            padding: 9px 12px;
            border: 1.5px solid var(--pts-grey-light);
            border-radius: 9px;
            font-size: 13px;
            font-family: inherit;
            background: #fff;
            outline: none;
            min-width: 160px;
        }

        .filter-field select:focus,
        .filter-field input:focus {
            border-color: var(--pts-green);
            box-shadow: 0 0 0 3px rgba(122, 193, 66, 0.15);
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

        .btn-sm {
            padding: 7px 14px;
            font-size: 12px;
        }

        .btn-primary {
            background: var(--pts-black);
            color: #fff;
        }

        .btn-primary:hover {
            background: var(--pts-green-dark);
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

        .btn-ghost {
            background: transparent;
            color: var(--pts-grey);
            padding: 11px 14px;
        }

        .btn-ghost:hover {
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

        .data-table tbody tr:hover {
            background: rgba(122, 193, 66, 0.04);
        }

        .data-table .num {
            text-align: right;
            font-variant-numeric: tabular-nums;
        }

        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 6px;
            font-size: 11.5px;
            font-weight: 700;
            line-height: 1.4;
        }

        .badge-danger {
            background: #fee2e2;
            color: #b91c1c;
        }

        .empty-state {
            text-align: center;
            padding: 48px 24px;
            color: var(--pts-grey);
        }

        .empty-state p {
            margin-bottom: 16px;
            font-size: 13.5px;
        }

        .pagination-wrap {
            margin-top: 18px;
            display: flex;
            justify-content: flex-end;
        }
    </style>
@endsection

@section('content')

    <div class="comms-wrap">

        <div class="comms-head">
            <div>
                <h1>Communication History</h1>
                <p>Past bulk sends to parents and guardians.</p>
            </div>
            <a href="{{ route('communications.index') }}" class="btn btn-secondary">
                <i class="fa-solid fa-pen-to-square"></i> New message
            </a>
        </div>

        <form method="GET" action="{{ route('communications.history') }}" class="filter-bar">
            <div class="filter-field">
                <label for="status">Status</label>
                <select name="status" id="status">
                    <option value="">Any</option>
                    @foreach (['queued', 'sent', 'delivered', 'bounced', 'failed'] as $s)
                        <option value="{{ $s }}" @selected($filters['status'] === $s)>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
            </div>

            <div class="filter-field">
                <label for="from">From</label>
                <input type="date" name="from" id="from" value="{{ $filters['from'] }}">
            </div>

            <div class="filter-field">
                <label for="to">To</label>
                <input type="date" name="to" id="to" value="{{ $filters['to'] }}">
            </div>

            <button type="submit" class="btn btn-primary">Filter</button>
            <a href="{{ route('communications.history') }}" class="btn btn-ghost">Clear</a>
        </form>

        <div class="panel">
            @if ($batches->isEmpty())
                <div class="empty-state">
                    <p>No bulk communications have been sent yet.</p>
                    <a href="{{ route('communications.index') }}" class="btn btn-primary">Send one now</a>
                </div>
            @else
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Sent</th>
                            <th>Subject</th>
                            <th class="num">Recipients</th>
                            <th class="num">Delivered</th>
                            <th class="num">Failed</th>
                            <th class="num">Queued</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($batches as $batch)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($batch->sent_at)->format('d M Y, H:i') }}</td>
                                <td>{{ $batch->subject }}</td>
                                <td class="num">{{ $batch->recipient_count }}</td>
                                <td class="num">{{ $batch->delivered_count }}</td>
                                <td class="num">
                                    @if ($batch->failed_count > 0)
                                        <span class="badge badge-danger">{{ $batch->failed_count }}</span>
                                    @else
                                        0
                                    @endif
                                </td>
                                <td class="num">{{ $batch->queued_count }}</td>
                                <td>
                                    <a href="{{ route('communications.history.detail', ['batch_key' => $batch->batch_key]) }}"
                                        class="btn btn-sm btn-secondary">View</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="pagination-wrap">
                    {{ $batches->links() }}
                </div>
            @endif
        </div>

    </div>

@endsection