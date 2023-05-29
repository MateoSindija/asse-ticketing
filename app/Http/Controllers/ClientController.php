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
    public function edit(Client $clients)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreClientsRequest $request, string $id)
    {


        Client::where("id", $id)
            ->update($request->all());

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
