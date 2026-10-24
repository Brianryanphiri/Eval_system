@extends('layouts.app')

@section('title', 'Roles & Permissions')
@section('page-title', 'Settings')

@section('styles')
    <style>
        /* ---------- Settings sub-nav ---------- */
        .settings-tabs {
            display: flex;
            gap: 4px;
            margin-bottom: 22px;
            border-bottom: 1.5px solid var(--pts-grey-light);
        }

        .settings-tab {
            padding: 12px 18px;
            font-size: 13.5px;
            font-weight: 600;
            color: var(--pts-grey);
            cursor: pointer;
            border-bottom: 2px solid transparent;
            margin-bottom: -1.5px;
            text-decoration: none;
        }

        .settings-tab.active {
            color: var(--pts-black);
            border-bottom-color: var(--pts-green);
            font-weight: 700;
        }

        .settings-tab.disabled {
            color: #C1C5CC;
            cursor: not-allowed;
        }

        .page-sub {
            font-size: 13.5px;
            color: var(--pts-grey);
            margin-bottom: 20px;
        }

        /* ---------- Permission matrix ---------- */
        .matrix-panel {
            background: var(--pts-white);
            border: 1px solid var(--pts-grey-light);
            border-radius: 14px;
            overflow: hidden;
            margin-bottom: 20px;
        }

        .matrix-table {
            width: 100%;
            border-collapse: collapse;
        }

        .matrix-table thead th {
            text-align: center;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: var(--pts-grey);
            padding: 14px 10px;
            background: var(--pts-bg);
            border-bottom: 1px solid var(--pts-grey-light);
        }

        .matrix-table thead th.perm-col {
            text-align: left;
            min-width: 220px;
        }

        .matrix-table tbody td {
            padding: 12px 10px;
            text-align: center;
            border-bottom: 1px solid var(--pts-grey-light);
        }

        .matrix-table tbody tr:last-child td {
            border-bottom: none;
        }

        .matrix-table tbody tr.group-header td {
            background: var(--pts-bg);
            text-align: left;
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--pts-green-dark);
            padding: 9px 10px;
        }

        .perm-name {
            text-align: left;
            font-size: 13px;
            font-weight: 600;
            color: #111827;
        }

        .perm-key {
            font-size: 10.5px;
            color: var(--pts-grey);
            font-family: 'Courier New', monospace;
            margin-top: 1px;
        }

        .perm-toggle {
            position: relative;
            display: inline-block;
            width: 38px;
            height: 22px;
        }

        .perm-toggle input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .toggle-slider {
            position: absolute;
            cursor: pointer;
            inset: 0;
            background: var(--pts-grey-light);
            border-radius: 20px;
            transition: 0.2s;
        }

        .toggle-slider::before {
            content: "";
            position: absolute;
            width: 16px;
            height: 16px;
            left: 3px;
            bottom: 3px;
            background: #fff;
            border-radius: 50%;
            transition: 0.2s;
        }

        .perm-toggle input:checked+.toggle-slider {
            background: var(--pts-green);
        }

        .perm-toggle input:checked+.toggle-slider::before {
            transform: translateX(16px);
        }

        .perm-toggle input:disabled+.toggle-slider {
            background: var(--pts-black);
            opacity: 0.9;
            cursor: not-allowed;
        }

        .full-access-note {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 12px;
            color: var(--pts-grey);
            padding: 12px 20px;
            background: var(--pts-bg);
            border-top: 1px solid var(--pts-grey-light);
        }

        .empty-note {
            padding: 30px 20px;
            text-align: center;
            color: var(--pts-grey);
            font-size: 13.5px;
        }

        .form-footer {
            display: flex;
            justify-content: flex-end;
        }

        .btn-primary {
            padding: 12px 24px;
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
        }

        .btn-primary:hover {
            background: var(--pts-green-dark);
        }

        @media (max-width: 700px) {
            .matrix-panel {
                overflow-x: auto;
            }

            .matrix-table {
                min-width: 600px;
            }
        }
    </style>
@endsection

@section('content')

    <div class="settings-tabs">
        <span class="settings-tab disabled" title="Not built yet">Institution Profile</span>
        <span class="settings-tab disabled" title="Not built yet">Assessment Policy</span>
        <span class="settings-tab disabled" title="Not built yet">Grading Scales</span>
        <a href="{{ route('settings.roles') }}" class="settings-tab active">Roles &amp; Permissions</a>
        <span class="settings-tab disabled" title="Not built yet">Academic Calendar</span>
    </div>

    <div class="page-sub">Define exactly what each role can view, create, edit, approve, or publish across the system.</div>

    @if (session('success'))
        <div
            style="background: var(--pts-green-light); color: var(--pts-green-dark); border: 1px solid var(--pts-green); padding: 12px 16px; border-radius: 9px; font-size: 13px; margin-bottom: 18px;">
            {{ session('success') }}
        </div>
    @endif

    @if ($permissionGroups->isEmpty())
        <div class="matrix-panel">
            <div class="empty-note">
                No permissions have been seeded yet. Run <code>php artisan db:seed --class=PermissionSeeder</code> to populate
                the permission list.
            </div>
        </div>
    @else
        <form method="POST" action="{{ route('settings.roles.update') }}">
            @csrf

            <div class="matrix-panel">
                <table class="matrix-table">
                    <thead>
                        <tr>
                            <th class="perm-col">Permission</th>
                            @foreach ($roles as $roleLabel => $roleSlug)
                                <th>{{ $roleLabel }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($permissionGroups as $groupName => $perms)
                            <tr class="group-header">
                                <td colspan="{{ count($roles) + 1 }}">{{ $groupName }}</td>
                            </tr>
                            @foreach ($perms as $permission)
                                <tr>
                                    <td class="perm-name">
                                        {{ $permission->label }}
                                        <div class="perm-key">{{ $permission->key }}</div>
                                    </td>
                                    @foreach ($roles as $roleLabel => $roleSlug)
                                        @php
                                            $isSuperAdmin = $roleSlug === 'super_admin';
                                            $isGranted = $isSuperAdmin || $grants->has("{$roleSlug}|{$permission->key}");
                                        @endphp
                                        <td>
                                            <label class="perm-toggle">
                                                <input type="checkbox" name="permissions[{{ $roleSlug }}][]" value="{{ $permission->key }}"
                                                    {{ $isGranted ? 'checked' : '' }} {{ $isSuperAdmin ? 'disabled' : '' }}>
                                                <span class="toggle-slider"></span>
                                            </label>
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
                <div class="full-access-note">
                    <i class="fa-solid fa-lock" style="color:var(--pts-black);"></i>
                    Super Admin always has full access and cannot be restricted.
                </div>
            </div>

            <div class="form-footer">
                <button type="submit" class="btn-primary"><i class="fa-solid fa-check"></i> Save Permissions</button>
            </div>
        </form>
    @endif

@endsection