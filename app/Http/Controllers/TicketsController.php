<?php

namespace App\Http\Controllers;

use App\Models\Tickets;
use App\Http\Requests\StoreTicketsRequest;
use App\Http\Requests\UpdateTicketsRequest;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class TicketsController extends Controller
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
     * @param array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    private function validate_create($data)
    {
        return Validator::make($data, [
            'client_id' => ['required', 'uuid', 'exists:client,id'],
            'status' => ['required', 'string', 'max:20', Rule::in(["Open", "In progress", "Closed"])],
            'title' => ['required', 'string', 'max:100'],
            'description' => ['required', 'string', 'max:1000'],
        ]);
    }
    /**
     * @param array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    private function validate_update($data)
    {
        return Validator::make($data, [
            'status' => ['required', 'string', 'max:20', Rule::in(["Open", "In progress", "Closed"])],
            'title' => ['required', 'string', 'max:100'],
            'description' => ['required', 'string', 'max:1000'],
        ]);
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $ticket = Ticket::all()->toJson();

        return json_decode($ticket);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = $this->validate_create($request->all());
        if ($validator->fails()) {
            return response($validator->errors()->first());
        }



        $ticket = new Ticket();
        $ticket->user_id = Auth::id();
        $ticket->client_id = $request->client_id;
        $ticket->status = $request->status;
        $ticket->title = $request->title;
        $ticket->description = $request->description;
        $ticket->save();

        // return view("home", ["newComment" => $comment]);
        return $ticket;
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $ticket = Ticket::find($id);

        // return view("home", compact("comment", $comment));
        return $ticket;
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Ticket $tickets)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = $this->validate_update($request->all());
        if ($validator->fails()) {
            return response($validator->errors()->first());
        }



        Ticket::where("id", $id)
            ->update(["title" => $request->title, "description" => $request->description, "status" => $request->status]);

        return redirect("/home")->with("status", "Updated successfully");
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Ticket::destroy($id);

        return redirect("/home")->with("status", "Deleted successfully");
    }
}
