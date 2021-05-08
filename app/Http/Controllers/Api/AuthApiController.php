<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Throwable;

class AuthApiController extends Controller {
    // middleware
    public function __construct() {
        $this->middleware( 'auth:api', ['except' => ['login']] );
    }

    public function index() {
        $users    = User::all();
        $jsonData = [
            // note : 'UserResource::collection( $users )' eite use hobe multiple data pawar jonno ..ar single data pawar jonno use hobe just "new UserResource( $users )" etotuku
            'data'    => UserResource::collection( $users ), //eita use kora hoise specific data pawar jonno resourceController er sahajje..
            'success' => true,
        ];
        return response()->json( $jsonData, 401 );

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store( Request $request ) {
        //this validation i use for api
        $validator = Validator::make( $request->all(), [
            'name'     => 'required',
            'email'    => 'required|unique:users,email',
            'password' => 'required|min:8',
        ] );

        //validate message send through this
        if ( $validator->fails() ) {
            $jsonData = [
                'errors'  => $validator->errors(),
                'success' => false,
            ];
            return response()->json( $jsonData, 401 );
        }

        //ei try catch use kore data pass korle technical laravel er jei error gula ache oigula show kore na eita best way

        try {

            $saveData           = New User();
            $saveData->name     = $request->name;
            $saveData->email    = $request->email;
            $saveData->password = hash::make( $request->password );
            $FinalSave          = $saveData->save();

            //message
            if ( $FinalSave ) {
                $jsonData = [
                    'message' => 'User Successfuly Registerd!',
                    'success' => true,
                ];
                return response()->json( $jsonData, 200 );
            }

        } catch ( Throwable $th ) {
            $jsonData = [
                'message' => 'User Not Registerd for some technical issue!',
                'success' => false,
                'error'   => $th,
            ];
            return response()->json( $jsonData, 401 );
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show( $id ) {
        try {
            $users = User::where( 'id', $id )->first();

            if ( !empty( $users ) ) {
                $jsonData = [
                    'data'    => $users,
                    'message' => 'Showed Specific Data!',
                    'success' => true,
                ];
            } else {
                $jsonData = [
                    'message' => 'Data Not Found!',
                    'success' => false,
                ];
            }

            return response()->json( $jsonData, 200 );
        } catch ( Throwable $th ) {

            $jsonData = [
                'message' => 'User not showing for some technical issue!',
                'success' => false,
                'errors'  => $th,
            ];
            return response()->json( $jsonData, 400 );
        }

    }

    //update data
    public function update( Request $request, $id ) {
        //this validation i use for api
        $validator = Validator::make( $request->all(), [
            'name'     => 'required',
            'email'    => 'required|unique:users,email,' . $id,
            'password' => 'required|min:8',
        ] );

        //validate message send through this
        if ( $validator->fails() ) {
            $jsonData = [
                'errors'  => $validator->errors(),
                'success' => false,
            ];
            return response()->json( $jsonData, 401 );
        }

        //ei try catch use kore data pass korle technical laravel er jei error gula ache oigula show kore na eita best way

        try {

            $saveData           = User::find( $id );
            $saveData->name     = $request->name;
            $saveData->email    = $request->email;
            $saveData->password = hash::make( $request->password );
            $FinalSave          = $saveData->save();

            //message
            if ( $FinalSave ) {
                $jsonData = [
                    'message' => 'User Successfuly Updated!',
                    'success' => true,
                ];
                return response()->json( $jsonData, 200 );
            }

        } catch ( Throwable $th ) {
            $jsonData = [
                'message' => 'User Not Updated for some technical issue!',
                'success' => false,
                'error'   => $th,
            ];
            return response()->json( $jsonData, 401 );
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy( $id ) {
        try {
            $dlt = User::findOrFail( $id )->delete();

            $jsonData = [
                'data'    => $id,
                'message' => 'Delete data!',
                'success' => true,
            ];
            return response()->json( $jsonData, 200 );
        } catch ( Throwable $th ) {
            $jsonData = [
                'message' => 'Data not deleted for some technical issue!',
                'success' => false,
                'errors'  => $th,
            ];
            return response()->json( $jsonData, 400 );
        }
    }

}
