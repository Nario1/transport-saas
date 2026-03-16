<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'TransJunín — SaaS')</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap"
        rel="stylesheet">
    <style>
        :root {
            --bg: #f0f2f7;
            --sidebar: #0f1623;
            --sidebar2: #171e2e;
            --card: #ffffff;
            --border: #e2e6ef;
            --border2: #d0d6e3;
            --accent: #1d4ed8;
            --accent-l: #dbeafe;
            --accent-t: rgba(29, 78, 216, 0.08);
            --gold: #d97706;
            --gold-l: #fef3c7;
            --green: #16a34a;
            --green-l: #dcfce7;
            --red: #dc2626;
            --red-l: #fee2e2;
            --orange: #ea580c;
            --orange-l: #ffedd5;
            --text: #0f172a;
            --text2: #475569;
            --text3: #94a3b8;
            --shadow: 0 1px 3px rgba(0, 0, 0, 0.07), 0 1px 2px rgba(0, 0, 0, 0.04);
            --shadow-m: 0 4px 12px rgba(0, 0, 0, 0.08), 0 2px 4px rgba(0, 0, 0, 0.04);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--bg);
            color: var(--text);
            display: flex;
            min-height: 100vh;
            font-size: 14px;
        }

        /* ── SIDEBAR ── */
        .sb {
            width: 232px;
            min-height: 100vh;
            background: var(--sidebar);
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            display: flex;
            flex-direction: column;
            z-index: 200;
        }

        .sb-brand {
            padding: 20px 18px 16px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.07);
        }

        .brand-logo {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .brand-icon {
            width: 36px;
            height: 36px;
            background: var(--accent);
            border-radius: 9px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 16px;
            color: #fff;
            flex-shrink: 0;
        }

        .brand-name {
            font-size: 14px;
            font-weight: 700;
            color: #fff;
            line-height: 1.2;
        }

        .brand-sub {
            font-size: 10.5px;
            color: rgba(255, 255, 255, 0.4);
            margin-top: 1px;
        }

        .sb-company {
            margin: 12px 10px;
            background: var(--sidebar2);
            border: 1px solid rgba(255, 255, 255, 0.07);
            border-radius: 10px;
            padding: 10px 12px;
            display: flex;
            align-items: center;
            gap: 9px;
            cursor: pointer;
        }

        .company-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #22c55e;
            flex-shrink: 0;
        }

        .company-name {
            font-size: 12.5px;
            font-weight: 600;
            color: #e2e8f0;
            flex: 1;
        }

        .company-caret {
            color: rgba(255, 255, 255, 0.3);
            font-size: 11px;
        }

        .sb-nav {
            flex: 1;
            padding: 8px 10px;
            overflow-y: auto;
        }

        .nav-group {
            margin-bottom: 20px;
        }

        .nav-group-label {
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.09em;
            color: rgba(255, 255, 255, 0.3);
            padding: 0 8px;
            margin-bottom: 4px;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 9px;
            padding: 8px 10px;
            border-radius: 8px;
            cursor: pointer;
            color: rgba(255, 255, 255, 0.55);
            font-size: 13px;
            font-weight: 500;
            transition: all .15s;
            margin-bottom: 1px;
            position: relative;
            text-decoration: none;
        }

        .nav-item:hover {
            background: rgba(255, 255, 255, 0.06);
            color: rgba(255, 255, 255, 0.9);
        }

        .nav-item.active {
            background: var(--accent);
            color: #fff;
        }

        .nav-item .ni {
            font-size: 15px;
            width: 18px;
            text-align: center;
            flex-shrink: 0;
        }

        .nav-badge {
            margin-left: auto;
            background: var(--red);
            color: #fff;
            font-size: 10px;
            padding: 1px 7px;
            border-radius: 99px;
            font-weight: 700;
        }

        .sb-footer {
            padding: 14px 10px;
            border-top: 1px solid rgba(255, 255, 255, 0.07);
        }

        .user-row {
            display: flex;
            align-items: center;
            gap: 9px;
            padding: 8px 10px;
            border-radius: 8px;
            text-decoration: none;
        }

        .user-av {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            background: var(--accent);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 700;
            color: #fff;
            flex-shrink: 0;
        }

        .user-name {
            font-size: 12.5px;
            font-weight: 600;
            color: #e2e8f0;
        }

        .user-role {
            font-size: 11px;
            color: rgba(255, 255, 255, 0.4);
        }

        /* ── MAIN ── */
        .main {
            margin-left: 232px;
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .topbar {
            background: var(--card);
            border-bottom: 1px solid var(--border);
            padding: 0 28px;
            height: 56px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: var(--shadow);
        }

        .tb-left {
            display: flex;
            flex-direction: column;
        }

        .tb-title {
            font-size: 16px;
            font-weight: 700;
            color: var(--text);
        }

        .tb-crumb {
            font-size: 11.5px;
            color: var(--text3);
        }

        .tb-right {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .tb-date {
            background: var(--bg);
            border: 1px solid var(--border);
            border-radius: 7px;
            padding: 6px 12px;
            font-size: 12px;
            color: var(--text2);
        }

        .btn-primary {
            background: var(--accent);
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 8px 16px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            font-family: inherit;
            transition: opacity .15s;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .btn-primary:hover {
            opacity: .87;
        }

        .btn-secondary {
            background: var(--card);
            color: var(--text2);
            border: 1px solid var(--border2);
            border-radius: 8px;
            padding: 7px 14px;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            font-family: inherit;
            transition: all .15s;
        }

        .btn-secondary:hover {
            border-color: var(--accent);
            color: var(--accent);
        }

        .btn-danger {
            background: var(--red-l);
            color: var(--red);
            border: 1px solid rgba(220, 38, 38, .2);
            border-radius: 7px;
            padding: 5px 12px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            font-family: inherit;
        }

        .btn-sm {
            padding: 5px 12px;
            font-size: 12px;
        }

        /* ── CONTENT ── */
        .content {
            padding: 24px 28px;
            flex: 1;
        }

        .panel {
            animation: fadeIn .2s ease;
        }

        /* ── CARDS ── */
        .card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 14px;
            box-shadow: var(--shadow);
            margin-bottom: 20px;
        }

        .card-header {
            padding: 16px 20px;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .card-title {
            font-size: 14px;
            font-weight: 700;
        }

        .card-body {
            padding: 20px;
        }

        /* ── STAT CARDS ── */
        .stats-row {
            display: grid;
            grid-template-columns: repeat(6, 1fr);
            gap: 14px;
            margin-bottom: 22px;
        }

        .stat {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 18px 16px;
            box-shadow: var(--shadow);
            position: relative;
            overflow: hidden;
        }

        .stat-label {
            font-size: 11px;
            font-weight: 600;
            color: var(--text2);
            text-transform: uppercase;
            letter-spacing: .06em;
            margin-bottom: 8px;
        }

        .stat-val {
            font-size: 26px;
            font-weight: 800;
            line-height: 1;
            margin-bottom: 4px;
        }

        .stat-sub {
            font-size: 11.5px;
            color: var(--text3);
        }

        .stat-icon {
            position: absolute;
            right: 14px;
            top: 14px;
            font-size: 20px;
            opacity: .25;
        }

        .stat.blue .stat-val {
            color: var(--accent);
        }

        .stat.green .stat-val {
            color: var(--green);
        }

        .stat.gold .stat-val {
            color: var(--gold);
        }

        .stat.red .stat-val {
            color: var(--red);
        }

        .stat.orange .stat-val {
            color: var(--orange);
        }

        .stat::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            border-radius: 14px 14px 0 0;
        }

        .stat.blue::after {
            background: var(--accent);
        }

        .stat.green::after {
            background: var(--green);
        }

        .stat.gold::after {
            background: var(--gold);
        }

        .stat.red::after {
            background: var(--red);
        }

        .stat.orange::after {
            background: var(--orange);
        }

        /* ── TABLES ── */
        .tbl {
            width: 100%;
            border-collapse: collapse;
        }

        .tbl th {
            text-align: left;
            padding: 10px 16px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .07em;
            color: var(--text3);
            background: var(--bg);
            border-bottom: 1px solid var(--border);
        }

        .tbl td {
            padding: 12px 16px;
            font-size: 13px;
            border-bottom: 1px solid var(--border);
            vertical-align: middle;
        }

        .tbl tbody tr:hover td {
            background: rgba(29, 78, 216, 0.02);
        }

        /* ── PILLS ── */
        .pill {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 3px 10px;
            border-radius: 99px;
            font-size: 11.5px;
            font-weight: 600;
        }

        .pill::before {
            content: '';
            width: 5px;
            height: 5px;
            border-radius: 50%;
            background: currentColor;
            display: inline-block;
        }

        .pill.green {
            background: var(--green-l);
            color: var(--green);
        }

        .pill.red {
            background: var(--red-l);
            color: var(--red);
        }

        .pill.orange {
            background: var(--orange-l);
            color: var(--orange);
        }

        .pill.blue {
            background: var(--accent-l);
            color: var(--accent);
        }

        .pill.gold {
            background: var(--gold-l);
            color: var(--gold);
        }

        /* ── FORMS ── */
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
        }

        .field {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .field label {
            font-size: 11.5px;
            font-weight: 600;
            color: var(--text2);
            text-transform: uppercase;
            letter-spacing: .05em;
        }

        .field input,
        .field select,
        .field textarea {
            background: var(--bg);
            border: 1px solid var(--border2);
            border-radius: 8px;
            padding: 9px 12px;
            font-size: 13.5px;
            color: var(--text);
            font-family: inherit;
            outline: none;
        }

        .field input:focus,
        .field select:focus,
        .field textarea:focus {
            border-color: var(--accent);
            background: #fff;
        }

        .field-full {
            grid-column: 1/-1;
        }

        /* ── MODAL ── */
        .modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(15, 22, 35, 0.5);
            z-index: 500;
            align-items: center;
            justify-content: center;
        }

        .modal-overlay.open {
            display: flex;
        }

        .modal {
            background: var(--card);
            border-radius: 16px;
            width: 620px;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
        }

        .modal-header {
            padding: 20px 24px 16px;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .modal-body {
            padding: 20px 24px;
        }

        .modal-footer {
            padding: 16px 24px;
            border-top: 1px solid var(--border);
            display: flex;
            gap: 10px;
            justify-content: flex-end;
        }

        /* ── MINI CHART ── */
        .chart-bars {
            display: flex;
            align-items: flex-end;
            gap: 5px;
            height: 70px;
        }

        .cb-wrap {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 3px;
        }

        .cb {
            width: 100%;
            border-radius: 4px 4px 0 0;
            background: var(--accent-l);
            position: relative;
            height: 70px;
        }

        .cb-fill {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            border-radius: 4px 4px 0 0;
            background: var(--accent);
        }

        /* ── OTHERS ── */
        .mono {
            font-family: 'JetBrains Mono', monospace;
            font-size: 12px;
            background: var(--bg);
            border: 1px solid var(--border);
            padding: 2px 7px;
            border-radius: 5px;
        }

        .alert {
            border-radius: 10px;
            padding: 12px 16px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 13px;
            margin-bottom: 18px;
        }

        .alert.warning {
            background: var(--orange-l);
            color: var(--orange);
            border: 1px solid rgba(234, 88, 12, .2);
        }

        .g2-1 {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 16px;
            margin-bottom: 20px;
        }

        .progress-wrap {
            margin-bottom: 10px;
        }

        .progress-label {
            display: flex;
            justify-content: space-between;
            font-size: 12px;
            margin-bottom: 4px;
        }

        .progress-track {
            height: 7px;
            background: var(--bg);
            border-radius: 4px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            border-radius: 4px;
            background: var(--green);
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(6px)
            }

            to {
                opacity: 1;
                transform: none
            }
        }
    </style>

    @yield('extra_css')
    </style>
</head>

<body>
    @yield('body_content')
    @stack('scripts')
</body>

</html>
