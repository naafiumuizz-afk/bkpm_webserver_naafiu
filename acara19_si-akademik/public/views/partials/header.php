<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8'); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f8f9fa;
        }
        .login-page {
            padding-top: 42px;
            padding-bottom: 42px;
        }
        .login-card {
            max-width: 376px;
            width: 100%;
        }
        .login-card .card-body {
            padding: 22px;
        }
        .login-card .form-control,
        .login-card .btn {
            min-height: 38px;
            font-size: 0.875rem;
        }
        .login-card .alert,
        .login-card .form-label,
        .login-card .login-demo {
            font-size: 0.875rem;
        }
        .login-card .alert {
            padding: 13px 14px;
        }
    </style>
</head>
<body>
