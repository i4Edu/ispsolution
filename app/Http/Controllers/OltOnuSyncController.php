<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OltOnuSyncController extends Controller
{
    public function sync()
    {
        return view('olt-onu-sync.sync');
    }
}
