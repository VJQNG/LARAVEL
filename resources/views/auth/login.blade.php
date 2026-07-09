<!DOCTYPE html>
<html>
<body style="font-family: sans-serif; padding: 40px;">
    <h2>Iniciar Sesión</h2>
    <form method="POST" action="/login">
        @csrf
        <p>Email: <br><input type="email" name="email" required></p>
        <p>Contraseña: <br><input type="password" name="password" required></p>
        <button type="submit">Entrar</button>
    </form>
    <br>
    <a href="/register">Crear una cuenta nueva</a>
</body>
</html>
