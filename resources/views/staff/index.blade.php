@extends('layouts.app')

@section('title', 'Staff')
@section('page-title', 'Staff')

@section('styles')
    <style>
        .page-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .page-head__sub {
            font-size: 13.5px;
            color: var(--pts-grey);
        }

        .btn-add {
            padding: 11px 18px;
            background: var(--pts-black);
            color: #fff;
            border: none;
            border-radius: 9px;
            font-size: 13.5px;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }

        .btn-add:hover {
            background: var(--pts-green-dark);
        }

        .table-panel {
            background: var(--pts-white);
            border: 1px solid var(--pts-grey-light);
            border-radius: 14px;
            overflow: hidden;
        }

        .staff-table {
            width: 100%;
            border-collapse: collapse;
        }

        .staff-table thead th {
            text-align: left;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--pts-grey);
            font-weight: 700;
            padding: 14px 20px;
            background: var(--pts-bg);
            border-bottom: 1px solid var(--pts-grey-light);
        }

        .staff-table tbody td {
            padding: 14px 20px;
            font-size: 13.5px;
            color: #374151;
            border-bottom: 1px solid var(--pts-grey-light);
        }

        .staff-table tbody tr:last-child td {
            border-bottom: none;
        }

        .staff-cell {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .staff-avatar {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: var(--pts-black);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 700;
            flex-shrink: 0;
        }

        .role-pill {
            font-size: 11px;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 20px;
            background: var(--pts-bg);
            color: #374151;
            text-transform: capitalize;
        }

        .status-pill {
            font-size: 11px;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 20px;
        }

        .status-pill.pending {
            background: #FFFBEB;
            color: #D97706;
        }

        .status-pill.active {
            background: var(--pts-green-light);
            color: var(--pts-green-dark);
        }
    </style>
@endsection

@section('content')

    <div class="page-head">
        <div class="page-head__sub">{{ $staff->count() }} staff members</div>
        <a href="{{ route('staff.create') }}" class="btn-add"><i class="fa-solid fa-plus"></i> Add Staff</a>
    </div>

    <div class="table-panel">
        <table class="staff-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Role</th>
                    <th>Email</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($staff as $member)
                    <tr>
                        <td>
                            <div class="staff-cell">
                                <div class="staff-avatar">{{ strtoupper(substr($member->name, 0, 1)) }}</div>
                                {{ $member->name }}
                            </div>
                        </td>
                        <td><span class="role-pill">{{ str_replace('_', ' ', $member->role) }}</span></td>
                        <td>{{ $member->email }}</td>
                        <td>
                            <span class="status-pill {{ $member->must_change_password ? 'pending' : 'active' }}">
                                {{ $member->must_change_password ? 'Invite Pending' : 'Active' }}
                            </span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

@endsection