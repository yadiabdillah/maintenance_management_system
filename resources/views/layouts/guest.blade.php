<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Maintenance Management System')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .guest-layout {
            min-height: 100vh;
        }
    </style>
</head>
<body>
    <div class="guest-layout d-flex align-items-center justify-content-center">
        @yield('content')
    </div>
</body>
</html>
