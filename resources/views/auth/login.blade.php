<x-guest-layout>
    <style>
        body { font-family: 'Figtree', sans-serif; background: linear-gradient(135deg, #f4f5f7 0%, #e2e8f0 100%); }
        .login-card { background: white; border-radius: 1.5rem; padding: 3rem 2.5rem; box-shadow: 0 20px 40px -10px rgba(0,0,0,0.1); text-align: center; max-width: 450px; width: 100%; }
        .login-logo { width: 90px; height: 90px; object-fit: contain; margin-bottom: 1rem; filter: drop-shadow(0 4px 6px rgba(0,0,0,0.1)); }
        .login-title { font-size: 1.5rem; font-weight: 800; color: #1e293b; margin-bottom: 0.25rem; letter-spacing: 0.025em; }
        .login-subtitle { font-size: 0.85rem; color: #64748b; margin-bottom: 2rem; }
        .form-group { text-align: left; margin-bottom: 1.25rem; }
        .form-label { display: block; font-size: 0.8rem; font-weight: 600; color: #475569; margin-bottom: 0.5rem; }
        .form-input { width: 100%; padding: 0.875rem 1rem; border: 1.5px solid #cbd5e1; border-radius: 0.75rem; outline: none; transition: all 0.2s; font-size: 0.95rem; }
        .form-input:focus { border-color: #35348b; box-shadow: 0 0 0 4px rgba(53,52,139,0.1); }
        .btn-login { width: 100%; background: #35348b; color: white; padding: 1rem; border-radius: 0.75rem; border: none; font-size: 1rem; font-weight: 700; cursor: pointer; transition: all 0.2s; margin-top: 0.5rem; box-shadow: 0 4px 12px rgba(53,52,139,0.25); }
        .btn-login:hover { background: #2a2970; transform: translateY(-2px); box-shadow: 0 8px 16px rgba(53,52,139,0.3); }
        .error-msg { font-size: 0.75rem; color: #ef4444; margin-top: 0.25rem; display: block; }
    </style>

    <div class="login-card">
        <img src="{{ asset('logo-dishub.png') }}" alt="Logo Dishub" class="login-logo">
        <h1 class="login-title">SISTEM PENOMORAN SK</h1>
        <p class="login-subtitle">Masuk untuk melanjutkan ke sistem</p>

        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <!-- Info Notice (e.g. register disabled) -->
        @if(session('info'))
            <div style="background:#dbeafe; border:1px solid #93c5fd; color:#1e40af; padding:0.75rem 1rem; border-radius:0.75rem; font-size:0.82rem; margin-bottom:1.25rem; text-align:left;">
                ℹ️ {{ session('info') }}
            </div>
        @endif


        <form method="POST" action="{{ route('login') }}">
            @csrf

            <!-- Email Address -->
            <div class="form-group">
                <label for="email" class="form-label">Email</label>
                <input id="email" class="form-input" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="Masukkan email anda..." />
                <x-input-error :messages="$errors->get('email')" class="error-msg" />
            </div>

            <!-- Password -->
            <div class="form-group">
                <label for="password" class="form-label">Password</label>
                <input id="password" class="form-input" type="password" name="password" required autocomplete="current-password" placeholder="••••••••" />
                <x-input-error :messages="$errors->get('password')" class="error-msg" />
            </div>

            <div class="form-group" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
                <label for="remember_me" style="display: inline-flex; align-items: center; gap: 0.5rem; cursor: pointer; font-size: 0.85rem; color: #64748b;">
                    <input id="remember_me" type="checkbox" name="remember" style="width: 1rem; height: 1rem; border-radius: 0.25rem; accent-color: #35348b;">
                    <span>Ingat saya</span>
                </label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" style="font-size: 0.85rem; color: #35348b; text-decoration: none; font-weight: 600;">Lupa password?</a>
                @endif
            </div>

            <button type="submit" class="btn-login">
                MASUK SEKARANG
            </button>
        </form>
    </div>
</x-guest-layout>
