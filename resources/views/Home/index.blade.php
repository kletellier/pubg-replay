<?php use App\Classes\BladeHelper; ?>
 
@extends('layout')

@section('title',   $title   )

@section('script')
	function doSearch()
	{
		var vName = $("#inpName").val().trim();
		if(vName!='')
		{
			$("#frmSearch").submit();
		}
	}
@endsection

@section('contenu')
<div class="row">
	<div class="col-xs-12 text-center"><h4>PUBG Replay</h4> </div>
</div> 
<form  id="frmSearch" action="{{ url('/player') }}" method="post">
	<div class="form-group">
	    <label for="inpName">Player name</label>
	    <input type="text" class="form-control" name="inpName" id="inpName" placeholder="Enter player name">
	</div>
	<div class="form-group">
	    <label for="inpShards">Select platform</label>
	    <select class="form-control" name="inpShards" id="inpShards">
			@foreach($shards as $shard)
				<option value="{{  $shard->value }}" {{  $shard->selected }}>{{  $shard->name }}</option>
			@endforeach
	    </select>
	</div>
	<div class="form-group">
	    <input type="button" class="btn btn-default" value="Search" onclick="doSearch()">
	</div>
	{{ csrf_field() }}
</form>
@endsection