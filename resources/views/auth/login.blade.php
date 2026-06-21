<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Kuisioner Dosen</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Inter', sans-serif;
            margin: 0;
        }
        .login-card {
            background: rgba(255, 255, 255, 0.98);
            border-radius: 24px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
            overflow: hidden;
            width: 100%;
            max-width: 400px;
            border: 1px solid rgba(255,255,255,0.1);
        }
        .login-header {
            background: white;
            padding: 40px 30px 20px;
            text-align: center;
        }
        .form-control {
            border-radius: 0 12px 12px 0;
            padding: 12px 15px;
            border: 1px solid #e2e8f0;
            background-color: #f8fafc;
            border-left: none;
        }
        .form-control:focus {
            box-shadow: none;
            background-color: #fff;
            border-color: #38bdf8;
        }
        .input-group-text {
            border-radius: 12px 0 0 12px;
            border: 1px solid #e2e8f0;
            background-color: #f8fafc;
            padding-left: 20px;
            border-right: none;
        }
        .input-group:focus-within .input-group-text, .input-group:focus-within .form-control {
            border-color: #38bdf8;
            background-color: #fff;
        }
        .btn-login {
            background: linear-gradient(135deg, #38bdf8 0%, #0284c7 100%);
            border: none;
            border-radius: 12px;
            padding: 12px;
            font-weight: 600;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(56, 189, 248, 0.3);
        }
    </style>
</head>
<body>

<div class="login-card">
    <div class="login-header">
        <div class="d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px; background: #f1f5f9; border-radius: 20px;">
            <i class="bi bi-shield-lock-fill fs-1 text-primary"></i>
        </div>
        <h3 class="fw-bolder mb-1 text-dark" style="letter-spacing: -0.5px;">Admin Panel</h3>
        <p class="text-muted fw-medium mb-0">Sistem Kuisioner Dosen</p>
    </div>
    <div class="p-4 pt-2 pb-5">
        @if($errors->any())
            <div class="alert alert-danger py-2 small fw-medium text-center rounded-3 mb-4 border-0 bg-danger bg-opacity-10 text-danger">
                <i class="bi bi-exclamation-circle me-1"></i> {{ $errors->first() }}
            </div>
        @endif
        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="mb-4">
                <label class="form-label fw-bold text-secondary small text-uppercase" style="letter-spacing: 1px;">Email Address</label>
                <div class="input-group shadow-sm">
                    <span class="input-group-text"><i class="bi bi-envelope text-muted"></i></span>
                    <input type="email" name="email" class="form-control" placeholder="admin@admin.com" value="{{ old('email') }}" required autofocus>
                </div>
            </div>
            <div class="mb-5">
                <label class="form-label fw-bold text-secondary small text-uppercase" style="letter-spacing: 1px;">Password</label>
                <div class="input-group shadow-sm">
                    <span class="input-group-text"><i class="bi bi-lock text-muted"></i></span>
                    <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                </div>
            </div>
            <button type="submit" class="btn btn-primary w-100 btn-login text-white">
                Masuk ke Dashboard <i class="bi bi-arrow-right ms-2"></i>
            </button>
        </form>
    </div>
</div>

</body>
</html>
