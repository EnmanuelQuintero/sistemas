<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login - Sistema Institucional</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="w-full max-w-md bg-white rounded-2xl shadow-lg p-8">
        
        <!-- Logo institucional -->
        <div class="flex justify-center mb-6">
            <img src="{{ asset('images/logo-negro.png') }}" alt="Logo Institucional" class="h-16">
        </div>

        <h2 class="text-center text-2xl font-bold text-gray-800 mb-6">
            Ingreso al Sistema
        </h2>

        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <ul class="text-sm">
                    @foreach ($errors->all() as $error)
                        <li>• {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('login.post') }}" method="POST" class="space-y-5">
            @csrf

            <div>
                <label for="nombre" class="block text-sm font-medium text-gray-700">Usuario</label>
                <input type="text" name="nombre" id="nombre" value="{{ old('nombre') }}"
                       class="mt-1 w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none"
                       required autofocus>
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Contraseña</label>
                <input type="password" name="password" id="password"
                       class="mt-1 w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none"
                       required>
            </div>

            <button type="submit"
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 rounded-lg transition">
                Iniciar Sesión
            </button>
        </form>

        <p class="mt-6 text-center text-sm text-gray-500">
            © {{ date('Y') }} - Custom Make
        </p>
    </div>
</body>
</html>
