<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Http\Requests\StoreClientsRequest;
use App\Http\Requests\UpdateClientsRequest;
use Illuminate\Database\Eloquent\Casts\Json;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Ramsey\Uuid\Uuid;

class ClientController extends Controller
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
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    private function validator(array $data)
    {

        return  Validator::make($data, [
            'first_name' => ['required', 'string', 'max:50'],
            'last_name' => ['required', 'string', 'max:50'],
            'phone' => ['required', 'string', 'max:20', 'unique:client,phone'],
            'email' => ['required', 'email:rfc,dns', 'unique:client,email', 'max:255']
        ]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input("search");
        $clients = $search ? $this->search($search) : Client::all();


        return $clients->toJson();
    }

    private function search(string | null $q)
    {

        if ($q == "") {
            return [];
        }

        $clients = Client::where(function ($query) use ($q) {
            $query->orWhere('first_name', 'ILIKE', '%' . $q . '%')
                ->orWhere('last_name', 'ILIKE', '%' . $q . '%')
                ->orWhere('email', 'ILIKE', '%' . $q . '%')
                ->orWhere('phone', 'ILIKE', '%' . $q . '%')
                ->orWhereRaw('CONCAT("client"."first_name",' . "' '" . ', "client"."last_name") ILIKE ' . "'%$q%'");
        });

        return $clients->get();
    }

    /**
     * Show the form for creating a new resource.
     *
     */
    public function create()
    {

        return view("clientAdd");
    }

    /**
     * Store a newly created resource in storage.
     *  @param Request $request
     *
     */
    public function store(Request $request)
    {
        $validator = $this->validator($request->all());
        if ($validator->fails()) {
            return response($validator->errors()->first(), 403);
        }

        $client = new Client();

        $client->first_name = $request->first_name;
        $client->last_name = $request->last_name;
        $client->phone = $request->phone;
        $client->email = $request->email;
        $client->save();

        return response('Client successfully added', 200);
    }

    /**
     * @param string $id 
     *
     */
    public function show(string $id)
    {

        $client = Client::find($id);

        // return view("home", compact("client", $client));
        return $client;
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Client $clients)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = $this->validator($request->all());
        if ($validator->fails()) {
            return response($validator->errors()->first());
        }



        Client::where("id", $id)
            ->update(["first_name" => $request->first_name, "last_name" => $request->last_name, "phone" => $request->phone, "email" => $request->email]);

        return response("Updated successfully", 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $client_id)
    {
        Client::destroy($client_id);
        return redirect("/home")->with("status", "Deleted successfully");
    }
}
