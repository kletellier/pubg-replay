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
  <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css">
  <link rel="stylesheet" type="text/css" href="{{ url('css/pubg.css' ) }}?d=20180612"> 
  <script>
      @yield('script')
  </script>
</head>
<body>
<div class="container">
  <div class="row">
    &nbsp;
  </div>
  <div class="row text-center">
    <a href="{{ url('/') }}" title="Back to main menu"><img class="img-rounded" src="{{ url('logo_mail.jpg') }}" width="302" height="148"></a>
  </div>
  @yield('contenu')
</div>
@if(env('GITHUB_LINK','')!='')
  <a href="{{ env('GITHUB_LINK','_blank') }}"><img style="position: absolute; top: 0; right: 0; border: 0;" src="https://s3.amazonaws.com/github/ribbons/forkme_right_darkblue_121621.png" alt="Fork me on GitHub"></a>
@endif
</body>
</html>