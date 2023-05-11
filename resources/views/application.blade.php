<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>{{ env('APP_NAME') }}</title>
  <!-- Styles -->

  <link rel="stylesheet" href="{{ asset(mix('css/main.css')) }}">
  <link rel="stylesheet" href="{{ asset(mix('css/iconfont.css')) }}">
  <link rel="stylesheet" href="{{ asset(mix('css/material-icons/material-icons.css')) }}">
  <link rel="stylesheet" href="{{ asset(mix('css/vuesax.css')) }}">
  <link rel="stylesheet" href="{{ asset(mix('css/prism-tomorrow.css')) }}">
  <link rel="stylesheet" href="{{ asset(mix('css/app.css')) }}">

  {{-- <link rel="stylesheet" href="{{ asset(mix('css/iconfont.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('css/material-icons/material-icons.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('css/vuesax.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('css/prism-tomorrow.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('css/app.css')) }}">
    <link rel="stylesheet" href="{{ asset('dist/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset(mix('css/main.css')) }}"> --}}

  <!-- Favicon -->
  <link rel="icon" href="{{ asset('images/favicon.png') }}">
</head>

<body>
  <noscript>
    <strong>We're sorry but this software doesn't work properly without JavaScript enabled. Please enable it to
      continue.</strong>
  </noscript>
  <div id="app">
  </div>

  <!-- <script src="js/app.js"></script> -->
  <script src="{{ asset(mix('js/app.js')) }}"></script>

</body>

</html>
