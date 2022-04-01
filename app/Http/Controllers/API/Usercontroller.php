<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class Usercontroller extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['index', 'store']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            "name" => "string",
            "email" => "string|required",
            "status" => "integer",
            "role" => "integer",
            "password" => "string|required",
        ]);

        return User::create([
            "name" => $request->name,
            "email" => $request->email,
            "status" => 1,
            "role" => $request->role,
            "password" => bcrypt($request->password),
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (auth()->user()->role == 1)
            return auth()->user();

        return User::findOrFail($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        $request->validate([
            "name" => "string",
            "email" => "string",
            "password" => "string",
            "role" => "integer",
            "status" => "integer"

        ]);

        $user  = User::find($id);
        $user->name = $request->name;
        if (auth()->user()->role == 2) {

            $user->role = $request->role;
        }
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->save();
        return $user;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
