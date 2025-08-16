<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>LaraTaskr</title>
    @vite(['resources/js/app.js'])
    <style>
        body{font-family: system-ui, -apple-system, sans-serif; padding: 24px; background:#f8fafc}
        .container{max-width:900px; margin:0 auto}
        header{display:flex; gap:12px; align-items:center; margin-bottom:24px}
        select, input, button{padding:8px 10px; border:1px solid #cbd5e1; border-radius:6px; background:white}
        button{cursor:pointer; background:#0ea5e9; color:white; border:none}
        ul{list-style:none; padding:0; margin:0; display:flex; flex-direction:column; gap:8px}
        li{background:white; border:1px solid #e2e8f0; border-radius:8px; padding:10px; display:flex; align-items:center; gap:10px}
        .handle{cursor:grab; color:#64748b; user-select:none}
        .right{margin-left:auto; display:flex; gap:6px}
        form.inline{display:inline-flex; gap:6px; align-items:center}
        .muted{color:#64748b; font-size:12px}
    </style>
</head>
<body>
<div class="container">
    <header>
        <h1 style="margin:0">LaraTaskr</h1>
        @if (session('status'))
            <span class="muted">· {{ session('status') }}</span>
        @endif
    </header>

    <main>
        @yield('content')
    </main>
</div>
</body>
</html>
