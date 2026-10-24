@extends('layouts.app')

@section('title', $settingsTitle ?? 'Settings')
@section('page-title', 'Settings')

@section('styles')
    <style>
        .settings-panel {
            background: var(--pts-white);
            border: 1px solid var(--pts-grey-light);
            border-radius: 14px;
            padding: 28px 32px;
        }

        .settings-panel__title {
            font-size: 18px;
            font-weight: 800;
            color: #111827;
            margin-bottom: 4px;
        }

        .settings-panel__sub {
            font-size: 13px;
            color: var(--pts-grey);
            margin-bottom: 24px;
        }

        /* ---------- Category sections ---------- */
        .settings-section {
            margin-bottom: 28px;
        }

        .settings-section:last-child {
            margin-bottom: 0;
        }

        .settings-section__title {
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--pts-green-dark);
            margin-bottom: 12px;
            padding-bottom: 8px;
            border-bottom: 1px solid var(--pts-grey-light);
        }

        /* ---------- Compact 3-column card grid ---------- */
        .settings-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
        }

        .settings-card {
            background: var(--pts-white);
            border: 1px solid var(--pts-grey-light);
            border-radius: 11px;
            padding: 12px 14px;
            text-decoration: none;
            color: inherit;
            display: flex;
            flex-direction: column;
            gap: 8px;
            min-height: 92px;
            transition: border-color 0.15s, transform 0.15s, box-shadow 0.15s;
            position: relative;
        }

        .settings-card:hover {
            border-color: var(--pts-green);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(122, 193, 66, 0.12);
        }

        .settings-card__head {
            display: flex;
            align-items: center;
            gap: 11px;
        }

        .settings-card__icon-wrap {
            width: 42px;
            height: 42px;
            border-radius: 10px;
            background: var(--pts-green-light);
            color: var(--pts-green-dark);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 19px;
            flex-shrink: 0;
        }

        .settings-card__title {
            font-size: 13px;
            font-weight: 700;
            color: #111827;
            line-height: 1.25;
        }

        .settings-card__desc {
            font-size: 11.5px;
            color: var(--pts-grey);
            line-height: 1.4;
            flex: 1;
        }

        .settings-card__footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-top: 8px;
            border-top: 1px solid var(--pts-grey-light);
            margin-top: auto;
        }

        .settings-card__status {
            font-size: 10.5px;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 20px;
        }

        .settings-card__status.ok {
            background: var(--pts-green-light);
            color: var(--pts-green-dark);
        }

        .settings-card__status.warn {
            background: #FFFBEB;
            color: #D97706;
        }

        .settings-card__status.todo {
            background: #FEF2F2;
            color: #DC2626;
        }

        .settings-card__arrow {
            font-size: 11px;
            color: var(--pts-grey);
            transition: transform 0.15s, color 0.15s;
        }

        .settings-card:hover .settings-card__arrow {
            color: var(--pts-green-dark);
            transform: translateX(3px);
        }

        /* ---------- Back link ---------- */
        .settings-back {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            color: var(--pts-grey);
            text-decoration: none;
            font-weight: 600;
            margin-bottom: 18px;
        }

        .settings-back:hover {
            color: var(--pts-green-dark);
        }

        /* ---------- Banner ---------- */
        .settings-banner {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 14px 18px;
            background: var(--pts-green-light);
            border-left: 4px solid var(--pts-green);
            border-radius: 10px;
            margin-bottom: 22px;
        }

        .settings-banner i {
            color: var(--pts-green-dark);
            font-size: 16px;
        }

        .settings-banner__text {
            font-size: 13px;
            color: #1F2937;
            line-height: 1.5;
        }

        .settings-banner__text strong {
            color: var(--pts-green-dark);
        }

        /* ---------- Placeholder ---------- */
        .settings-placeholder {
            padding: 50px 20px;
            text-align: center;
            color: var(--pts-grey);
            font-size: 13.5px;
        }

        .settings-placeholder i {
            font-size: 26px;
            color: var(--pts-grey-light);
            display: block;
            margin-bottom: 14px;
        }

        .settings-placeholder code {
            background: var(--pts-bg);
            padding: 2px 6px;
            border-radius: 5px;
            font-size: 12px;
        }

        /* ---------- Responsive ---------- */
        @media (max-width: 1200px) {
            .settings-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 700px) {
            .settings-grid {
                grid-template-columns: 1fr;
            }

            .settings-panel {
                padding: 20px;
            }
        }
    </style>
@endsection

@section('content')

    @if (session('success'))
        <div style="background:var(--pts-green-light); color:var(--pts-green-dark); border:1px solid var(--pts-green);
                            padding:12px 16px; border-radius:9px; font-size:13px; margin-bottom:18px;">
            {{ session('success') }}
        </div>
    @endif

    <div class="settings-panel">
        @yield('settings-content')
    </div>

@endsection