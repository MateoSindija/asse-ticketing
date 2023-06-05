<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTicketsRequest;
use App\Http\Requests\UpdateTicketsRequest;
use App\Models\Client;
use App\Models\Ticket;
use App\Models\User;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $status = $request->input("status");
        $entries = $request->input("entries");
        $search = $request->input("search");
        $start_date = $request->input("startDate");
        $end_date = $request->input("endDate");

        $tickets = $search ?
            $this->search($search, $status, $entries, $start_date, $end_date)
            : $this->tickets($status, $entries, $start_date, $end_date);


        return view("home", array_merge(["tickets" => $tickets], $this->getHomeData($start_date, $end_date)));
    }

    private function getHomeData(string | null $start_date, string | null $end_date): array
    {
        $number_of_tickets = Ticket::count();
        $number_of_completed_tickets = Ticket::where("status", "=", "Closed")->count();
        $number_of_progress_tickets = Ticket::where("status", "=", "In progress")->count();
        $number_of_opened_tickets = Ticket::where("status", "=", "Open")->count();
        $completion_percentage = number_format((float)(($number_of_completed_tickets / $number_of_tickets) * 100), 1, ".", "");
        $latest_ticket_date = Ticket::latest()->select("created_at")->first()->created_at;
        $oldest_ticket_date =  Ticket::oldest()->select("created_at")->first()->created_at;

        return [
            "number_of_tickets" => $number_of_tickets,
            "number_of_completed_tickets" => $number_of_completed_tickets,
            "number_of_progress_tickets" => $number_of_progress_tickets,
            "number_of_opened_tickets" => $number_of_opened_tickets,
            "completion_percentage" => $completion_percentage,
            "latest_ticket_date" => $latest_ticket_date,
            "oldest_ticket_date" => $oldest_ticket_date,
            "date_filter_start" => $start_date ? DateTime::createFromFormat('Y-m-d', $start_date) : $oldest_ticket_date,
            "date_filter_end" => $end_date ? DateTime::createFromFormat('Y-m-d', $end_date) : $latest_ticket_date,
        ];
    }

    private function tickets(
        string | null $status,
        string | null $entries,
        string | null $start_date,
        string | null $end_date
    ) {


        $query = Ticket::query();

        $query->has("client");
        $query->has("user");

        $query->when($status && $status != "all", function ($query) use ($status) {
            if ($status == "mine") {
                $userID = Auth::id();
                return $query->where("user_id", $userID);
            }
            return $query->where("status", $status);
        });
        $query->when($start_date && $end_date, function ($query) use ($start_date, $end_date) {
            return $query->whereBetween("created_at", [$start_date, $end_date]);
        });

        return $query->orderBy("created_at", "desc")->paginate($entries ? $entries : 20);
    }

    private function search(
        string $q,
        string | null $status = "all",
        string | null $entries = "20",
        string | null $start_date,
        string | null $end_date
    ) {


        $ticket = Ticket::select('ticket.*')->join("client", "ticket.client_id", "=", "client.id")
            ->join("user", "ticket.user_id", "=", "user.id")
            ->when($status != "all", function ($query) use ($status) {
                if ($status == "mine") {
                    $userID = Auth::id();
                    return $query->where("ticket.user_id", $userID);
                }
                return $query->where("status", $status);
            })->when($start_date && $end_date, function ($query) use ($start_date, $end_date) {
                return $query->whereBetween("ticket.created_at", [$start_date, $end_date]);
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

        return view("ticket", ["users" => $users, "clients" => $clients]);
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTicketsRequest $request)
    {

        Ticket::query()->create($request->all());

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
        }])->first();


        return view("ticketInfo", ["ticket" => $ticket, "comment_count" => $comment_count->comment_count]);
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

        return view("ticket", [
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
    public function update(UpdateTicketsRequest $request, string $id)
    {

        $tickets = $this->tickets("all", "20", null, null);


        Ticket::where("id", $id)
            ->update($request->all());


        return view("home", array_merge(["tickets" => $tickets], $this->getHomeData(null, null)));
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Ticket::destroy($id);


        $tickets = $this->tickets("all", "20", null, null);


        return view("home", array_merge(["tickets" => $tickets], $this->getHomeData(null, null)));
    }
}
