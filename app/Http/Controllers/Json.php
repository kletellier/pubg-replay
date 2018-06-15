<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Classes\ReplayProvider;

class Json extends Controller
{
    public function path($id,$shards,$user)
    {
    	$ret = ReplayProvider::getReplay($id,$shards,$user);

        return response()
            ->json($ret);
    }
}
