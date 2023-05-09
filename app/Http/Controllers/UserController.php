<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Ramsey\Uuid\Uuid;

class UserController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::all();

        return view("home", compact("users", $users));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }


    /**
     * Display the specified resource.
     */
    public function show(Uuid $id)
    {
        $user = User::find("id", $id);

        //return view("home", compact("user", $user));
        return $user;
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => ['required', 'string', 'max:50'],
            'last_name' => ['required', 'string', 'max:50'],
            'email' => ['required', 'email:rfc,dns', 'max:255']
        ]);

        if ($validator->fails()) {
            return response($validator->errors()->first());
        }

        //check if mail is taken
        $count = User::where("email", "=", $request->email)->where("id", "<>", $id)->count();

        if ($count >= 1) {
            return response("Mail taken",  409);
        }

        User::where("id", $id)
            ->update(["first_name" => $request->first_name, "last_name" => $request->last_name, "email" => $request->email]);

        return redirect("/home")->with("status", "Updated successfully");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        User::destroy($id);
        return redirect("/home")->with("status", "Deleted successfully");
    }
}
