<x-layouts.app :title="__('Dashboard')">
    <div class="shell">
        <nav class="nav">
            <div class="brand">
                <strong>{{ $appSettings->get('branding.app_name', config('app.name')) }}</strong>
                <span>{{ $appSettings->get('branding.tagline', config('tydypos.branding.tagline')) }}</span>
            </div>

            <div class="actions">
                <div class="nav-links">
                    <a class="button button-muted" href="{{ route('products.index') }}">Products</a>
                    <a class="button button-muted" href="{{ route('pos.index') }}">POS</a>
                </div>

                <div class="pill">
                    <strong>{{ __('Active branch') }}:</strong>
                    <span class="muted">{{ auth()->user()->currentBranch?->name ?? 'Unassigned' }}</span>
                </div>

                <form method="POST" action="{{ route('locale.update') }}" class="pill">
                    @csrf
                    @method('PATCH')
                    <label for="locale" style="margin: 0;">{{ __('Locale') }}</label>
                    <select id="locale" name="locale" onchange="this.form.submit()" style="padding: 8px 10px; border: 0;">
                        <option value="en" @selected(app()->getLocale() === 'en')>{{ __('English') }}</option>
                        <option value="es" @selected(app()->getLocale() === 'es')>{{ __('Spanish') }}</option>
                    </select>
                </form>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="button button-muted" type="submit">{{ __('Sign out') }}</button>
                </form>
            </div>
        </nav>

        <section class="hero">
            <div style="display: flex; justify-content: space-between; gap: 16px; flex-wrap: wrap;">
                <div>
                    <div class="badge">{{ __('Foundation Ready') }}</div>
                    <h1 style="font-size: 2.4rem; margin: 16px 0 10px;">{{ __('Dashboard') }}</h1>
                    <p class="muted" style="max-width: 680px;">
                        {{ __('Tydy POS now has a working Laravel foundation with authentication, role-based seed data, branch awareness, shared-hosting-safe uploads, and theme-driven branding settings.') }}
                    </p>
                </div>
                <div class="panel" style="min-width: 280px;">
                    <div class="muted">{{ __('Role coverage') }}</div>
                    <div style="font-size: 1.05rem; font-weight: 700; margin-top: 10px;">Owner, Manager, Cashier</div>
                    <div class="muted" style="margin-top: 12px;">{{ auth()->user()->email }}</div>
                </div>
            </div>

            <div class="stat-grid">
                <div class="stat">
                    <span class="muted">{{ __('Users') }}</span>
                    <strong>{{ $stats['users'] }}</strong>
                </div>
                <div class="stat">
                    <span class="muted">{{ __('Branches') }}</span>
                    <strong>{{ $stats['branches'] }}</strong>
                </div>
                <div class="stat">
                    <span class="muted">{{ __('Settings') }}</span>
                    <strong>{{ $stats['settings'] }}</strong>
                </div>
            </div>

            <div class="hero-grid">
                <div class="panel">
                    <div class="muted">{{ __('Foundation Summary') }}</div>
                    <ul style="padding-left: 18px; margin: 14px 0 0; line-height: 1.8;">
                        <li>{{ __('Modular monolith foundation') }}</li>
                        <li>{{ __('Shared-hosting-safe uploads') }}</li>
                        <li>{{ __('Permissions and seed data') }}</li>
                    </ul>
                </div>

                <div class="panel">
                    <div class="muted">Milestone 1</div>
                    <div style="font-size: 1.2rem; font-weight: 700; margin-top: 10px;">Core, Auth, Settings, Branches</div>
                    <p class="muted" style="margin-top: 10px;">
                        Next milestones will add inventory, checkout integrity, receipts, installer, and updater flows on top of this base.
                    </p>
                </div>
            </div>
        </section>
    </div>
</x-layouts.app>
