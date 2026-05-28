<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SIGVI · Iniciar Sesión</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root {
            --primary: #0d1b2a;
            --accent: #e8700a;
            --accent-light: #ff8c2a;
            --danger: #dc2626;
            --success: #16a34a;
            --text: #0f172a;
            --text-muted: #64748b;
            --border: #dde3ec;
            --radius: 22px;
            --radius-sm: 9px;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', system-ui, sans-serif;
            min-height: 100vh;
            background: linear-gradient(135deg, #0d1b2a 0%, #1b3a5c 45%, #0f1f33 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            overflow-x: hidden;
            overflow-y: auto;
        }

        .login-box {
            background: rgba(255, 255, 255, 0.99);
            border-radius: var(--radius);
            padding: 48px 44px;
            width: 100%;
            max-width: 430px;
            box-shadow: 0 30px 90px rgba(0, 0, 0, 0.35);
            position: relative;
            z-index: 1;
            margin: auto;
        }

        .login-logo {
            text-align: center;
            margin-bottom: 34px;
        }

        .logo-img-wrapper {
            margin: 0 auto 16px;
            width: 120px;
            height: 120px;
            border-radius: 50%;
            overflow: hidden;
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
        }

        .logo-img-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .login-logo h1 {
            font-size: 28px;
            font-weight: 900;
            color: var(--text);
            letter-spacing: 2px;
            margin: 0;
        }

        .login-logo .tagline {
            font-size: 12px;
            color: var(--text-muted);
            margin-top: 6px;
        }

        .login-logo .company-label {
            display: inline-block;
            background: rgba(232, 112, 10, 0.1);
            color: var(--accent);
            font-size: 11px;
            font-weight: 700;
            padding: 3px 12px;
            border-radius: 20px;
            margin-top: 8px;
            letter-spacing: 1px;
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: var(--text);
            margin-bottom: 6px;
        }

        .input-wrapper {
            position: relative;
        }

        .form-control, .form-select {
            width: 100%;
            padding: 10px 14px;
            border: 1.5px solid var(--border);
            border-radius: var(--radius-sm);
            font-size: 14px;
            color: var(--text);
            outline: none;
            transition: all 0.2s;
            font-family: inherit;
            background: #fff;
        }

        .form-select {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 12px center;
            background-size: 14px;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(232, 112, 10, 0.12);
        }

        .form-control.is-invalid, .form-select.is-invalid {
            border-color: var(--danger);
        }

        .invalid-feedback {
            color: var(--danger);
            font-size: 12px;
            margin-top: 5px;
            display: block;
        }

        .input-wrapper.password .form-control {
            padding-right: 44px;
        }

        .toggle-password {
            position: absolute;
            right: 6px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #94a3b8;
            font-size: 18px;
            cursor: pointer;
            padding: 6px;
            z-index: 2;
            transition: color 0.2s;
            line-height: 1;
        }

        .toggle-password:hover {
            color: var(--accent);
        }

        .toggle-password i {
            pointer-events: none;
        }

        .btn-login {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, var(--accent), #c85a00);
            color: #fff;
            border: none;
            border-radius: 11px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.22s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            box-shadow: 0 4px 14px rgba(232, 112, 10, 0.3);
            font-family: inherit;
            margin-bottom: 16px;
        }

        .btn-login:hover {
            background: linear-gradient(135deg, var(--accent-light), var(--accent));
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(232, 112, 10, 0.4);
        }

        .btn-google {
            width: 100%;
            padding: 11px;
            background: #fff;
            color: var(--text);
            border: 1.5px solid var(--border);
            border-radius: 11px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.22s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            font-family: inherit;
            text-decoration: none;
        }

        .btn-google:hover {
            background: #f8fafc;
            border-color: var(--accent);
            box-shadow: 0 4px 14px rgba(232, 112, 10, 0.15);
        }

        .btn-google img {
            width: 20px;
            height: 20px;
        }

        .alert-error {
            background: rgba(220, 38, 38, 0.08);
            border: 1px solid rgba(220, 38, 38, 0.2);
            border-radius: 8px;
            padding: 12px 14px;
            color: #b91c1c;
            font-size: 13px;
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 20px;
        }

        .footer-note {
            text-align: center;
            margin-top: 24px;
            font-size: 12px;
            color: var(--text-muted);
        }

        @media (max-width: 480px) {
            .login-box {
                padding: 36px 24px;
                border-radius: 18px;
            }

            .login-logo h1 {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>

<div class="login-box">
    <!-- Logo / Marca -->
    <div class="login-logo">
        <div class="logo-img-wrapper">
            <img src="{{ asset('assets/img/Logo/Logo_Install D.jpeg') }}" alt="Logo Install D">
        </div>
        <h1>SIGVI</h1>
        <p class="tagline">Sistema de Gestión de Ventas e Inventario</p>
        <span class="company-label">INSTALL D · AUTOMOTRIZ</span>
    </div>

    <!-- Mensajes de sesión -->
    @if (session('status'))
        <div class="alert-error" style="background:rgba(22,163,74,0.08);border-color:rgba(22,163,74,0.2);color:var(--success)">
            <i class="bi bi-check-circle-fill"></i> {{ session('status') }}
        </div>
    @endif

    <!-- Errores de validación -->
    @if ($errors->any())
        <div class="alert-error">
            <i class="bi bi-x-circle-fill"></i>
            {{ $errors->first() }}
        </div>
    @endif

    <!-- Formulario de inicio de sesión -->
    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="form-group">
            <label class="form-label">
                <i class="bi bi-person-badge-fill me-1" style="color:var(--accent)"></i> Rol
            </label>
            <select name="rol" class="form-select @error('rol') is-invalid @enderror" required>
                <option value="Admin" {{ old('rol') == 'Admin' ? 'selected' : '' }}>Administrador</option>
                <option value="Vendedor" {{ old('rol') == 'Vendedor' ? 'selected' : '' }}>Vendedor</option>
            </select>
            @error('rol')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label class="form-label">
                <i class="bi bi-envelope-fill me-1" style="color:var(--accent)"></i> Correo electrónico
            </label>
            <div class="input-wrapper">
                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                       value="{{ old('email') }}" required autofocus autocomplete="username"
                       placeholder="admin@installd.pe">
            </div>
            @error('email')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label class="form-label">
                <i class="bi bi-lock-fill me-1" style="color:var(--accent)"></i> Contraseña
            </label>
            <div class="input-wrapper password">
                <input type="password" name="password" id="password"
                       class="form-control @error('password') is-invalid @enderror"
                       required autocomplete="current-password" placeholder="••••••••">
                <button type="button" class="toggle-password" onclick="togglePassword()" tabindex="-1" aria-label="Mostrar contraseña">
                    <i class="bi bi-eye-slash" id="toggleIcon"></i>
                </button>
            </div>
            @error('password')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>

        <!-- NUEVO: Selector de rol (solo Admin y Vendedor) -->


        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:8px;">
            <label style="display:flex;align-items:center;gap:7px;font-size:13px;color:var(--text-muted);cursor:pointer">
                <input type="checkbox" name="remember" style="accent-color:var(--accent)"> Recordarme
            </label>
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" style="font-size:13px;color:var(--accent);font-weight:500;white-space:nowrap;">
                    ¿Olvidaste tu contraseña?
                </a>
            @endif
        </div>

        <button type="submit" class="btn-login">
            <i class="bi bi-box-arrow-in-right"></i> Iniciar Sesión
        </button>
    </form>

    <!-- Separador -->
    <div style="display:flex;align-items:center;gap:12px;margin:18px 0;">
        <hr style="flex:1;border:none;border-top:1px solid var(--border);">
        <span style="font-size:12px;color:var(--text-muted);">o</span>
        <hr style="flex:1;border:none;border-top:1px solid var(--border);">
    </div>

    <!-- Botón de Google -->
    <a href="{{ route('auth.google') }}" class="btn-google">
        <img src="https://developers.google.com/identity/images/g-logo.png" alt="Google logo">
        Iniciar sesión con Google
    </a>

    <!-- Pie de página -->
    <p class="footer-note">SIGVI © {{ date('Y') }} · Install D · Trujillo, Perú</p>
</div>

<script>
    function togglePassword() {
        const passwordInput = document.getElementById('password');
        const toggleIcon = document.getElementById('toggleIcon');

        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            toggleIcon.classList.remove('bi-eye-slash');
            toggleIcon.classList.add('bi-eye');
        } else {
            passwordInput.type = 'password';
            toggleIcon.classList.remove('bi-eye');
            toggleIcon.classList.add('bi-eye-slash');
        }
    }
</script>

</body>
</html>
