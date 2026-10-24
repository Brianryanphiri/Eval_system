<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#0F0F0F">
    <meta name="app-version" content="1.0.0">

    <title>@yield('title', 'Pro Tech Solutions') | Evaluation System</title>

    <link rel="icon" href="{{ asset('images/favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/favicon.png') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    @yield('styles')

    <style>
        :root {
            --pts-black: #0F0F0F;
            --pts-black-soft: #1A1A1A;
            --pts-black-elevated: #222222;
            --pts-green: #7AC142;
            --pts-green-dark: #5E9A31;
            --pts-green-light: #E9F5DD;
            --pts-grey: #6B7280;
            --pts-grey-light: #E5E7EB;
            --pts-bg: #F5F6F8;
            --pts-white: #FFFFFF;
            --pts-red: #DC2626;
            --pts-red-light: #FEF2F2;
            --pts-amber: #D97706;
            --pts-amber-light: #FFFBEB;
            --pts-blue: #2563EB;
            --pts-blue-light: #EFF6FF;
            --sidebar-width: 260px;
            --sidebar-collapsed-width: 72px;
            --topbar-height: 68px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--pts-bg);
            color: #111827;
        }

        /* ============================================================
           SIDEBAR
           ============================================================ */
        .pts-sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: var(--pts-black);
            display: flex;
            flex-direction: column;
            z-index: 40;
            transition: width 0.2s ease, transform 0.25s ease;
        }

        .pts-sidebar__brand {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 22px 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            flex-shrink: 0;
        }

        .pts-sidebar__brand-icon {
            width: 36px;
            height: 36px;
            border-radius: 9px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .pts-sidebar__brand-text {
            color: #fff;
            font-weight: 700;
            font-size: 15px;
            line-height: 1.1;
            min-width: 0;
        }

        .pts-sidebar__brand-text span {
            display: block;
            font-weight: 400;
            font-size: 11px;
            color: var(--pts-grey);
            letter-spacing: 0.06em;
            margin-top: 2px;
        }

        .pts-nav {
            flex: 1;
            padding: 14px 10px;
            overflow-y: auto;
            overflow-x: hidden;
        }

        .pts-nav::-webkit-scrollbar {
            width: 6px;
        }

        .pts-nav::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.12);
            border-radius: 3px;
        }

        .pts-nav::-webkit-scrollbar-track {
            background: transparent;
        }

        /* Sidebar search */
        .pts-nav__search {
            position: relative;
            margin: 0 4px 12px;
        }

        .pts-nav__search input {
            width: 100%;
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 8px;
            padding: 8px 10px 8px 32px;
            color: #fff;
            font-size: 12.5px;
            font-family: inherit;
            outline: none;
            transition: all 0.15s;
        }

        .pts-nav__search input::placeholder {
            color: #6B7280;
        }

        .pts-nav__search input:focus {
            border-color: var(--pts-green);
            background: rgba(122, 193, 66, 0.08);
        }

        .pts-nav__search i {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: #6B7280;
            font-size: 12px;
            pointer-events: none;
        }

        /* Collapsible sections */
        .pts-nav__section {
            font-size: 10.5px;
            text-transform: uppercase;
            letter-spacing: 0.09em;
            color: #6B7280;
            margin: 16px 10px 6px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            cursor: pointer;
            user-select: none;
        }

        .pts-nav__section:hover {
            color: #9CA3AF;
        }

        .pts-nav__section i {
            font-size: 9px;
            transition: transform 0.15s;
        }

        .pts-nav__section.collapsed i {
            transform: rotate(-90deg);
        }

        .pts-nav__items {
            overflow: hidden;
            transition: max-height 0.2s ease;
        }

        .pts-nav__items.collapsed {
            max-height: 0 !important;
        }

        .pts-nav__link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 9px 12px;
            border-radius: 8px;
            color: #C9CBCF;
            text-decoration: none;
            font-size: 13.5px;
            font-weight: 500;
            margin-bottom: 1px;
            transition: background 0.15s, color 0.15s;
            position: relative;
        }

        .pts-nav__link i.pts-nav__icon {
            width: 18px;
            text-align: center;
            font-size: 14px;
            flex-shrink: 0;
        }

        .pts-nav__link .pts-nav__label {
            flex: 1;
            min-width: 0;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .pts-nav__link .pts-nav__badge {
            font-size: 10px;
            font-weight: 700;
            padding: 2px 7px;
            border-radius: 10px;
            background: rgba(122, 193, 66, 0.2);
            color: var(--pts-green);
            flex-shrink: 0;
        }

        .pts-nav__link .pts-nav__badge.red {
            background: rgba(220, 38, 38, 0.2);
            color: #F87171;
        }

        .pts-nav__link .pts-nav__badge.amber {
            background: rgba(217, 119, 6, 0.2);
            color: #FBBF24;
        }

        .pts-nav__link:hover {
            background: rgba(255, 255, 255, 0.06);
            color: #fff;
        }

        .pts-nav__link.active {
            background: var(--pts-green);
            color: var(--pts-black);
            font-weight: 600;
        }

        .pts-nav__link.active::before {
            content: '';
            position: absolute;
            left: -10px;
            top: 50%;
            transform: translateY(-50%);
            width: 3px;
            height: 60%;
            background: var(--pts-green);
            border-radius: 0 3px 3px 0;
        }

        .pts-nav__link.active .pts-nav__badge {
            background: rgba(15, 15, 15, 0.15);
            color: var(--pts-black);
        }

        .pts-nav__link.hidden-by-search {
            display: none;
        }

        .pts-sidebar__footer {
            padding: 14px 18px;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
            display: flex;
            align-items: center;
            gap: 10px;
            flex-shrink: 0;
        }

        .pts-sidebar__avatar {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: var(--pts-green);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--pts-black);
            font-weight: 700;
            font-size: 13px;
            flex-shrink: 0;
        }

        .pts-sidebar__user-info {
            min-width: 0;
            flex: 1;
        }

        .pts-sidebar__user-name {
            color: #fff;
            font-size: 13px;
            font-weight: 600;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .pts-sidebar__user-role {
            color: var(--pts-grey);
            font-size: 11px;
            text-transform: capitalize;
        }

        /* ---------- Desktop collapsed sidebar ---------- */
        .pts-sidebar.collapsed {
            width: var(--sidebar-collapsed-width);
        }

        .pts-sidebar.collapsed .pts-sidebar__brand-text,
        .pts-sidebar.collapsed .pts-nav__search,
        .pts-sidebar.collapsed .pts-nav__section,
        .pts-sidebar.collapsed .pts-nav__label,
        .pts-sidebar.collapsed .pts-nav__badge,
        .pts-sidebar.collapsed .pts-sidebar__user-info {
            display: none;
        }

        .pts-sidebar.collapsed .pts-nav__link {
            justify-content: center;
            padding: 10px;
        }

        .pts-sidebar.collapsed .pts-nav__link i.pts-nav__icon {
            font-size: 17px;
        }

        .pts-sidebar.collapsed .pts-sidebar__brand {
            justify-content: center;
            padding: 22px 10px;
        }

        .pts-sidebar.collapsed .pts-sidebar__footer {
            justify-content: center;
            padding: 14px 10px;
        }

        .pts-main.sidebar-collapsed {
            margin-left: var(--sidebar-collapsed-width);
        }

        /* ============================================================
           MAIN AREA
           ============================================================ */
        .pts-main {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            transition: margin-left 0.2s ease;
        }

        .pts-topbar {
            height: var(--topbar-height);
            background: var(--pts-white);
            border-bottom: 1px solid var(--pts-grey-light);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 24px;
            position: sticky;
            top: 0;
            z-index: 30;
            gap: 16px;
        }

        .pts-topbar__left {
            display: flex;
            align-items: center;
            gap: 14px;
            min-width: 0;
            flex: 1;
        }

        .pts-topbar__toggle {
            width: 38px;
            height: 38px;
            border-radius: 8px;
            background: transparent;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--pts-grey);
            cursor: pointer;
            font-size: 16px;
            flex-shrink: 0;
        }

        .pts-topbar__toggle:hover {
            background: var(--pts-bg);
        }

        .pts-topbar__title-wrap {
            min-width: 0;
        }

        .pts-topbar__title {
            font-size: 17px;
            font-weight: 700;
            color: #111827;
            line-height: 1.2;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .pts-topbar__breadcrumbs {
            font-size: 11.5px;
            color: var(--pts-grey);
            margin-top: 2px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .pts-topbar__breadcrumbs a {
            color: var(--pts-grey);
            text-decoration: none;
        }

        .pts-topbar__breadcrumbs a:hover {
            color: var(--pts-green-dark);
        }

        .pts-topbar__breadcrumbs .sep {
            color: #D1D5DB;
        }

        .pts-topbar__right {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-shrink: 0;
        }

        /* Global search */
        .pts-topbar__search {
            position: relative;
            width: 240px;
        }

        .pts-topbar__search input {
            width: 100%;
            background: var(--pts-bg);
            border: 1px solid transparent;
            border-radius: 8px;
            padding: 8px 12px 8px 34px;
            font-size: 13px;
            font-family: inherit;
            outline: none;
            transition: all 0.15s;
            color: #111827;
        }

        .pts-topbar__search input:focus {
            background: #fff;
            border-color: var(--pts-green);
        }

        .pts-topbar__search i {
            position: absolute;
            left: 11px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--pts-grey);
            font-size: 12px;
            pointer-events: none;
        }

        .pts-topbar__search kbd {
            position: absolute;
            right: 8px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 10px;
            color: var(--pts-grey);
            background: #fff;
            border: 1px solid var(--pts-grey-light);
            border-radius: 4px;
            padding: 1px 5px;
            font-family: inherit;
            pointer-events: none;
        }

        /* Term selector */
        .pts-term-pill {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 7px 12px;
            background: var(--pts-green-light);
            border-radius: 8px;
            font-size: 12.5px;
            font-weight: 600;
            color: var(--pts-green-dark);
            white-space: nowrap;
            cursor: pointer;
        }

        .pts-term-pill i {
            font-size: 11px;
        }

        /* Icon buttons */
        .pts-topbar__icon-btn {
            width: 38px;
            height: 38px;
            border-radius: 8px;
            background: var(--pts-bg);
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--pts-grey);
            cursor: pointer;
            font-size: 14px;
            position: relative;
            transition: all 0.15s;
        }

        .pts-topbar__icon-btn:hover {
            background: var(--pts-green-light);
            color: var(--pts-green-dark);
        }

        .pts-topbar__icon-btn .dot {
            position: absolute;
            top: 7px;
            right: 7px;
            width: 8px;
            height: 8px;
            background: var(--pts-red);
            border-radius: 50%;
            border: 2px solid var(--pts-white);
        }

        .pts-topbar__icon-btn .count {
            position: absolute;
            top: -4px;
            right: -4px;
            min-width: 18px;
            height: 18px;
            padding: 0 5px;
            background: var(--pts-red);
            color: #fff;
            border-radius: 9px;
            font-size: 10px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid var(--pts-white);
        }

        /* Dropdowns */
        .pts-dropdown {
            position: relative;
        }

        .pts-dropdown__menu {
            position: absolute;
            top: calc(100% + 8px);
            right: 0;
            min-width: 220px;
            background: var(--pts-white);
            border: 1px solid var(--pts-grey-light);
            border-radius: 12px;
            padding: 6px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            opacity: 0;
            visibility: hidden;
            transform: translateY(-4px);
            transition: all 0.15s ease;
            z-index: 50;
        }

        .pts-dropdown.open .pts-dropdown__menu {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .pts-dropdown__item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px 12px;
            border-radius: 8px;
            color: #111827;
            text-decoration: none;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            border: none;
            background: none;
            width: 100%;
            text-align: left;
            font-family: inherit;
        }

        .pts-dropdown__item:hover {
            background: var(--pts-bg);
        }

        .pts-dropdown__item i {
            width: 16px;
            text-align: center;
            font-size: 13px;
            color: var(--pts-grey);
        }

        .pts-dropdown__divider {
            height: 1px;
            background: var(--pts-grey-light);
            margin: 6px 4px;
        }

        .pts-dropdown__header {
            padding: 10px 12px 6px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--pts-grey);
        }

        /* Quick create button */
        .pts-quick-create {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 8px 14px;
            background: var(--pts-green);
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 12.5px;
            font-weight: 600;
            cursor: pointer;
            font-family: inherit;
            transition: all 0.15s;
        }

        .pts-quick-create:hover {
            background: var(--pts-green-dark);
        }

        .pts-quick-create i {
            font-size: 11px;
        }

        /* Notification panel */
        .pts-notif-panel {
            min-width: 340px;
            padding: 0;
        }

        .pts-notif-panel__head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 14px 16px;
            border-bottom: 1px solid var(--pts-grey-light);
        }

        .pts-notif-panel__title {
            font-size: 13px;
            font-weight: 700;
            color: #111827;
        }

        .pts-notif-panel__mark {
            font-size: 11.5px;
            font-weight: 600;
            color: var(--pts-green-dark);
            cursor: pointer;
            background: none;
            border: none;
            font-family: inherit;
            text-decoration: none;
        }

        .pts-notif-panel__list {
            max-height: 320px;
            overflow-y: auto;
        }

        .pts-notif-item {
            display: flex;
            gap: 12px;
            padding: 12px 16px;
            border-bottom: 1px solid var(--pts-grey-light);
            text-decoration: none;
            color: inherit;
        }

        .pts-notif-item:hover {
            background: var(--pts-bg);
        }

        .pts-notif-item:last-child {
            border-bottom: none;
        }

        .pts-notif-item__icon {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            background: var(--pts-green-light);
            color: var(--pts-green-dark);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            flex-shrink: 0;
        }

        .pts-notif-item__text {
            font-size: 12.5px;
            color: #111827;
            line-height: 1.4;
        }

        .pts-notif-item__time {
            font-size: 11px;
            color: var(--pts-grey);
            margin-top: 2px;
        }

        .pts-notif-panel__empty {
            padding: 32px 16px;
            text-align: center;
            color: var(--pts-grey);
            font-size: 12.5px;
        }

        /* Page actions slot (topbar right) */
        .pts-page-actions {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* ============================================================
           CONTENT
           ============================================================ */
        .pts-content {
            padding: 24px 28px;
            flex: 1;
        }

        /* Flash messages */
        .pts-flash {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 500;
            margin-bottom: 16px;
            border: 1px solid;
            position: relative;
        }

        .pts-flash.success {
            background: var(--pts-green-light);
            border-color: rgba(122, 193, 66, 0.3);
            color: var(--pts-green-dark);
        }

        .pts-flash.error {
            background: var(--pts-red-light);
            border-color: rgba(220, 38, 38, 0.2);
            color: var(--pts-red);
        }

        .pts-flash.warning {
            background: var(--pts-amber-light);
            border-color: rgba(217, 119, 6, 0.2);
            color: var(--pts-amber);
        }

        .pts-flash.info {
            background: var(--pts-blue-light);
            border-color: rgba(37, 99, 235, 0.2);
            color: var(--pts-blue);
        }

        .pts-flash i {
            font-size: 15px;
        }

        .pts-flash__close {
            margin-left: auto;
            background: none;
            border: none;
            cursor: pointer;
            color: inherit;
            opacity: 0.6;
            font-size: 14px;
            padding: 2px 6px;
            font-family: inherit;
        }

        .pts-flash__close:hover {
            opacity: 1;
        }

        /* ============================================================
           FOOTER
           ============================================================ */
        .pts-footer {
            border-top: 1px solid var(--pts-grey-light);
            background: var(--pts-white);
            padding: 16px 28px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 12px;
            color: var(--pts-grey);
            gap: 16px;
            flex-wrap: wrap;
        }

        .pts-footer__links {
            display: flex;
            align-items: center;
            gap: 18px;
            flex-wrap: wrap;
        }

        .pts-footer__links a {
            color: var(--pts-grey);
            text-decoration: none;
        }

        .pts-footer__links a:hover {
            color: var(--pts-green-dark);
        }

        .pts-footer__right {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .pts-footer__status {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .pts-footer__status .dot {
            width: 7px;
            height: 7px;
            background: var(--pts-green);
            border-radius: 50%;
        }

        .pts-footer kbd {
            font-size: 10.5px;
            background: var(--pts-bg);
            border: 1px solid var(--pts-grey-light);
            border-radius: 4px;
            padding: 1px 5px;
            font-family: inherit;
        }

        /* ============================================================
           TOASTS + MODALS
           ============================================================ */
        .pts-toasts {
            position: fixed;
            bottom: 24px;
            right: 24px;
            display: flex;
            flex-direction: column;
            gap: 10px;
            z-index: 100;
            pointer-events: none;
        }

        .pts-toast {
            min-width: 260px;
            max-width: 380px;
            background: var(--pts-black);
            color: #fff;
            border-radius: 10px;
            padding: 12px 16px;
            font-size: 13px;
            display: flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.25);
            pointer-events: auto;
            animation: toast-in 0.2s ease;
        }

        .pts-toast.success i {
            color: var(--pts-green);
        }

        .pts-toast.error i {
            color: #F87171;
        }

        .pts-toast.warning i {
            color: #FBBF24;
        }

        @keyframes toast-in {
            from {
                opacity: 0;
                transform: translateY(12px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Confirm dialog */
        .pts-confirm {
            position: fixed;
            inset: 0;
            background: rgba(15, 15, 15, 0.5);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 200;
            padding: 20px;
        }

        .pts-confirm.open {
            display: flex;
        }

        .pts-confirm__box {
            background: var(--pts-white);
            border-radius: 14px;
            padding: 24px;
            max-width: 420px;
            width: 100%;
            text-align: center;
        }

        .pts-confirm__icon {
            width: 52px;
            height: 52px;
            border-radius: 50%;
            background: var(--pts-red-light);
            color: var(--pts-red);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            margin: 0 auto 14px;
        }

        .pts-confirm__title {
            font-size: 16px;
            font-weight: 700;
            color: #111827;
            margin-bottom: 6px;
        }

        .pts-confirm__text {
            font-size: 13px;
            color: var(--pts-grey);
            margin-bottom: 20px;
            line-height: 1.5;
        }

        .pts-confirm__actions {
            display: flex;
            gap: 10px;
            justify-content: center;
        }

        .pts-confirm__btn {
            padding: 10px 20px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            border: none;
            font-family: inherit;
        }

        .pts-confirm__btn.cancel {
            background: var(--pts-bg);
            color: #111827;
        }

        .pts-confirm__btn.danger {
            background: var(--pts-red);
            color: #fff;
        }

        /* ============================================================
           RESPONSIVE
           ============================================================ */
        .pts-sidebar__overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 39;
        }

        @media (max-width: 1024px) {
            .pts-topbar__search {
                width: 180px;
            }
        }

        @media (max-width: 900px) {
            .pts-sidebar {
                transform: translateX(-100%);
            }

            .pts-sidebar.mobile-open {
                transform: translateX(0);
            }

            .pts-sidebar.collapsed {
                width: var(--sidebar-width);
            }

            .pts-main,
            .pts-main.sidebar-collapsed {
                margin-left: 0;
            }

            .pts-sidebar__overlay.active {
                display: block;
            }

            .pts-topbar__search {
                display: none;
            }

            .pts-term-pill {
                display: none;
            }
        }

        @media (max-width: 640px) {
            .pts-content {
                padding: 18px 16px;
            }

            .pts-topbar {
                padding: 0 16px;
            }

            .pts-footer {
                padding: 14px 16px;
                flex-direction: column;
                align-items: flex-start;
            }

            .pts-quick-create span {
                display: none;
            }
        }
    </style>
</head>

<body>

    @php
        $user = auth()->user();
        $isAdmin = $user && in_array($user->role, ['school_admin', 'super_admin']);
        $school = $user?->school;
        $currentYear = \App\Models\AcademicYear::where('school_id', $school?->id)->where('is_current', true)->first();
        $currentTerm = $currentYear
            ? \App\Models\Term::where('academic_year_id', $currentYear->id)->where('is_current', true)->first()
            : null;

        // Learning flag count for sidebar badge (safe fallback if table doesn't exist yet)
        $flagCount = 0;
        if ($user && $school && $currentTerm) {
            try {
                $flagCount = \App\Models\StudentLearningProfile::where('school_id', $school->id)
                    ->where('term_id', $currentTerm->id)
                    ->whereIn('flag', ['at_risk', 'needs_support'])
                    ->count();
            } catch (\Throwable $e) {
                $flagCount = 0;
            }
        }

        // Announcements: unread count for the bell + recent list for the panel
        $announcementCount = 0;
        $recentAnnouncements = collect();
        if ($user && $school) {
            try {
                $announcementCount = \App\Models\Announcement::where('school_id', $school->id)
                    ->published()
                    ->active()
                    ->visibleTo($user->role)
                    ->count();

                $recentAnnouncements = \App\Models\Announcement::with('author')
                    ->where('school_id', $school->id)
                    ->published()
                    ->active()
                    ->visibleTo($user->role)
                    ->byImportance()
                    ->limit(5)
                    ->get();
            } catch (\Throwable $e) {
                $announcementCount = 0;
                $recentAnnouncements = collect();
            }
        }
    @endphp

    {{-- Sidebar --}}
    <aside class="pts-sidebar" id="ptsSidebar">
        <div class="pts-sidebar__brand">
            <div class="pts-sidebar__brand-icon">
                @php
                    $schoolLogo = $school?->logo_path
                        ? \Storage::disk('public')->url($school->logo_path)
                        : asset('images/logo.png');
                @endphp
                <img src="{{ $schoolLogo }}" alt="{{ $school->name ?? 'Pro Tech Solutions' }}"
                    style="width: 100%; height: 100%; object-fit: contain; border-radius: 9px;">
            </div>
            <div class="pts-sidebar__brand-text">
                {{ $school->name ?? 'Pro Tech Solutions' }}
                <span>EVALUATION SYSTEM</span>
            </div>
        </div>

        <nav class="pts-nav">
            {{-- Sidebar search --}}
            <div class="pts-nav__search">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" id="navSearch" placeholder="Search menu…" autocomplete="off">
            </div>

            {{-- MAIN --}}
            <div class="pts-nav__section" data-section="main">
                Main
                <i class="fa-solid fa-chevron-down"></i>
            </div>
            <div class="pts-nav__items" data-items="main">
                <a href="{{ route('dashboard') }}"
                    class="pts-nav__link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="fa-solid fa-gauge pts-nav__icon"></i>
                    <span class="pts-nav__label">Dashboard</span>
                </a>
                <a href="{{ route('students.index') }}"
                    class="pts-nav__link {{ request()->routeIs('students.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-users pts-nav__icon"></i>
                    <span class="pts-nav__label">Students</span>
                </a>
                <a href="{{ route('classes.overview') }}"
                    class="pts-nav__link {{ request()->routeIs('classes.overview') ? 'active' : '' }}">
                    <i class="fa-solid fa-chalkboard pts-nav__icon"></i>
                    <span class="pts-nav__label">Classes Overview</span>
                </a>
                @if ($isAdmin)
                    <a href="{{ route('academic.index') }}"
                        class="pts-nav__link {{ request()->routeIs('academic.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-chalkboard-user pts-nav__icon"></i>
                        <span class="pts-nav__label">Classes & Subjects</span>
                    </a>
                @endif
            </div>

            {{-- COMMUNICATION --}}
            <div class="pts-nav__section" data-section="communication">
                Communication
                <i class="fa-solid fa-chevron-down"></i>
            </div>
            <div class="pts-nav__items" data-items="communication">
                <a href="{{ route('announcements.index') }}"
                    class="pts-nav__link {{ request()->routeIs('announcements.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-bullhorn pts-nav__icon"></i>
                    <span class="pts-nav__label">Announcements</span>
                    @if ($announcementCount > 0)
                        <span class="pts-nav__badge">{{ $announcementCount }}</span>
                    @endif
                </a>
                @can('communications.send')
                    <a href="{{ route('communications.index') }}"
                        class="pts-nav__link {{ request()->routeIs('communications.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-paper-plane pts-nav__icon"></i>
                        <span class="pts-nav__label">Communications</span>
                    </a>
                @endcan
            </div>

            {{-- ASSESSMENT --}}
            <div class="pts-nav__section" data-section="assessment">
                Assessment
                <i class="fa-solid fa-chevron-down"></i>
            </div>
            <div class="pts-nav__items" data-items="assessment">
                <a href="{{ route('assessments.index') }}"
                    class="pts-nav__link {{ request()->routeIs('assessments.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-file-pen pts-nav__icon"></i>
                    <span class="pts-nav__label">Assessments</span>
                </a>
                <a href="{{ route('marks.entry') }}"
                    class="pts-nav__link {{ request()->routeIs('marks.entry*') || request()->routeIs('marking.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-keyboard pts-nav__icon"></i>
                    <span class="pts-nav__label">Marks Entry</span>
                </a>
                <a href="{{ route('timetable.index') }}"
                    class="pts-nav__link {{ request()->routeIs('timetable.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-table-cells-large pts-nav__icon"></i>
                    <span class="pts-nav__label">Timetable</span>
                </a>
                <a href="{{ route('interventions.index') }}"
                    class="pts-nav__link {{ request()->routeIs('interventions.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-hand-holding-heart pts-nav__icon"></i>
                    <span class="pts-nav__label">Interventions</span>
                </a>
            </div>

            {{-- ATTENDANCE --}}
            <div class="pts-nav__section" data-section="attendance">
                Attendance
                <i class="fa-solid fa-chevron-down"></i>
            </div>
            <div class="pts-nav__items" data-items="attendance">
                <a href="{{ route('attendance.index') }}"
                    class="pts-nav__link {{ request()->routeIs('attendance.index') || request()->routeIs('attendance.store') ? 'active' : '' }}">
                    <i class="fa-solid fa-clipboard-check pts-nav__icon"></i>
                    <span class="pts-nav__label">Take Attendance</span>
                </a>
                <a href="{{ route('attendance.summary') }}"
                    class="pts-nav__link {{ request()->routeIs('attendance.summary') ? 'active' : '' }}">
                    <i class="fa-solid fa-calendar-check pts-nav__icon"></i>
                    <span class="pts-nav__label">Attendance Summary</span>
                </a>
                @if ($isAdmin)
                    <a href="{{ route('attendance.reasons') }}"
                        class="pts-nav__link {{ request()->routeIs('attendance.reasons') ? 'active' : '' }}">
                        <i class="fa-solid fa-list-check pts-nav__icon"></i>
                        <span class="pts-nav__label">Absence Reasons</span>
                    </a>
                @endif
            </div>

            {{-- LEARNING --}}
            <div class="pts-nav__section" data-section="learning">
                Learning
                <i class="fa-solid fa-chevron-down"></i>
            </div>
            <div class="pts-nav__items" data-items="learning">
                <a href="{{ route('learning.flags') }}"
                    class="pts-nav__link {{ request()->routeIs('learning.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-brain pts-nav__icon"></i>
                    <span class="pts-nav__label">Learning Flags</span>
                    @if ($flagCount > 0)
                        <span class="pts-nav__badge amber">{{ $flagCount }}</span>
                    @endif
                </a>
            </div>

            {{-- RESULTS --}}
            <div class="pts-nav__section" data-section="results">
                Results
                <i class="fa-solid fa-chevron-down"></i>
            </div>
            <div class="pts-nav__items" data-items="results">
                <a href="{{ route('reports.index') }}"
                    class="pts-nav__link {{ request()->routeIs('reports.index') ? 'active' : '' }}">
                    <i class="fa-solid fa-file-lines pts-nav__icon"></i>
                    <span class="pts-nav__label">Reports</span>
                </a>
                <a href="{{ route('reports.broadsheet') }}"
                    class="pts-nav__link {{ request()->routeIs('reports.broadsheet') ? 'active' : '' }}">
                    <i class="fa-solid fa-table-cells pts-nav__icon"></i>
                    <span class="pts-nav__label">Broadsheet</span>
                </a>
                <a href="{{ route('reports.rankings') }}"
                    class="pts-nav__link {{ request()->routeIs('reports.rankings') ? 'active' : '' }}">
                    <i class="fa-solid fa-ranking-star pts-nav__icon"></i>
                    <span class="pts-nav__label">Rankings</span>
                </a>
                <a href="{{ route('reports.merit') }}"
                    class="pts-nav__link {{ request()->routeIs('reports.merit') ? 'active' : '' }}">
                    <i class="fa-solid fa-award pts-nav__icon"></i>
                    <span class="pts-nav__label">Merit List</span>
                </a>
                <a href="{{ route('reports.pass-list') }}"
                    class="pts-nav__link {{ request()->routeIs('reports.pass-list') ? 'active' : '' }}">
                    <i class="fa-solid fa-circle-check pts-nav__icon"></i>
                    <span class="pts-nav__label">Pass List</span>
                </a>
                <a href="{{ route('reports.grade-analysis') }}"
                    class="pts-nav__link {{ request()->routeIs('reports.grade-analysis') ? 'active' : '' }}">
                    <i class="fa-solid fa-chart-column pts-nav__icon"></i>
                    <span class="pts-nav__label">Grade Analysis</span>
                </a>
                <a href="{{ route('reports.subject-performance') }}"
                    class="pts-nav__link {{ request()->routeIs('reports.subject-performance') ? 'active' : '' }}">
                    <i class="fa-solid fa-book-open pts-nav__icon"></i>
                    <span class="pts-nav__label">Subject Performance</span>
                </a>
                <a href="{{ route('reports.statistics') }}"
                    class="pts-nav__link {{ request()->routeIs('reports.statistics') ? 'active' : '' }}">
                    <i class="fa-solid fa-calculator pts-nav__icon"></i>
                    <span class="pts-nav__label">Statistics</span>
                </a>
                <a href="{{ route('reports.export') }}"
                    class="pts-nav__link {{ request()->routeIs('reports.export') ? 'active' : '' }}">
                    <i class="fa-solid fa-download pts-nav__icon"></i>
                    <span class="pts-nav__label">Export Center</span>
                </a>
            </div>

            {{-- SYSTEM --}}
            @if ($isAdmin)
                <div class="pts-nav__section" data-section="system">
                    System
                    <i class="fa-solid fa-chevron-down"></i>
                </div>
                <div class="pts-nav__items" data-items="system">
                    <a href="{{ route('staff.index') }}"
                        class="pts-nav__link {{ request()->routeIs('staff.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-user-tie pts-nav__icon"></i>
                        <span class="pts-nav__label">Staff</span>
                    </a>
                    <a href="{{ route('settings.overview') }}"
                        class="pts-nav__link {{ request()->routeIs('settings.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-sliders pts-nav__icon"></i>
                        <span class="pts-nav__label">Settings</span>
                    </a>
                    <a href="{{ route('settings.audit') }}"
                        class="pts-nav__link {{ request()->routeIs('settings.audit') ? 'active' : '' }}">
                        <i class="fa-solid fa-clipboard-list pts-nav__icon"></i>
                        <span class="pts-nav__label">Audit Log</span>
                    </a>
                </div>
            @endif
        </nav>

        <div class="pts-sidebar__footer">
            <div class="pts-sidebar__avatar" style="overflow:hidden;">
                @if ($user && $user->avatar_url)
                    <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}"
                        style="width:100%; height:100%; object-fit:cover;">
                @else
                    {{ $user ? strtoupper(substr($user->name, 0, 1)) : 'U' }}
                @endif
            </div>
            <div class="pts-sidebar__user-info">
                <div class="pts-sidebar__user-name">{{ $user->name ?? 'Guest User' }}</div>
                <div class="pts-sidebar__user-role">{{ str_replace('_', ' ', $user->role ?? 'Not signed in') }}</div>
            </div>
        </div>
    </aside>

    <div class="pts-sidebar__overlay" id="sidebarOverlay"></div>

    {{-- Main --}}
    <div class="pts-main">

        {{-- Topbar --}}
        <header class="pts-topbar">
            <div class="pts-topbar__left">
                <button class="pts-topbar__toggle" id="sidebarToggle" title="Toggle sidebar">
                    <i class="fa-solid fa-bars"></i>
                </button>
                <div class="pts-topbar__title-wrap">
                    <div class="pts-topbar__title">@yield('page-title', 'Dashboard')</div>
                    @hasSection('breadcrumbs')
                        <div class="pts-topbar__breadcrumbs">
                            @yield('breadcrumbs')
                        </div>
                    @endif
                </div>
            </div>

            <div class="pts-topbar__right">

                {{-- Global search --}}
                <div class="pts-topbar__search">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" placeholder="Search students, exams…" id="globalSearch">
                    <kbd>⌘K</kbd>
                </div>

                {{-- Term pill --}}
                @if ($currentTerm)
                    <div class="pts-term-pill" title="Current term">
                        <i class="fa-regular fa-calendar"></i>
                        {{ $currentTerm->name ?? 'Term' }} · {{ $currentYear->label ?? '' }}
                    </div>
                @endif

                {{-- Page actions slot --}}
                @hasSection('page-actions')
                    <div class="pts-page-actions">
                        @yield('page-actions')
                    </div>
                @endif

                {{-- Quick create --}}
                @canany(['students.edit', 'assessment.create'])
                    <div class="pts-dropdown" data-dropdown>
                        <button class="pts-quick-create" data-dropdown-toggle>
                            <i class="fa-solid fa-plus"></i>
                            <span>New</span>
                        </button>
                        <div class="pts-dropdown__menu">
                            @can('students.edit')
                                <a href="{{ route('students.create') }}" class="pts-dropdown__item">
                                    <i class="fa-solid fa-user-plus"></i> Add Student
                                </a>
                            @endcan
                            @can('assessment.create')
                                <a href="{{ route('assessments.create') }}" class="pts-dropdown__item">
                                    <i class="fa-solid fa-file-circle-plus"></i> New Assessment
                                </a>
                            @endcan
                            @if ($isAdmin)
                                <a href="{{ route('staff.create') }}" class="pts-dropdown__item">
                                    <i class="fa-solid fa-user-tie"></i> Add Staff
                                </a>
                                <div class="pts-dropdown__divider"></div>
                                <a href="{{ route('students.import') }}" class="pts-dropdown__item">
                                    <i class="fa-solid fa-upload"></i> Import Students
                                </a>
                            @endif
                        </div>
                    </div>
                @endcanany

                {{-- Notifications (bell) --}}
                <div class="pts-dropdown" data-dropdown>
                    <button class="pts-topbar__icon-btn" data-dropdown-toggle title="Notifications">
                        <i class="fa-regular fa-bell"></i>
                        @if ($announcementCount > 0)
                            <span class="count">{{ $announcementCount }}</span>
                        @endif
                    </button>
                    <div class="pts-dropdown__menu pts-notif-panel">
                        <div class="pts-notif-panel__head">
                            <div class="pts-notif-panel__title">Announcements</div>
                            <a href="{{ route('announcements.index') }}" class="pts-notif-panel__mark">View all</a>
                        </div>
                        <div class="pts-notif-panel__list">
                            @forelse ($recentAnnouncements as $ann)
                                <a href="{{ route('announcements.show', $ann->id) }}" class="pts-notif-item">
                                    <div class="pts-notif-item__icon">
                                        <i
                                            class="fa-solid {{ $ann->priority === 'urgent' ? 'fa-triangle-exclamation' : 'fa-bullhorn' }}"></i>
                                    </div>
                                    <div>
                                        <div class="pts-notif-item__text">{{ $ann->title }}</div>
                                        <div class="pts-notif-item__time">
                                            {{ $ann->author->name ?? 'Admin' }} ·
                                            {{ $ann->publish_at ? $ann->publish_at->diffForHumans() : $ann->created_at->diffForHumans() }}
                                        </div>
                                    </div>
                                </a>
                            @empty
                                <div class="pts-notif-panel__empty">
                                    No announcements yet.
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- User menu --}}
                <div class="pts-dropdown" data-dropdown>
                    <button class="pts-topbar__icon-btn" data-dropdown-toggle title="Account">
                        <i class="fa-solid fa-gear"></i>
                    </button>
                    <div class="pts-dropdown__menu">
                        <div class="pts-dropdown__header"
                            style="display:flex; align-items:center; gap:10px; text-transform:none; letter-spacing:0;">
                            @if ($user && $user->avatar_url)
                                <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}"
                                    style="width:28px; height:28px; border-radius:50%; object-fit:cover;">
                            @else
                                <span
                                    style="width:28px; height:28px; border-radius:50%; background:var(--pts-green); color:var(--pts-black); display:inline-flex; align-items:center; justify-content:center; font-size:12px; font-weight:700;">
                                    {{ $user->initials ?? 'U' }}
                                </span>
                            @endif
                            <span
                                style="font-size:12.5px; color:#111827; font-weight:600;">{{ $user->name ?? 'Guest' }}</span>
                        </div>

                        {{-- Personal --}}
                        <a href="{{ route('profile.edit') }}" class="pts-dropdown__item">
                            <i class="fa-solid fa-user"></i> My Profile
                        </a>
                        <a href="{{ route('password.change') }}" class="pts-dropdown__item">
                            <i class="fa-solid fa-key"></i> Change Password
                        </a>

                        {{-- Admin-only --}}
                        @if ($isAdmin)
                            <div class="pts-dropdown__divider"></div>
                            <a href="{{ route('settings.profile') }}" class="pts-dropdown__item">
                                <i class="fa-solid fa-building"></i> School Profile
                            </a>
                            <a href="{{ route('settings.overview') }}" class="pts-dropdown__item">
                                <i class="fa-solid fa-sliders"></i> Settings
                            </a>
                        @endif

                        <div class="pts-dropdown__divider"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="pts-dropdown__item">
                                <i class="fa-solid fa-right-from-bracket"></i> Log out
                            </button>
                        </form>
                    </div>
                </div>

            </div>
        </header>

        {{-- Content --}}
        <main class="pts-content">

            {{-- Flash messages --}}
            @if (session('success'))
                <div class="pts-flash success">
                    <i class="fa-solid fa-circle-check"></i>
                    <span>{{ session('success') }}</span>
                    <button class="pts-flash__close" type="button" onclick="this.parentElement.remove()">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
            @endif

            @if (session('error'))
                <div class="pts-flash error">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <span>{{ session('error') }}</span>
                    <button class="pts-flash__close" type="button" onclick="this.parentElement.remove()">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
            @endif

            @if (session('warning'))
                <div class="pts-flash warning">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    <span>{{ session('warning') }}</span>
                    <button class="pts-flash__close" type="button" onclick="this.parentElement.remove()">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
            @endif

            @if (session('info'))
                <div class="pts-flash info">
                    <i class="fa-solid fa-circle-info"></i>
                    <span>{{ session('info') }}</span>
                    <button class="pts-flash__close" type="button" onclick="this.parentElement.remove()">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
            @endif

            @if ($errors->any())
                <div class="pts-flash error">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <div>
                        <strong>Please fix the following:</strong>
                        <ul style="margin: 4px 0 0 18px;">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    <button class="pts-flash__close" type="button" onclick="this.parentElement.remove()">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
            @endif

            @yield('content')
        </main>

        {{-- Footer --}}
        <footer class="pts-footer">
            <div class="pts-footer__links">
                <span>© {{ date('Y') }} Pro Tech Solutions</span>
                <a href="{{ route('settings.help') }}">Help & Docs</a>
                <a href="{{ route('settings.status') }}">System Status</a>
                <a href="{{ route('settings.changelog') }}">Changelog</a>
            </div>
            <div class="pts-footer__right">
                <div class="pts-footer__status">
                    <span class="dot"></span>
                    <span>All systems operational</span>
                </div>
                <span>v1.0.0</span>
                <span>Press <kbd>⌘K</kbd> to search</span>
            </div>
        </footer>

    </div>

    {{-- Toasts --}}
    <div class="pts-toasts" id="ptsToasts"></div>

    {{-- Confirm dialog --}}
    <div class="pts-confirm" id="ptsConfirm">
        <div class="pts-confirm__box">
            <div class="pts-confirm__icon"><i class="fa-solid fa-triangle-exclamation"></i></div>
            <div class="pts-confirm__title" id="ptsConfirmTitle">Are you sure?</div>
            <div class="pts-confirm__text" id="ptsConfirmText">This action cannot be undone.</div>
            <div class="pts-confirm__actions">
                <button class="pts-confirm__btn cancel" type="button" id="ptsConfirmCancel">Cancel</button>
                <button class="pts-confirm__btn danger" type="button" id="ptsConfirmOk">Confirm</button>
            </div>
        </div>
    </div>

    <script>
        // ============================================================
        // Sidebar toggle (mobile slide + desktop collapse)
        // ============================================================
        (function () {
            const sidebar = document.getElementById('ptsSidebar');
            const toggle = document.getElementById('sidebarToggle');
            const overlay = document.getElementById('sidebarOverlay');
            const main = document.querySelector('.pts-main');

            if (!sidebar || !toggle) return;

            // Restore desktop collapsed state from localStorage
            if (window.innerWidth > 900 && localStorage.getItem('sidebarCollapsed') === '1') {
                sidebar.classList.add('collapsed');
                main?.classList.add('sidebar-collapsed');
            }

            toggle.addEventListener('click', function () {
                if (window.innerWidth <= 900) {
                    // Mobile: slide in/out
                    sidebar.classList.toggle('mobile-open');
                    overlay.classList.toggle('active');
                } else {
                    // Desktop: collapse/expand
                    sidebar.classList.toggle('collapsed');
                    main?.classList.toggle('sidebar-collapsed');

                    const isCollapsed = sidebar.classList.contains('collapsed');
                    localStorage.setItem('sidebarCollapsed', isCollapsed ? '1' : '0');
                }
            });

            overlay?.addEventListener('click', function () {
                sidebar.classList.remove('mobile-open');
                overlay.classList.remove('active');
            });
        })();

        // ============================================================
        // Collapsible nav sections
        // ============================================================
        (function () {
            document.querySelectorAll('.pts-nav__section').forEach(function (header) {
                header.addEventListener('click', function () {
                    const key = header.dataset.section;
                    const items = document.querySelector('.pts-nav__items[data-items="' + key + '"]');
                    if (!items) return;

                    header.classList.toggle('collapsed');
                    items.classList.toggle('collapsed');

                    if (items.classList.contains('collapsed')) {
                        items.style.maxHeight = '0';
                    } else {
                        items.style.maxHeight = items.scrollHeight + 'px';
                    }
                });

                const key = header.dataset.section;
                const items = document.querySelector('.pts-nav__items[data-items="' + key + '"]');
                if (items) items.style.maxHeight = items.scrollHeight + 'px';
            });
        })();

        // ============================================================
        // Sidebar search filter
        // ============================================================
        (function () {
            const input = document.getElementById('navSearch');
            if (!input) return;

            input.addEventListener('input', function () {
                const q = input.value.trim().toLowerCase();
                document.querySelectorAll('.pts-nav__link').forEach(function (link) {
                    const text = link.textContent.trim().toLowerCase();
                    link.classList.toggle('hidden-by-search', q !== '' && !text.includes(q));
                });
            });
        })();

        // ============================================================
        // Dropdowns
        // ============================================================
        (function () {
            document.querySelectorAll('[data-dropdown]').forEach(function (dd) {
                const toggle = dd.querySelector('[data-dropdown-toggle]');
                if (!toggle) return;

                toggle.addEventListener('click', function (e) {
                    e.stopPropagation();
                    const isOpen = dd.classList.contains('open');
                    document.querySelectorAll('[data-dropdown].open').forEach(function (o) {
                        o.classList.remove('open');
                    });
                    if (!isOpen) dd.classList.add('open');
                });
            });

            document.addEventListener('click', function () {
                document.querySelectorAll('[data-dropdown].open').forEach(function (o) {
                    o.classList.remove('open');
                });
            });
        })();

        // ============================================================
        // Keyboard shortcut: Cmd/Ctrl+K → focus global search
        // ============================================================
        (function () {
            document.addEventListener('keydown', function (e) {
                if ((e.metaKey || e.ctrlKey) && e.key.toLowerCase() === 'k') {
                    e.preventDefault();
                    const input = document.getElementById('globalSearch');
                    if (input) input.focus();
                }
            });
        })();

        // ============================================================
        // Toast utility (global)
        // ============================================================
        window.ptsToast = function (message, type) {
            type = type || 'success';
            const container = document.getElementById('ptsToasts');
            if (!container) return;

            const icons = {
                success: 'fa-circle-check',
                error: 'fa-circle-exclamation',
                warning: 'fa-triangle-exclamation',
            };

            const toast = document.createElement('div');
            toast.className = 'pts-toast ' + type;
            toast.innerHTML = '<i class="fa-solid ' + (icons[type] || icons.success) + '"></i><span>' + message + '</span>';
            container.appendChild(toast);

            setTimeout(function () {
                toast.style.opacity = '0';
                toast.style.transition = 'opacity 0.2s';
                setTimeout(function () { toast.remove(); }, 200);
            }, 4000);
        };

        // ============================================================
        // Confirm dialog (global) — replaces window.confirm()
        // ============================================================
        window.ptsConfirm = function (options, onConfirm) {
            const dialog = document.getElementById('ptsConfirm');
            const title = document.getElementById('ptsConfirmTitle');
            const text = document.getElementById('ptsConfirmText');
            const okBtn = document.getElementById('ptsConfirmOk');
            const cancelBtn = document.getElementById('ptsConfirmCancel');

            title.textContent = options.title || 'Are you sure?';
            text.textContent = options.text || 'This action cannot be undone.';
            okBtn.textContent = options.confirmText || 'Confirm';
            cancelBtn.textContent = options.cancelText || 'Cancel';

            dialog.classList.add('open');

            const close = function () { dialog.classList.remove('open'); };

            cancelBtn.onclick = close;
            okBtn.onclick = function () {
                close();
                if (typeof onConfirm === 'function') onConfirm();
            };
        };

        // Intercept forms with data-confirm
        document.addEventListener('submit', function (e) {
            const form = e.target;
            if (form.dataset.confirm && !form.dataset.confirmed) {
                e.preventDefault();
                window.ptsConfirm({
                    title: 'Confirm action',
                    text: form.dataset.confirm,
                    action: function () {
                        form.dataset.confirmed = '1';
                        form.submit();
                    }
                });
            }
        });
    </script>

    @yield('scripts')
</body>

</html>