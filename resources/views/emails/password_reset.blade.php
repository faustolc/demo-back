<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Restablecimiento de Contraseña</title>
</head>
<body>
    <h1>Hola, {{ $user->name }}</h1>
    <p>Se ha solicitado un restablecimiento de contraseña para tu cuenta.</p>
    <p>Tu nueva contraseña temporal es:</p>
    <p><strong>{{ $newPassword }}</strong></p>
    <p>Te recomendamos iniciar sesión y cambiar esta contraseña por una que recuerdes.</p>
    <p>Gracias,</p>
</body>
</html>
