<?php use App\Classes\BladeHelper; ?>
 
@extends('layout')

@section('title',   $title   )

@section('script')
 
@endsection

@section('contenu')
<div class="row">
	<div class="col-xs-12 text-center"><h4>{{ $name }} - {{ $shards }}</h4> </div>
</div> 
<div class="row">
	<table class="table table-bordered table-hover table-striped">
		<thead>
			<tr>
				<th>Date</th>
				<th>Mode</th>
				<th>Ranking</th>
				<th>&nbsp;</th>
			</tr>
		</thead>
		<tbody>
			@foreach($matches as $match)
			@if($match->isOk())
				<tr>
					<td>{{ $match->getCreatedAt() }}</td>
					<td>{!! $match->getModeHtml() !!}</td>
					<td>{{ $match->extractPlace($name) }}</td>
					<td><a href="{{ url('replay')}}/{{ $match->getId() }}/{{ $match->getShards() }}/{{ $name }}" class='btn btn-default'><i class='fa fa-play'></i>&nbsp;Replay</a></td>
				</tr>
			@endif
			@endforeach
		</tbody>
	</table>
</div>
@if($missing)
	<div class="row">
		<div class="bg-danger col-xs-8 col-xs-offset-2">
			It lacks games because of the limitation of requests to the server of PUBG, thank you to refresh this search again within 1mn to complete the list
		</div>
	</div>
@endif
@endsection