<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Listado de Usuarios</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #333; text-align: left; }
        th { background: #f2f2f2; }
    </style>
</head>
<body>
    <h2>Listado de Usuarios</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Username</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Roles</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
            <tr>
                <td>{{ $user->_id }}</td>
                <td>{{ $user->name }}</td>
                <td>{{ $user->username }}</td>
                <td>{{ $user->email }}</td>
                <td>{{ $user->phone }}</td>
                <td>{{ is_array($user->roles) ? implode(', ', $user->roles) : $user->roles }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
