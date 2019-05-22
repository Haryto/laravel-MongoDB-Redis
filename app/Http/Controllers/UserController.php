<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

use App\MongoUser;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (Cache::has('users')) {
            $users = Cache::get('users');
        }
        else {
            $users = MongoUser::all();

            Cache::forever('users', $users);
        }

        return $users;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $result = [
            'success' => false
        ];

        if ( $user = MongoUser::create($request) ) {
            $result['success'] = true;
            $result['id'] = $user->_id;

            $users = Cache::pull('users');

            Cache::forever('users', $users->push($user));
        }

        return response()->json($result);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
        $user = MongoUser::findOrFail($id);

        $user->fill($request->all());

        if ( $user->save() ) {
            $users = Cache::pull('users');

            $userKey = $users->search(function ($item) use ($id) {
                return $item->getAttributes()['_id'] == $id;
            });

            $users->forget($userKey);

            Cache::forever('users', $users->push($user));
        }   

        return response()->json($user);
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
