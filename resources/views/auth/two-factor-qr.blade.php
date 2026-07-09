<!DOCTYPE html>
<html>
<head>
    <title>Código QR 2FA</title>
</head>
<body style="font-family: sans-serif; padding: 40px;">
    <h2>Configuración de Autenticación de Dos Factores</h2>
    <p>Escanea este código con tu aplicación de autenticación:</p>
    
    <div style="margin: 20px 0;">
        {!! $qrCode !!} 
    </div>
    
    <p><strong>Clave secreta manual:</strong> {{ $secretKey }}</p>
    
    <a href="/perfil">Volver al perfil</a>
</body>
</html>
