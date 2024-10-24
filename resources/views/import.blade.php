<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Importar Documentos</title>
</head>
<body>
    <h1>Importar Documentos</h1>

    @if (session('success'))
        <div>{{ session('success') }}</div>
    @endif

    <form action="{{ route('import.process') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="file" name="file" required>
        <button type="submit">Importar JSON</button>
    </form>

    <form action="{{ route('process.queue') }}" method="POST">
        @csrf
        <button type="submit">Processar Fila</button>
    </form>
</body>
</html>
