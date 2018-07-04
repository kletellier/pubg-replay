
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
var vRadiusInit;
var vRadius = 4;
var vStroke = 1;
var vDisplayName = true;
var imageLoot = new Image(); 
var vLastDisplayTime = 0;
var grpAttack = new Array();

var vScale = 1;
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

    vSize= parseInt($(window).width()*0.92);

    if(vSize < 700)
    {
        vRadius = 3;
        vStroke = 1;
    } 

    vRadiusInit = vRadius;

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
        document.location.href = vUrlRoot;
    }
    else
    {       
        vDuration = vObj.duration;  
        displayMap();   
    }   
}

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

    var vUrlCarte = vUrlMap + vCarte;


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

// add players and circles
initCircles();

// init time slider
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
    if(newScale == 1)
    {
        newPos = {
            x: 0,
            y: 0
        };
    }
    vScale = newScale;
 
    stage.position(newPos);
    stage.batchDraw();
    adaptSize(vScale);
});


stage.on('tap',function(){
    var vScaleFactor = 1.4;
    var oldScale = stage.scaleX();

    var mousePointTo = {
        x: stage.getPointerPosition().x / oldScale - stage.x() / oldScale,
        y: stage.getPointerPosition().y / oldScale - stage.y() / oldScale,
    };

    var newScale =   oldScale * vScaleFactor;
    if(newScale < 1)
    {
        newScale = 1;
    }
    stage.scale({ x: newScale, y: newScale });

    var newPos = {
        x: -(mousePointTo.x - stage.getPointerPosition().x / newScale) * newScale,
        y: -(mousePointTo.y - stage.getPointerPosition().y / newScale) * newScale
    };
    if(newScale == 1)
    {
        newPos = {
            x: 0,
            y: 0
        };
    }
    vScale = newScale;
 
    stage.position(newPos);
    stage.batchDraw();
    adaptSize(vScale);
});

stage.on('dbltap',function(){
    vScale = 1;
     newPos = {
            x: 0,
            y: 0
        };
    stage.scale({ x: vScale, y: vScale });
    stage.position(newPos);
    stage.batchDraw();
    adaptSize(vScale);    
});


// start timer
vTimer = setInterval(RefreshMaps, 200);

};

// load image assets
imageObj.src = vUrlCarte;
imageLoot.src = vUrlLoot;

}

function adaptSize(scale)
{
    vFontSize = 6
    vRadius = 2;

    if(scale==1)
    {
        vRadius = vRadiusInit;
        vFontSize = 11;
    }

    vObj.players.forEach(function(eleme)
    {
        var vName = eleme.name;
        var vId = eleme.id;
        var vCircle = stage.findOne("#" + vId);
        var vTexte = stage.findOne("#txt_" + vId);
        vCircle.radius(vRadius);
        vTexte.fontSize(vFontSize);
    });
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
        id = eleme.id;
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
            id:id,
            perfectDrawEnabled : false
        });

        var text = new Konva.Text({
            x: 0,
            y: 0,
            text: name + " (" + eleme.winPlace + ")",
            fill: 'white',
            fontSize: 11,
            fontStyle: 'bold',
            id:"txt_" + id
        });

        if(eleme.isplayer==1)
        {
            text.fill('#33cc33');          
        }
        else
        { 
            text.fill('white');
        }

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

    for(igms=0;igms < vObj.gamestates.length;igms++)
    {
        var gs = vObj.gamestates[igms];
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
        else
        {
            break;
        }
    }

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
        id = eleme.id;
        pts = eleme.points;
        x = 0;
        y = 0;
        couleur = eleme.color;
        if(vNb>eleme.kill)
        {
        // death player
        // take last point
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

    var circle = stage.findOne("#" + id);    
    circle.x(coordToPix(x));
    circle.y(coordToPix(y));    
    circle.fill(couleur);
    
    if( vNb < eleme.kill)
    {
        var texte = stage.findOne("#txt_" + id);
        texte.x(coordToPix(x) + 5);
        texte.y(coordToPix(y) - 5);
        texte.visible(vDisplayName);        
    }
    else
    {
        var texte = stage.findOne("#txt_" + id);
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
        var damager = vDamage.attackerId;
        var victim = vDamage.victimId; 

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