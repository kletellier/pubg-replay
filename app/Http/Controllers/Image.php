<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use App\Classes\SimpleImage;
use App\Classes\Maps;

class Image extends Controller
{
     

    public function maplowres($id)
    {      
        switch ($id) {
            case Maps::MAP_ERANGEL:
            case Maps::MAP_BALTIC:
                $file = "erangel.jpg";
                break;
            case Maps::MAP_MIRAMAR:
                $file = "miramar.jpg";
                break;
            case Maps::MAP_SAVAGE:
                $file = "savage.jpg"; 
                break;
            case Maps::MAP_VIKENDI:
                $file = "vikendi.jpg"; 
                break;
            default:
                $file = "default.jpg";
                break;
        } 
        $path = 'assets/' . $file ;  
        return Storage::disk('local')->download($path);
    }

     
    public function itemicon($id)
    {
        $path = "assets/icons/" . $id . ".png";
        if(Storage::disk('local')->exists($path))
        {
            return Storage::disk('local')->download($path);
        }
        else
        {
            $image = new SimpleImage();
            $image->fromNew(75, 75, "#ffffff")->resize(75,75);
            
            return response()->stream(function() use ($image) {
                echo $image->toString();
            }
            , 200, [               
                'Content-Type'          => 'image/jpeg', 
                'Content-Disposition'   => 'attachment; filename="' . $id . '.png"' 
            ]);            
        }        
    }
}
