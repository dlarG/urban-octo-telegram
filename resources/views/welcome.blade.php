<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>RentStreet — Boarding houses in Sogod</title>
    @vite(['resources/css/app.css'])
</head>
<body class="min-h-screen bg-white text-gray-900">
    <div class="max-w-3xl mx-auto px-6 py-24 text-center">
        <h1 class="text-4xl font-bold tracking-tight">RentStreet</h1>
        <p class="mt-4 text-lg text-gray-600">
            Boarding houses, bed spaces, and rentals in Sogod, Southern Leyte.
        </p>
        <div class="mt-8 flex justify-center gap-3">
            <a href="{{ route('register') }}"
               class="rounded-md bg-indigo-600 px-5 py-2.5 text-sm font-medium text-white hover:bg-indigo-700">
                Get started
            </a>
            <a href="{{ route('login') }}"
               class="rounded-md border border-gray-300 px-5 py-2.5 text-sm font-medium hover:bg-gray-50">
                Log in
            </a>
        </div>
    </div>
</body>
</html>