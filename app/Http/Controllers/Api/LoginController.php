<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller {
    // middleware
    public function __construct() {
        $this->middleware( 'auth:api', ['except' => ['login']] );
    }

    ///jwt login
    public function login( Request $request ) {
        $credentials = request( ['email', 'password'] );
        $token       = Auth::attempt( $credentials );

        if ( !$token ) {
            //if login data didn't match then show this error
            $jsonData = [
                'error'   => 'Your Credentials didn\'t match!',
                'success' => false,
            ];
            return response()->json( $jsonData, 401 );
        } else {
            //if login data match then create a token
            return $this->createJwtToken( $token ); ///we will find this $token from protected route
        }
    }

    //jwt logout
    public function logout() {
        Auth::logout();

        $jsonData = [
            'message' => 'You Successfully Logout!',
            'success' => true,
        ];
        return response()->json( $jsonData, 200 );
    }

    //auto refresh jwt token here
    public function refresh() {
        return $this->createJwtToken( Auth::refresh() );
    }

    //create jwt token in protected function -- if i use this route in public function then just use $this->funcitonName();
    protected function createJwtToken( $token ) {
        return response()->json( [
            'access_token' => $token,
            'token_type'   => 'bearer',
            'expires_in'   => Auth::factory()->getTTL() * 60,
        ] );
    }
}
