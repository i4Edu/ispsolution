<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class FakeRadiusController extends Controller
{
    public function authenticate(Request $request)
    {
        $username = $request->input('username');
        $password = $request->input('password');

        $user = User::where('username', $username)->first();

        if ($user && \Hash::check($password, $user->password)) {
            return response()->json([
                'Response-Packet' => 'Access-Accept'
            ]);
        }

        return response()->json([
            'Response-Packet' => 'Access-Reject'
        ]);
    }
}
