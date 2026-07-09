<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Práctica 4 - Pruebas de Seguridad</title>
    <style>
        body { font-family: sans-serif; margin: 40px; background: #f4f4f9; color: #333; }
        .box { background: white; padding: 20px; margin-bottom: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .danger { border-left: 5px solid #dc3545; background: #fdf2f2; }
        .success { border-left: 5px solid #28a745; background: #f2fdf4; }
        input, textarea { width: 100%; max-width: 400px; padding: 8px; margin-top: 5px; }
        button { background: #007bff; color: white; border: none; padding: 10px 15px; border-radius: 4px; cursor: pointer; }
    </style>
</head>
<body>
    <h1>Práctica 4: Control de CSRF y XSS</h1>

    <div class="box">
        <h2>Formulario de Comentarios</h2>
        <form action="/guardar-comentario" method="POST">
            @csrf
            <p>
                <label>Email:</label><br>
                <input type="email" name="email" value="marcos@example.com" required>
            </p>
            <p>
                <label>Título:</label><br>
                <input type="text" name="titulo" value="Prueba Inyección" required>
            </p>
            <p>
                <label>Contenido:</label><br>
                <textarea name="contenido" rows="4" required>&lt;script&gt;alert('XSS')&lt;/script&gt;</textarea>
            </p>
            <button type="submit">Enviar</button>
        </form>
    </div>

    @if(isset($comentario_crudo))
    <div class="box danger">
        <h2>1. Sin escape (Peligroso)</h2>
        <div>{!! $comentario_crudo !!}</div>
    </div>
    <div class="box success">
        <h2>2. Con escape Blade (Seguro)</h2>
        <div>{{ $comentario_limpio }}</div>
    </div>
    @endif
</body>
</html>
