<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .container { max-width: 900px; }
        .card { border-radius: 10px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); }
        .table { background: #fff; border-radius: 10px; overflow: hidden; }
        .table th { background: #343a40; color: white; }
        .btn { border-radius: 5px; }
        .badge { padding: 6px 12px; font-size: 14px; }
        .pagination .page-item.active .page-link { background-color: #007bff; border-color: #007bff; }
        .pagination .page-link { color: #007bff; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
            <div class="flex justify-end">
                @auth
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn btn-danger">Logout</button>
                    </form>
                @endauth
            </div>
        </div>
    </nav>
    <div class="container mt-5">
        @yield('content')
    </div>
</body>
</html>