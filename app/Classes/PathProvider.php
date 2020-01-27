<?php
 
namespace App\Classes;

use Stringy\Stringy as S;
use Carbon\Carbon;

class PathProvider   
{   

    public static function getFromTelemetry($json,$name)
    {
        $obj = json_decode($json);
        $collect = collect($obj);
        $gba = $collect->groupBy('_T')->toArray();

        $maxelapsed = 0;
        $team_id = "";
        $id = "";
        $carte = "";

        $ret = new \stdClass();


        $ftdate = function($sdate)
        {
            $obj = S::create($sdate)->replace("T"," ")->replace("Z","");
            $posdash = $obj->indexOfLast("."); 
            $date = $obj->substr(0,$posdash)->__toString();
             
            return  \Carbon\Carbon::createFromFormat("Y-m-d H:i:s",$date,"UTC");
        };

        $getItems = function($collection,$key)
        {
            return collect($collection[$key]);
        };

        $start = $getItems($gba,"LogMatchStart")->first();
        $startt = $start->_D;
        $datestart = $ftdate($startt);  
        if(isset($start->mapName))  
        {
            $carte = $start->mapName;
        }  
        if(isset($start->common->mapName))
        {
            $carte = $start->common->mapName;
        }         

        $position_array = array();
        $loot_array = array();
        $zone_array = array();
        $players_array = array();
        $participants_array = array();
        $damages_array = array();

        $ids = $getItems($gba,"LogMatchDefinition")->first();  
        $idtmp = $ids->MatchId;
        $obj = S::create($idtmp);
        $posdash = $obj->indexOfLast("."); 
        $id = $obj->substr($posdash+1)->__toString();             
             
        $players =$getItems($gba,"LogMatchEnd");
        foreach ($players as $elem) {
            foreach ($elem->characters as $char) {
                $participant = new \stdClass();
                $participant->name = $char->name;
                $participant->id = sha1($char->name);
                if($char->name==$name)
                {
                    $team_id = $char->teamId;
                }
                $participant->ranking= $char->ranking;
                $participant->teamId = $char->teamId;
                $participants_array[] = $participant;               
            } 
                    
        }

        $parts = collect($participants_array);
        $names = $parts->where('teamId',$team_id)->pluck('name')->toArray();

        $positions = $getItems($gba,"LogPlayerPosition");
        foreach ($positions as $elem) {
            $position = new \stdClass();
            $position->id = $id;
            $position->datez = $elem->_D;
            $position->name = $elem->character->name;
            $position->position_x = $elem->character->location->x;
            $position->position_y = $elem->character->location->y;
            $position->position_z = $elem->character->location->z; 
            $position->elapsed = $elem->elapsedTime;
            $position_array[] = $position;
        }       

        $locs = collect($position_array);     
        
        $loots = $getItems($gba,"LogCarePackageLand");
        foreach ($loots as $elem) {
            $loot = new \stdClass();
            $loot->id = $id;
            $loot->datez = $elem->_D; 
            $loot->position_x = $elem->itemPackage->location->x;
            $loot->position_y = $elem->itemPackage->location->y;
            $loot->position_z = $elem->itemPackage->location->z;
            $loot->elapsed = $datestart->diffInSeconds($ftdate($elem->_D));
            $loot_array[] = $loot;
        }    

        $zones = $getItems($gba,"LogGameStatePeriodic");
        foreach($zones as $elem)
        {
            $zone = new \stdClass();
            $zone->id = $id;
            $zone->elapsedTime = $elem->gameState->elapsedTime;
            $zone->numAliveTeams = $elem->gameState->numAliveTeams;
            $zone->numJoinPlayers = $elem->gameState->numJoinPlayers;
            $zone->numStartPlayers = $elem->gameState->numStartPlayers;
            $zone->numAlivePlayers = $elem->gameState->numAlivePlayers;
            $zone->safety_radius = $elem->gameState->safetyZoneRadius;
            $zone->safety_x = $elem->gameState->safetyZonePosition->x;
            $zone->safety_y = $elem->gameState->safetyZonePosition->y;
            $zone->bluezone_radius = $elem->gameState->poisonGasWarningRadius;
            $zone->bluezone_x = $elem->gameState->poisonGasWarningPosition->x;
            $zone->bluezone_y = $elem->gameState->poisonGasWarningPosition->y;
            $zone->redzone_radius = $elem->gameState->redZoneRadius;
            $zone->redzone_x = $elem->gameState->redZonePosition->x;
            $zone->redzone_y = $elem->gameState->redZonePosition->y;
            $zone->blackzone_radius = $elem->gameState->blackZoneRadius;
            $zone->blackzone_x = $elem->gameState->blackZonePosition->x;
            $zone->blackzone_y = $elem->gameState->blackZonePosition->y;
            $zone_array[] = $zone;
        }

        $attacks = $getItems($gba,'LogPlayerAttack');


        $damages = $getItems($gba,'LogPlayerTakeDamage')->where('damageTypeCategory','Damage_Gun');
        foreach ($damages as $elem) {
            $damage = new \stdClass();
            $damage->id = $elem->attackId;
            $damage->victim = $elem->victim->name;
            $damage->victimId = sha1($elem->victim->name);
            $damage->x1 = $elem->victim->location->x;
            $damage->y1 = $elem->victim->location->y;

            $attacker = $attacks->where('attackId',$damage->id)->first();
            $damage->attacker = $attacker->attacker->name;
            $damage->attackerId = sha1($attacker->attacker->name);
            $damage->x2 = $attacker->attacker->location->x;
            $damage->y2 = $attacker->attacker->location->y;
            $damage->elapsed = $datestart->diffInSeconds($ftdate($elem->_D));
            $damages_array[] = $damage;
        }

        foreach ($participants_array as $participant) {
            $name = $participant->name;
            $obj = new \stdClass();
            $obj->name = $name;
            $obj->id = $participant->id;
            $obj->color = in_array($name,$names) ? "#00ff00": "#ffffff";
            $obj->points = array();
            $obj->teamId = $participant->teamId;
            $obj->winPlace = $participant->ranking;
            $obj->isplayer = in_array($name,$names) ? 1 : 0;
            $tmp = $locs->where('name',$name)->sortBy('elapsed');
            $lastelapsed = 0;
            foreach ($tmp as $loc) {
                if($loc->elapsed>0)
                {
                    
                    $pt = new \stdClass();
                    $pt->x = $loc->position_x;
                    $pt->y = $loc->position_y;
                    $pt->elapsed = $loc->elapsed;
                    if($loc->elapsed>$lastelapsed)$lastelapsed=$loc->elapsed;
                    if($loc->elapsed>$maxelapsed)$maxelapsed=$loc->elapsed;
                    $obj->points[] = $pt;                           
                }                         
            }
            $obj->kill = $lastelapsed;
            $players_array[] = $obj;            
        }  

        $ret->id = $id;
        $ret->carte = $carte;
        $ret->players = $players_array;
        $ret->duration = $maxelapsed;
        $ret->gamestates =$zone_array;
        $ret->damages = $damages_array;
        $ret->loots = $loot_array; 

        return $ret;  
    }    
}