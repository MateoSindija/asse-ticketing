<?php

namespace App\Http\Controllers;

use App\Models\Tickets;
use App\Http\Requests\StoreTicketsRequest;
use App\Http\Requests\UpdateTicketsRequest;
use App\Models\Ticket;
use Dotenv\Util\Str;
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
    public function index(Request $request)
    {

        $status = $request->input("status");
        $entries = $request->input("entries");


        $tickets = $request->input("search") ? $this->search($request->input("search"), $status, $entries) : $this->tickets($status, $entries);

        $number_of_tickets = Ticket::count();
        $number_of_completed_tickets = Ticket::where("status", "=", "Closed")->count();
        $number_of_progress_tickets = Ticket::where("status", "=", "In progress")->count();
        $number_of_opened_tickets = Ticket::where("status", "=", "Open")->count();
        $completion_percentage = number_format((float)(($number_of_completed_tickets / $number_of_tickets) * 100), 1, ".", "");


        return view("home", [
            "number_of_tickets" => $number_of_tickets,
            "number_of_completed_tickets" => $number_of_completed_tickets,
            "number_of_progress_tickets" => $number_of_progress_tickets,
            "number_of_opened_tickets" => $number_of_opened_tickets,
            "completion_percentage" => $completion_percentage,
            "tickets" => $tickets
        ]);
    }

    private function tickets(string | null $status, string | null $entries)
    {


        $query = Ticket::query();

        $query->whereHas("client");
        $query->whereHas("user");

        $query->when($status, function ($query) use ($status) {
            return $query->where("status", $status);
        });

        $query->when($entries, function ($query) use ($entries) {
            return $query->paginate($entries);
        })->when(!$entries, function ($query) {
            return $query->paginate(20);
        });

        return $query->get();
    }

    private function search(string $q, string $status, string $entries)
    {
        if ($q != "") {
            $ticket = Ticket::join("client", "ticket.client_id", "=", "client.id")
                ->when($status != "all", function ($query) use ($status) {
                    return $query->where("status", $status);
                })->where(function ($query) use ($q) {
                    $query->orWhere('first_name', 'ILIKE', '%' . trim(strtolower($q)) . '%')
                        ->orWhere('last_name', 'ILIKE', '%' . $q . '%')
                        // ->orWhereRaw('concat(first_name, last_name)', 'ILIKE', '%' . $q . '%')
                        ->orWhere('title', 'ILIKE', '%' . $q . '%')
                        ->orWhere('description', 'ILIKE', '%' . $q . '%');
                })->paginate($entries)->setPath('');

            if (count($ticket) > 0)
                return $ticket;
        }
        return  [];
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
