<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Authentication</title>

    <!-- Styles -->
    <link rel="stylesheet" href="{{ mix('app.css', 'lava') }}">

</head>
<body>
    
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">

        <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
    
            <!-- Validation Errors -->
            @if (isset($errors) && $errors->any())
                <div>
                    <div class="font-medium text-red-600">
                        {{ __('Whoops! Something went wrong.') }}
                    </div>
    
                    <ul class="mt-3 list-disc list-inside text-sm text-red-600">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
    
            <form method="POST" action="{{ route('auth.login') }}">
            @csrf
    
            <!-- Email Address -->
                <div>
    
                    <label for="email" class="block font-medium text-sm text-gray-700">
    
                        {{ __('Email') }}
    
                        <input id="email"
                               class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                               type="email" name="email" value="{{ old('email') }}"
                               required autofocus/>
    
                    </label>
    
                </div>
    
                <!-- Password -->
                <div class="mt-4">
    
                    <label for="password" class="block font-medium text-sm text-gray-700">
    
                        {{ __('Password') }}
    
                        <input id="password"
                               class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                               type="password" name="password" value="{{ old('password') }}"
                               required autocomplete="current-password"/>
    
                    </label>
    
                </div>
    
                <!-- Remember Me -->
                <div class="block mt-4">
                    <label for="remember_me" class="inline-flex items-center">
                        <input id="remember_me" type="checkbox"
                               value="1"
                               class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                               name="remember">
                        <span class="ml-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
                    </label>
                </div>
    
                <div class="flex items-center justify-end mt-4">
    
                    <button class="inline-flex items-center ml-3 px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150"
                            type="submit">
                        {{ __('Log in') }}
                    </button>
    
                </div>
            </form>
    
        </div>
    
    </div>

</body>
</html>