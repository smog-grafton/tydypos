<x-layouts.app :title="__('Sign in')">
    <div class="auth-shell">
        <div class="auth-card">
            <div class="badge">{{ __('Foundation Ready') }}</div>
            <h1 style="margin: 16px 0 8px; font-size: 2rem;">{{ $appSettings->get('branding.app_name', config('app.name')) }}</h1>
            <p class="muted" style="margin: 0 0 28px;">{{ $appSettings->get('branding.tagline', config('tydypos.branding.tagline')) }}</p>

            <form method="POST" action="{{ route('login.store') }}">
                @csrf

                <div class="field">
                    <label for="email">{{ __('Email address') }}</label>
                    <input id="email" name="email" type="email" value="{{ old('email', 'admin@tydypos.test') }}" required autofocus>
                    @error('email') <div class="error">{{ $message }}</div> @enderror
                </div>

                <div class="field">
                    <label for="password">{{ __('Password') }}</label>
                    <input id="password" name="password" type="password" value="password" required>
                    @error('password') <div class="error">{{ $message }}</div> @enderror
                </div>

                <label class="checkbox" for="remember">
                    <input id="remember" name="remember" type="checkbox" value="1" @checked(old('remember'))>
                    <span>{{ __('Remember me') }}</span>
                </label>

                <button class="button" type="submit" style="width: 100%; margin-top: 22px;">{{ __('Sign in') }}</button>
            </form>
        </div>
    </div>
</x-layouts.app>
