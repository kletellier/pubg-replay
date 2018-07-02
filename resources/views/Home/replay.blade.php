<?php use App\Classes\BladeHelper; ?>
@extends('layoutr')

@section('title',   $title   )


@section('script')

var vId = '{!! $id !!}';
var vShards = '{!! $shards !!}';
var vUser = '{!! $user !!}';
var vUrlLoot = "{{ url('itemicon') }}/drop";
var vUrl = '{{ url("json/path") }}/' + vId + "/" + vShards + "/" + vUser;
var vUrlRoot =  "{{ url('/')}}";
var vUrlMap = "{{ url('maplowres') }}/";

function showZoomHelp()
{
	bootbox.alert("You can zoom by using the mousewheel on a computer, with touch device like mobile or tablet, you can zoom by simple tap, unzoom to scale 1 by double tap");
}
@endsection

@section('contenu')

<div class="row">
	&nbsp;
</div>
<div class="row" id="controls">
	<div class="btn-group" id="playpause">
		<a class="btn btn-default"  href="javascript:togglePause()"><i id="btnplay" class="fa fa-pause"></i></a>
		<a class="btn btn-default"  href="javascript:resetPlay()"><i   class="fa fa-backward"></i></a>
	</div>
	<div class="btn-group" id="speedfactor">
		<a class="btn btn-default clspeed" id="speed5" href="javascript:setSpeed(5)">x5</a>
		<a class="btn btn-default clspeed active"  id="speed10"  href="javascript:setSpeed(10)">x10</a>
		<a class="btn btn-default clspeed"  id="speed15" href="javascript:setSpeed(15)">x15</a>
		<a class="btn btn-default clspeed"  id="speed20" href="javascript:setSpeed(20)">x20</a>
		<a class="btn btn-default clspeed"  id="speed35" href="javascript:setSpeed(35)">x35</a>
		<a class="btn btn-default"    href="javascript:showZoomHelp()"><i class="fa fa-search-plus"></i></a>
		<a class="btn btn-default" title="Take screenshot"  href="javascript:exportImage()"><i class="fa fa-photo"></i></a> 
		<a class="btn btn-default" title="Go to main menu" href="{{ url('/') }}"><i class="fa fa-home"></i></a> 
	</div>
	<div class="btn-group">		
		<span id="chk"><small>Display names</small>:&nbsp;<input type="checkbox" data-size="mini" data-state="true" name="chkname" id="chkname" checked></span>
		<span id="timer"></span>
		<span id="nbalive"></span>
	</div> 
	<div class="btn-group" id="selNom">
		
	</div> 
</div>
<div class="row">
	&nbsp;
</div>
<div class="row" id="slider">
	<div class="col-xs-12">
		<input class="col-xs-12" type="text" id="sldTimer">
	</div>		
</div>
<div class="row">
	&nbsp;
</div>
<div class="row" id="wait">
	<p>
	<i class="fa fa-refresh fa-spin"></i>&nbsp;Loading in progress... This may take few seconds !!
</p>
	<p>
		Why it take some times to display my replay ?
		<ul>
			<li> First we must extract telemetry url from your match </li>
			<li> After we download the telemetry, a big JSON file of 10-13 Mo...</li>
			<li> We parse this file to extract the needing data for the replay.</li>
			<li> After this we download the map file and the data on your browser (between 3 and 6 Mo for the map file and 400ko for the replay data)</li>
			<li> And it's done... The first time it can take <strong> 30 seconds </strong>, next viewing take less time because replay data was cached in the server</li>
		</ul>
	</p>
</div> 
<div class="row">
	<div col="col-xs-12">
		<div id="container"></div>		 
	</div>
</div> 

@endsection