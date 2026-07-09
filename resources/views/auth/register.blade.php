<!DOCTYPE html>
<html>
<body style="font-family: sans-serif; padding: 40px;">
    <h2>Registro de Usuario</h2>
    <form method="POST" action="/register">
        @csrf
        <p>Nombre: <br><input type="text" name="name" required></p>
        <p>Email: <br><input type="email" name="email" required></p>
        <p>Contraseña: <br><input type="password" name="password" required></p>
        <p>Confirmar Contraseña: <br><input type="password" name="password_confirmation" required></p>
        <button type="submit">Registrarme</button>
    </form>
    <br>
    <a href="/login">Ya tengo cuenta (Login)</a>
</body>
</html>
