<?php

namespace App\Http\Controllers;

use App\Models\Tickets;
use App\Http\Requests\StoreTicketsRequest;
use App\Http\Requests\UpdateTicketsRequest;
use App\Models\Client;
use App\Models\Comment;
use App\Models\Ticket;
use App\Models\User;
use Dotenv\Util\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
            'user_id' => ['required', 'uuid', 'exists:user,id'],
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
        $search = $request->input("search");

        $tickets = $search ?
            $this->search($search, $status, $entries)
            : $this->tickets($status, $entries);


        return view("home", array_merge(["tickets" => $tickets], $this->getHomeData()));
    }

    private function getHomeData(): array
    {
        $number_of_tickets = Ticket::count();
        $number_of_completed_tickets = Ticket::where("status", "=", "Closed")->count();
        $number_of_progress_tickets = Ticket::where("status", "=", "In progress")->count();
        $number_of_opened_tickets = Ticket::where("status", "=", "Open")->count();
        $completion_percentage = number_format((float)(($number_of_completed_tickets / $number_of_tickets) * 100), 1, ".", "");

        return [
            "number_of_tickets" => $number_of_tickets,
            "number_of_completed_tickets" => $number_of_completed_tickets,
            "number_of_progress_tickets" => $number_of_progress_tickets,
            "number_of_opened_tickets" => $number_of_opened_tickets,
            "completion_percentage" => $completion_percentage,
        ];
    }

    private function tickets(string | null $status, string | null $entries)
    {


        $query = Ticket::query();

        $query->whereHas("client");
        $query->whereHas("user");

        $query->when($status && $status != "all", function ($query) use ($status) {
            if ($status == "mine") {
                $userID = Auth::id();
                return $query->where("user_id", $userID);
            }
            return $query->where("status", $status);
        });


        return $query->orderBy("created_at", "desc")->paginate($entries ? $entries : 20);
    }

    private function search(string $q, string | null $status = "all", string | null $entries = "20")
    {

        $ticket = Ticket::join("client", "ticket.client_id", "=", "client.id")
            ->join("user", "ticket.user_id", "=", "user.id")
            ->when($status != "all", function ($query) use ($status) {
                if ($status == "mine") {
                    $userID = Auth::id();
                    return $query->where("ticket.user_id", $userID);
                }
                return $query->where("status", $status);
            })->where(function ($query) use ($q) {
                $query->orWhere('user.first_name', 'ILIKE', '%' . $q . '%')
                    ->orWhere('user.last_name', 'ILIKE', '%' . $q . '%')
                    ->orWhere('client.first_name', 'ILIKE', '%' . $q . '%')
                    ->orWhere('client.last_name', 'ILIKE', '%' . $q . '%')
                    ->orWhere('title', 'ILIKE', '%' . $q . '%')
                    ->orWhere('description', 'ILIKE', '%' . $q . '%')
                    ->orWhereRaw('CONCAT("user"."first_name",' . "' '" . ', "user"."last_name") ILIKE ' . "'%$q%'")
                    ->orWhereRaw('CONCAT("client"."first_name",' . "' '" . ', "client"."last_name") ILIKE ' . "'%$q%'");
            })->paginate($entries)->setPath('');


        return  $ticket;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $users = User::all();
        $clients = Client::all();

        return view("ticketAdd", ["users" => $users, "clients" => $clients]);
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = $this->validate_create($request->all());
        if ($validator->fails()) {
            return response($validator->errors()->first(), 403);
        }

        $ticket = new Ticket();
        $ticket->client_id = $request->client_id;
        $ticket->user_id = $request->user_id;
        $ticket->status = $request->status;
        $ticket->title = $request->title;
        $ticket->description = $request->description;
        $ticket->save();

        return response('Client successfully added', 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {

        $description = $request->description;
        $id = $request->ticket_id;
        $ticket = Ticket::where("ticket.id", $id)
            ->join("client", "ticket.client_id", "=", "client.id")
            ->join("user", "ticket.user_id", "=", "user.id")
            ->select(
                'ticket.id',
                'ticket.status',
                'ticket.title',
                'ticket.description',
                'ticket.created_at',
                'user.first_name as user_first_name',
                'user.last_name as user_last_name',
                'user.email as user_email',
                'client.first_name as client_first_name',
                'client.last_name as client_last_name',
                'client.email as client_email',
                'client.phone as client_phone',
            )
            ->first();

        if (isset($description)) {
            $ticket_description = Ticket::where("ticket.id", $id)->select("description")->first();
            return view("ticketDescription", ["description" => $ticket_description->description]);
        }

        $comment_count = Ticket::where("id", $id)->withCount(["comment" => function ($query) use ($id) {
            $query->where("ticket_id", $id);
        }])->get();

        return view("ticketInfo", ["ticket" => $ticket, "comment_count" => $comment_count[0]->comment_count]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $ticket = Ticket::find($id);
        $users = User::all();
        $clients = Client::all();

        $current_user = Ticket::where("ticket.id", $id)->join("user", "ticket.user_id", "=", "user.id")->first();
        $current_client = Ticket::where("ticket.id", $id)->join("client", "ticket.client_id", "=", "client.id")->first();

        return view("ticketAdd", [
            "isEdit" => true,
            "current_client" => $current_client,
            "current_user" => $current_user,
            "users" => $users,
            "clients" => $clients,
            "ticket" => $ticket
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = $this->validate_create($request->all());
        if ($validator->fails()) {
            return response($validator->errors()->first(), 403);
        }

        $tickets = $this->tickets("all", "20");


        Ticket::where("id", $id)
            ->update([
                "client_id" => $request->client_id,
                "user_id" => $request->user_id,
                "title" => $request->title,
                "description" => $request->description,
                "status" => $request->status
            ]);


        return view("home", array_merge(["tickets" => $tickets], $this->getHomeData()));
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Ticket::destroy($id);


        $tickets = $this->tickets("all", "20");


        return view("home", array_merge(["tickets" => $tickets], $this->getHomeData()));
    }
}
