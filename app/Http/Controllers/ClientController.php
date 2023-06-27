<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Http\Requests\StoreClientsRequest;
use App\Http\Requests\UpdateClientsRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Client\Response;
use Illuminate\Http\Request;

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
     * Display a listing of the resource.
     */
    public function index(Request $request): View | String
    {
        $search = $request->input("search");
        $entries = $request->input("entries");
        if ($search) {
            return Client::searchClients($search)->toJson();
        }

        $clients = Client::orderBy("created_at", "DESC")->paginate($entries ? $entries : 20);

        return view("clients", ["clients" => $clients]);
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
    public function store(StoreClientsRequest $request)
    {

        Client::query()->create($request->all());

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
    public function edit(Request $request): View
    {
        $client = Client::find($request->client_id);
        return view("clientAdd", ["client" => $client]);
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateClientsRequest $request, Client $client): View
    {
        $client_id = $request->route("client_id");
        $client->whereId($client_id)->update($request->validated());
        $clients = $client->orderBy("created_at", "DESC")->paginate(20);

        return view("clients", ["clients" => $clients]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $client_id): View
    {
        Client::destroy($client_id);
        $clients = Client::orderBy("created_at", "desc")->paginate(20);

        return view("clients", ["clients" => $clients]);
    }
}
