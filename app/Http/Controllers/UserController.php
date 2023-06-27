<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use Illuminate\Database\Eloquent\Casts\Json;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Redirect;
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
    public function index(Request $request): String
    {

        $search = $request->input("search");
        $users = $search ? User::searchUsers($search) : User::all();

        return $users->toJson();
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
    public function update(UpdateUserRequest $request, User $user): Redirector
    {

        $user_id = $request->route("reply_id");

        //check if mail is taken
        $count = User::where("email", "=", $request->email)->where("id", "<>", $user_id)->count();

        if ($count >= 1) {
            return response("Mail taken",  403);
        }

        $user->whereId($user_id)->update($request->validated());

        return redirect("/home")->with("status", "Updated successfully");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): Redirector
    {
        User::destroy($id);
        return redirect("/home")->with("status", "Deleted successfully");
    }
}
