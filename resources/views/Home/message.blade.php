<?php use App\Classes\BladeHelper; ?>
@extends('layout')

@section('title',   $title   )

@section('script')

@endsection

@section('contenu')
	<div class="row">
		&nbsp;
	</div> 
	<div class="row">
		<div class="col-xs-8 col-xs-offset-2 bg-danger text-center"><strong>{{ $message }}</strong></div>
	</div>
	<div class="row">
		&nbsp;
	</div> 
	<div class="row">
		<div class="col-xs-8 col-xs-offset-2 text-center"><a href="{{ url('/') }}" class="btn btn-default"><i class="fa fa-back"></i>&nbsp;Back to main menu</a></div>
	</div> 
@endsection