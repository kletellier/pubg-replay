<?php use App\Classes\BladeHelper; ?>

<!DOCTYPE html>
<html>
<head>
  <meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title')</title>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css" />
  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/js/bootstrap.min.js"></script>  
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootbox.js/4.4.0/bootbox.js"></script>
  <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css">
  <link rel="stylesheet" type="text/css" href="{{ url('css/pubg.css' ) }}?d=20180622"> 
  <script src="{{ url('js/konva.js' ) }}"></script>
  <link rel="stylesheet" type="text/css" href="{{ url('css/bootstrap-slider.min.css') }}">
  <script src="{{ url('js/bootstrap-slider.min.js') }}"></script>
  <link href="{{ url('css/bootstrap-switch.min.css') }}" rel="stylesheet"> 
  <script src="{{ url('js/bootstrap-switch.min.js') }}"></script>
  <script>
      @yield('script')
  </script>
  <script src="{{ url('js/replay.js' ) }}"></script>
</head>
<body class="bodyreplay">
<div class="container-replay">   
  @yield('contenu')
</div>
</body>
</html>