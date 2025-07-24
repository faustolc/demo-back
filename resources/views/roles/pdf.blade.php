<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Listado de Roles</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px;}
        th, td { border: 1px solid #333; padding: 8px; text-align: left;}
        th { background: #f2f2f2; }
    </style>
</head>
<body>
    <h2>Listado de Roles</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Fecha de creación</th>
            </tr>
        </thead>
        <tbody>
            @foreach($roles as $role)
            <tr>
                <td>{{ $role->id }}</td>
                <td>{{ $role->name }}</td>
                <td>{{ $role->created_at }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
