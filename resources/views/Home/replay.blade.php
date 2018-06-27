<?php use App\Classes\BladeHelper; ?>
@extends('layoutr')

@section('title',   $title   )


@section('script')

Array.prototype.last = function() {
return this[this.length-1];
}

Number.prototype.toMMSS = function () {
    var sec_num = parseInt(this, 10); // don't forget the second param
    var hours   = Math.floor(sec_num / 3600);
    var minutes = Math.floor((sec_num - (hours * 3600)) / 60);
    var seconds = sec_num - (hours * 3600) - (minutes * 60);

    if (hours   < 10) {hours   = "0"+hours;}
    if (minutes < 10) {minutes = "0"+minutes;}
    if (seconds < 10) {seconds = "0"+seconds;}
    return minutes+':'+seconds;
}

function exportImage()
{
	window.open(stage.toDataURL(), '_blank');
}

function ftSec(vSec)
{
	return vSec.toMMSS();
}

@if($size!=="")
var vPlayerSelected = "";
@endif
var vId = '{!! $id !!}';
var vShards = '{!! $shards !!}';
var vUser = '{!! $user !!}';
var vUrlLoot = "{{ url('itemicon') }}/drop";
var vObj;
var vSize = 1024;
var vMapDistance = 816000;
var vLayer;
var vTimer;
var vSlider;
var vSlideMove = false;
var vSecondes = 0;
var vSpeed = 10;
var vPause = false;
var vDuration = 0;
var vRadius = 4;
var vStroke = 1;
var vDisplayName = true;
var imageLoot = new Image(); 
var vLastDisplayTime = 0;
var grpAttack = new Array();
var vUrl = '{{ url("json/path") }}/' + vId + "/" + vShards + "/" + vUser;

var stage;

$(document).ready(function(){
 
$("#wait").show();
$("#slider").hide();
$("#chkname").bootstrapSwitch();
$('#chkname').bootstrapSwitch('size', 'mini');
$('#chkname').bootstrapSwitch('state', true, true);
$('#chkname').on('switchChange.bootstrapSwitch', function(event, state) {
  vDisplayName = state;
  displayTime(vSecondes);
});
$("#controls").hide();

@if($size!=="")
	var vWindow = parseInt($(window).width()*0.92);	
	vSize= parseInt({{ $size }});
	if(vSize < vWindow)
	{
		vSize = vWindow;
	}
	var vScroll = parseInt(vSize*0.92); 
	var vHeight = $(window).height(); 
@else 
	vSize= parseInt($(window).width()*0.92);
@endif

if(vSize < 700)
{
	vRadius = 3;
	vStroke = 1;
} 





$.ajax({
dataType: "json",
url: vUrl, 
success: onJson,
error: onError
});
});

function onError()
{
	$("#wait").text("Error during request, please refresh by press F5 key...");
	$("#wait").show();
}

function onJson(data)
{
	vObj = data; 
	if(vObj===false)
	{
		document.location.href = "{{ url('/')}}";
	}
	else
	{
		@if($size!='')
		vObj.players.forEach(function(eleme)
		{
			if(eleme.isplayer==1)
			{
				var vHtml = "<a class='btn btn-default' player='" + eleme.name + "' href='javascript:dpPlayer(\"" + eleme.name + "\")'>" + eleme.name + "</a>";
				$("#selNom").prepend(vHtml);
			}			
		});		 
		@endif
		vDuration = vObj.duration;	
		displayMap();	
	}	
}

@if($size!='')
function dpPlayer(vName)
{ 
	 
	var vJoueur = stage.findOne("#" + vName);
	var x = vJoueur.x();
	var y = vJoueur.y();	
	
	var vW = $(window).width()/2;
	var vH = $(window).height()/2;

	$(".scroll-container").scrollLeft(x-vW) ;
	$(".scroll-container").scrollTop(y-vH);
}
@endif

function InitSlider()
{
	$("#slider").show();

	vSlider = $("#sldTimer").slider({
		min:0,
		max:vDuration,
		step:vSpeed,
		formatter:ftSec
	});

	vSlider.slider().on('slideStart', function(ev){
		 vSlideMove = true;
	});

	vSlider.slider().on('slideStop', function(ev){
		vSlideMove = false;
		var newVal = vSlider.data('slider').getValue();
		if(vSecondes != newVal) {
			vSecondes = newVal;
			displayTime(vSecondes);
		}
	});

}

function coordToPix(vCoord)
{
	return (vCoord / vMapDistance) * vSize;
}

function displayMap()
{
	stage = new Konva.Stage({
	container: 'container',
	width: vSize,
	height: vSize
});
var vCarte = vObj.carte;
 
var vUrlCarte = "{{ url('maplowres') }}/" + vCarte;


if(vCarte==="Savage_Main")
{
	 vMapDistance = 408000;
}

var layer = new Konva.Layer();
var imageObj = new Image();


imageObj.onload = function() {
var map = new Konva.Image({
x: 0,
y: 0,
image: imageObj,
width: vSize,
height: vSize
});



// add the shape to the layer
layer.add(map);

// add the layer to the stage
stage.add(layer);

// création des objets
initCircles();

// Initialisation du slider
InitSlider();


$("#wait").hide();
$("#controls").show();
$("#slider").show();

// enable zoom system
var scaleBy = 1.05;
window.addEventListener('wheel', (e) => {
    e.preventDefault();
    var oldScale = stage.scaleX();

    var mousePointTo = {
        x: stage.getPointerPosition().x / oldScale - stage.x() / oldScale,
        y: stage.getPointerPosition().y / oldScale - stage.y() / oldScale,
    };

    var newScale = e.deltaY < 0 ? oldScale * scaleBy : oldScale / scaleBy;
    if(newScale < 1)
    {
    	newScale = 1;
    }
    stage.scale({ x: newScale, y: newScale });

    var newPos = {
        x: -(mousePointTo.x - stage.getPointerPosition().x / newScale) * newScale,
        y: -(mousePointTo.y - stage.getPointerPosition().y / newScale) * newScale
    };
    stage.position(newPos);
    stage.batchDraw();
});
 

vTimer = setInterval(RefreshMaps, 200);

};
imageObj.src = vUrlCarte;
imageLoot.src = vUrlLoot;
}

function RefreshMaps()
{
	if(vPause==false)
	{
		if( vSecondes < vObj.duration)
		{
			vSecondes += (vSpeed/5);			
			displayTime(vSecondes);
		}
		else
		{
			clearInterval(vTimer);
		} 
	}	
}

function initCircles()
{
	vLayer = new Konva.Layer();

	var bcircle = new Konva.Circle({
	x: 0,
	y: 0,
	radius: 0, 
	stroke: 'blue',
	strokeWidth: 2,
	id:'bcircle'
	});

	vLayer.add(bcircle);


	var wcircle = new Konva.Circle({
	x: 0,
	y: 0,
	radius: 0, 
	stroke: 'white',
	strokeWidth: 2,
	id:'wcircle'
	});

	vLayer.add(wcircle);

	var rcircle = new Konva.Circle({
	x: 0,
	y: 0,
	radius: 0, 
	stroke: 'red',
	fill: 'red',
	strokeWidth: 2,
	opacity: 0.4,
	id:'rcircle'
	});

	vLayer.add(rcircle);



vObj.players.forEach(function(eleme)
{
	name = eleme.name;
	pts = eleme.points;
	x = 0;
	y = 0;
	couleur = eleme.color;


	var circle = new Konva.Circle({
	x: 0,
	y: 0,
	radius: vRadius,
	fill: couleur,
	stroke: 'black',
	strokeWidth: vStroke,
	id:name
	});

	var text = new Konva.Text({
	x: 0,
	y: 0,
	text: name + " (" + eleme.winPlace + ")",
	fill: 'white',
	fontSize: 11,
	fontStyle: 'bold',
	id:"txt_" + name
	});

	vLayer.add(text);
	vLayer.add(circle);		 
});

stage.add(vLayer);
}

function togglePause()
{
	if(vPause)
	{
		$("#btnplay").removeClass('fa-play').addClass('fa-pause');
		vPause = false;

	}
	else
	{
		vPause = true;
		$("#btnplay").removeClass('fa-pause').addClass('fa-play');
	}
}

function setSpeed(speed)
{
	var vSelec = "speed" + speed;
	$(".clspeed").removeClass('active');
	$(vSelec).addClass('active');
	vSpeed = speed;
	vSlider.slider('setAttribute','step',vSpeed);
}

function resetPlay()
{
	vSecondes = 0;	 
	displayTime(vSecondes);
}

function displayTime(vNb)
{ 
	$("#timer").html("<strong>" + vSecondes.toMMSS() + "</strong>");
	if(vSlideMove==false)
	{
		vSlider.slider('setValue',vNb);
	}
	
	var nbLoot = 0;
	vObj.loots.forEach(function (lts){

		nbLoot++;
		var vidloot = "loot_" + nbLoot;

		if(lts.elapsed<=vNb)
		{ 
			
			var img = stage.findOne("#" + vidloot);

			if(img===undefined)
			{
				img = new Konva.Image({
			    x: coordToPix(lts.position_x)-12,
			    y: coordToPix(lts.position_y)-12,
			    image: imageLoot,
			    id: vidloot,
			    width: 25,
			    height: 25
				});

				vLayer.add(img);
			}
			img.x(coordToPix(lts.position_x));
			img.y(coordToPix(lts.position_y)); 
			img.visible(true); 
		}
		else
		{
			var img = stage.findOne("#" + vidloot);

			if(img!==undefined)
			{
				img.visible(false); 
			}			
		}
	});
	

	var vBlancX = 0;
	var vBlancY = 0;
	var vBlancR = 0;

	var vBleuX = 0;
	var vBleuY = 0;
	var vBleuR = 0;

	var vRougeX = 0;
	var vRougeY = 0;
	var vRougeR = 0;

	vObj.gamestates.forEach(function(gs){
	if(gs.elapsedTime<=vNb)
	{
		vBleuX = gs.safety_x;
		vBleuY = gs.safety_y;
		vBleuR = gs.safety_radius;
		vBlancX  = gs.bluezone_x;
		vBlancY  = gs.bluezone_y;
		vBlancR  = gs.bluezone_radius;
		vRougeX  = gs.redzone_x;
		vRougeY  = gs.redzone_y;
		vRougeR  = gs.redzone_radius;
	}	
});

if(vBlancR>0)
{
	var wcircle = stage.findOne("#wcircle");
	wcircle.x(coordToPix(vBlancX));
	wcircle.y(coordToPix(vBlancY));
	wcircle.radius(coordToPix(vBlancR)); 
}
else
{
	var wcircle = stage.findOne("#wcircle");
	wcircle.x(0);
	wcircle.y(0);
	wcircle.radius(0); 
}

if(vBleuR>0)
{
	var bcircle  = stage.findOne("#bcircle");
	bcircle.x(coordToPix(vBleuX));
	bcircle.y(coordToPix(vBleuY));
	bcircle.radius(coordToPix(vBleuR)); 
}
else
{
	var bcircle  = stage.findOne("#bcircle");
	bcircle.x(0);
	bcircle.y(0);
	bcircle.radius(0); 
}

if(vRougeR>0)
{
	var rcircle  = stage.findOne("#rcircle");
	rcircle.x(coordToPix(vRougeX));
	rcircle.y(coordToPix(vRougeY));
	rcircle.radius(coordToPix(vRougeR)); 
}
else
{
	var rcircle  = stage.findOne("#rcircle");
	rcircle.x(0);
	rcircle.y(0);
	rcircle.radius(0); 
}


nbAlive = 0;

vObj.players.forEach(function(eleme)
{

	name = eleme.name;
	pts = eleme.points;
	x = 0;
	y = 0;
	couleur = eleme.color;
	if(vNb>eleme.kill)
	{
		// joueur mort
		// on prend le dernier point
		coord = pts.last();
		if(coord!==undefined)
		{
			x = coord.x;
			y = coord.y;
		}

		couleur = "#ff0000";
	}
	else
	{
		nbAlive++;
		for (var i = 0; i <= pts.length - 1; i++) 
		{
			var pt = pts[i];
			if(pt.elapsed<=vNb)
			{
				x = pt.x;
				y = pt.y;
			}	
			else
			{
				break;
			}
		}

	}

	var circle = stage.findOne("#" + name);
	circle.x(coordToPix(x));
	circle.y(coordToPix(y));
	circle.fill(couleur);
	
	if( vNb < eleme.kill)
	{
		var texte = stage.findOne("#txt_" + name);
		texte.x(coordToPix(x) + 5);
		texte.y(coordToPix(y) - 5);
		texte.visible(vDisplayName);		 
		if(eleme.isplayer==1)
		{
			texte.fill('#33cc33');			
		}
		else
		{ 
			texte.fill('white');
		}
	}
	else
	{
		var texte = stage.findOne("#txt_" + name);
		if(texte!==undefined)
		{
			texte.visible(false);
		}
		
	}
	

});	

// display gun shots
grpAttack.forEach(function(elem){
	elem.visible(false);
});

for(idmg=0;idmg < vObj.damages.length;idmg++)
	{
		var vDamage = vObj.damages[idmg];
		var vId = "dmg_" + vDamage.id;
		if(vDamage.elapsed > vNb)
		{
			break;
		} 
		if(vDamage.elapsed > vLastDisplayTime && vDamage.elapsed <= vNb)
		{
			var damager = vDamage.attacker;
			var victim = vDamage.victim; 

			var vPosDmg = stage.findOne("#" + damager);
			var vPosVic = stage.findOne("#" + victim);

			if(vPosDmg!==undefined && vPosVic!==undefined)
			{ 
				var x1 = vPosDmg.x();
				var y1 = vPosDmg.y();
				var x2 = vPosVic.x();
				var y2  = vPosVic.y();

				vPosVic.fill("#FFFF00");

				var dmg = stage.findOne("#" + vId);
				if(dmg!==undefined)
				{			
				    dmg.visible(true);
				}	
				else
				{
                    dmg = new Konva.Line({
				      points: [x1, y1, x2, y2 ],
				      stroke: 'yellow',
				      strokeWidth: 1,
				      id:vId,
				      name:vId
				    });
				    grpAttack.push(dmg);
				}   

			    vLayer.add(dmg);
			}			
		}
		else 
		{			 
							 
		}		
	}

$("#nbalive").html("Left : <strong>" + nbAlive + "</strong>");
vLastDisplayTime = vNb;
vLayer.draw();
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
	<i class="fa fa-refresh fa-spin"></i>&nbsp;Loading in progress... This may take few seconds !!
</div> 
<div class="row">
	<div col="col-xs-12">		
	@if($size!=="")
	<div class="scroll-container">
@endif	 
		<div id="container"></div>
		@if($size!=="")
	</div>
@endif	 
	</div>
</div> 

@endsection