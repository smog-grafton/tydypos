<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $title ?? $appSettings->get('branding.app_name', config('app.name')) }}</title>
        <style>
            :root {
                --color-primary: {{ $appSettings->get('theme.primary', config('tydypos.theme.primary')) }};
                --color-primary-dark: {{ $appSettings->get('theme.primary_dark', config('tydypos.theme.primary_dark')) }};
                --color-accent: {{ $appSettings->get('theme.accent', config('tydypos.theme.accent')) }};
                --color-success: {{ $appSettings->get('theme.success', config('tydypos.theme.success')) }};
                --color-danger: {{ $appSettings->get('theme.danger', config('tydypos.theme.danger')) }};
                --color-background: {{ $appSettings->get('theme.background', config('tydypos.theme.background')) }};
                --color-surface: {{ $appSettings->get('theme.surface', config('tydypos.theme.surface')) }};
                --color-text-primary: {{ $appSettings->get('theme.text_primary', config('tydypos.theme.text_primary')) }};
                --color-text-muted: {{ $appSettings->get('theme.text_muted', config('tydypos.theme.text_muted')) }};
                --color-border: {{ $appSettings->get('theme.border', config('tydypos.theme.border')) }};
            }

            * { box-sizing: border-box; }
            body {
                margin: 0;
                min-height: 100vh;
                background:
                    radial-gradient(circle at top left, rgba(37, 99, 235, 0.16), transparent 25%),
                    radial-gradient(circle at top right, rgba(245, 158, 11, 0.16), transparent 22%),
                    var(--color-background);
                color: var(--color-text-primary);
                font-family: "Instrument Sans", "Segoe UI", sans-serif;
            }
            a { color: inherit; text-decoration: none; }
            .shell { max-width: 1180px; margin: 0 auto; padding: 24px; }
            .nav {
                display: flex; justify-content: space-between; align-items: center; gap: 16px;
                padding: 18px 22px; border: 1px solid var(--color-border); border-radius: 24px;
                background: rgba(255,255,255,0.86); backdrop-filter: blur(16px);
                box-shadow: 0 24px 60px rgba(15, 23, 42, 0.08);
            }
            .brand { display: flex; flex-direction: column; gap: 4px; }
            .brand strong { font-size: 1.1rem; }
            .brand span { color: var(--color-text-muted); font-size: 0.92rem; }
            .actions { display: flex; align-items: center; gap: 12px; flex-wrap: wrap; }
            .pill {
                display: inline-flex; align-items: center; gap: 8px;
                border: 1px solid var(--color-border); border-radius: 999px;
                padding: 10px 14px; background: var(--color-surface);
            }
            .button {
                border: 0; border-radius: 14px; padding: 11px 16px; font-weight: 700; cursor: pointer;
                background: linear-gradient(135deg, var(--color-primary), var(--color-primary-dark));
                color: white;
            }
            .button-muted {
                background: white; color: var(--color-text-primary); border: 1px solid var(--color-border);
            }
            .hero {
                margin-top: 26px; padding: 36px; border-radius: 32px; background: var(--color-surface);
                border: 1px solid rgba(226, 232, 240, 0.9); box-shadow: 0 36px 80px rgba(15, 23, 42, 0.08);
            }
            .hero-grid { display: grid; grid-template-columns: 1.5fr 1fr; gap: 18px; margin-top: 24px; }
            .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 18px; margin-top: 24px; }
            .panel, .stat {
                background: rgba(248, 250, 252, 0.9); border: 1px solid var(--color-border);
                border-radius: 24px; padding: 20px;
            }
            .table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 14px;
                font-size: 0.95rem;
            }
            .table th, .table td {
                text-align: left;
                padding: 12px 10px;
                border-bottom: 1px solid var(--color-border);
                vertical-align: top;
            }
            .stat-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 16px; margin-top: 18px; }
            .stat strong { display: block; font-size: 2rem; margin-top: 12px; }
            .muted { color: var(--color-text-muted); }
            .badge {
                display: inline-flex; align-items: center; padding: 8px 12px; border-radius: 999px;
                background: rgba(22, 163, 74, 0.08); color: var(--color-success); font-weight: 700;
            }
            .auth-shell {
                min-height: 100vh; display: grid; place-items: center; padding: 24px;
            }
            .auth-card {
                width: min(100%, 430px); padding: 32px; border-radius: 28px; background: rgba(255,255,255,0.94);
                border: 1px solid var(--color-border); box-shadow: 0 30px 80px rgba(15, 23, 42, 0.12);
            }
            label { display: block; font-size: 0.92rem; font-weight: 700; margin-bottom: 8px; }
            input, select {
                width: 100%; padding: 13px 14px; border-radius: 14px; border: 1px solid var(--color-border);
                background: white; color: var(--color-text-primary);
            }
            .field { margin-bottom: 18px; }
            .error { color: var(--color-danger); font-size: 0.88rem; margin-top: 6px; }
            .checkbox { display: flex; align-items: center; gap: 10px; font-size: 0.94rem; color: var(--color-text-muted); }
            .checkbox input { width: auto; }
            .stack { display: flex; flex-direction: column; gap: 18px; }
            .nav-links { display: flex; gap: 10px; flex-wrap: wrap; }
            .flash {
                margin-top: 16px; padding: 14px 18px; border-radius: 18px;
                border: 1px solid rgba(22, 163, 74, 0.2); background: rgba(22, 163, 74, 0.08);
                color: var(--color-success); font-weight: 600;
            }
            @media (max-width: 900px) {
                .hero-grid, .stat-grid, .grid-2 { grid-template-columns: 1fr; }
                .nav { align-items: flex-start; flex-direction: column; }
            }
        </style>
    </head>
    <body>
        @if (session('status'))
            <div class="shell">
                <div class="flash">{{ session('status') }}</div>
            </div>
        @endif
        {{ $slot }}
    </body>
</html>
