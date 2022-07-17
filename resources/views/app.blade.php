<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Nunito:400,600,700" rel="stylesheet">
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet"/>
        <script
            src="https://www.paypal.com/sdk/js?client-id=AXENds1O20ndqyMqVktO7EOWnZ4BuRfCIAJ4WSPIbVrrYwyraHZEUvh8DkaFXXmHmpqM5KUDLRqa0m4E&vault=true">
        </script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.14.0/css/all.min.css" integrity="sha512-1PKOgIY59xJ8Co8+NE6FZ+LOAZKjy+KY8iq0G4B3CyeY6wYHN3yt9PW0XpSriVlkMXe40PTKnXrLnZ9+fkDaog==" crossorigin="anonymous" />

        <!-- Scripts -->
        <script async defer src="https://apis.google.com/js/api.js"></script>
         <!-- Scripts -->
        @routes
        @vite('resources/js/app.js')
    </head>
    <body class="font-sans antialiased ic-scroller">
        @inertia
    </body>
</html>
