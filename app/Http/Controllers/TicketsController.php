<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTicketsRequest;
use App\Http\Requests\UpdateTicketsRequest;
use App\Models\Client;
use App\Models\Message;
use App\Models\Ticket;
use App\Models\User;
use DateTime;
use Illuminate\Http\Client\Response;
use Pusher\Pusher;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TicketsController extends Controller
{
    private $options;
    private $pusher;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->options = array(
            'cluster' => 'eu',
            'useTLS' => true,
        );

        $this->pusher = new Pusher(
            env('PUSHER_APP_KEY'),
            env('PUSHER_APP_SECRET'),
            env('PUSHER_APP_ID'),
            $this->options
        );
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $this->middleware('auth');

        $status = $request->input("status");
        $entries = $request->input("entries");
        $search = $request->input("search");
        $start_date = $request->input("startDate");
        $end_date = $request->input("endDate");
        $client_id = $request->input("clientID");

        $tickets = $search ?
            Ticket::searchTicket(
                $search,
                $status,
                $entries,
                $start_date,
                $end_date
            )
            : Ticket::getTicketsWithStatusAndDate($status, $entries, $start_date,  $end_date, $client_id);


        return view((isset($client_id) ? "clientTickets" : "home"), array_merge(["tickets" => $tickets], $this->getHomeData($start_date, $end_date)));
    }

    private function getHomeData(string | null $start_date, string | null $end_date): array
    {
        $this->middleware('auth');
        $number_of_tickets = Ticket::count();
        $number_of_completed_tickets = Ticket::where("status", "=", "Closed")->count();
        $number_of_progress_tickets = Ticket::where("status", "=", "In progress")->count();
        $number_of_opened_tickets = Ticket::where("status", "=", "Open")->count();
        $completion_percentage = number_format((float)(($number_of_completed_tickets / $number_of_tickets) * 100), 1, ".", "");
        $latest_ticket_date = Ticket::latest()->select("created_at")->first()->created_at;
        $oldest_ticket_date =  Ticket::oldest()->select("created_at")->first()->created_at;
        $messages_count = Message::where("user_id", auth()->user()->id)->count();

        return [
            "number_of_tickets" => $number_of_tickets,
            "number_of_completed_tickets" => $number_of_completed_tickets,
            "number_of_progress_tickets" => $number_of_progress_tickets,
            "number_of_opened_tickets" => $number_of_opened_tickets,
            "completion_percentage" => $completion_percentage,
            "latest_ticket_date" => $latest_ticket_date,
            "oldest_ticket_date" => $oldest_ticket_date,
            "message_count" => $messages_count,
            "date_filter_start" => $start_date ? DateTime::createFromFormat('Y-m-d', $start_date) : $oldest_ticket_date,
            "date_filter_end" => $end_date ? DateTime::createFromFormat('Y-m-d', $end_date) : $latest_ticket_date,
        ];
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $this->middleware('auth');
        $users = User::all();
        $clients = Client::all();

        return view("ticket", ["users" => $users, "clients" => $clients]);
    }

    public function storeFromClient(StoreTicketsRequest $request)
    {
        $client = Client::firstOrCreate(
            [
                'email' =>  $request->input('email'),
                'phone' => $request->input('phone')
            ],
            [
                'first_name' =>  $request->input('first_name'),
                'last_name' =>  $request->input('last_name'),
            ]
        );
        Ticket::query()->create([
            "title" => $request->input('title'),
            "description" => $request->input('description'),
            "client_id" => $client->id,
            "status" => "Open"
        ]);

        return response('Ticket successfully added', 200);
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTicketsRequest $request)
    {
        $this->middleware('auth');

        Ticket::query()->create($request->all());

        Message::query()->create(["user_id" => $request->input("user_id"), "title" => $request->input("title")]);
        $this->pusher->trigger($request->input('user_id'), 'new-ticket', []);



        return response('Ticket successfully added', 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request): View
    {
        $this->middleware('auth');
        $description = $request->description;
        $id = $request->ticket_id;
        $ticket = Ticket::with('client', 'user')->find($id);

        if (isset($description)) {
            $ticket_description = Ticket::find($id)->select("description")->first();
            return view("ticketDescription", ["description" => $ticket_description->description]);
        }

        $comment_count = Ticket::find($id)->withCount(["comment" => function ($query) use ($id) {
            $query->where("ticket_id", $id);
        }])->first();


        return view("ticketInfo", ["ticket" => $ticket, "comment_count" => $comment_count->comment_count]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id): View
    {
        $this->middleware('auth');
        $ticket = Ticket::find($id);
        $users = User::all();
        $clients = Client::all();

        $current_user = Ticket::with("user")->find($id);
        $current_client = Ticket::with("client")->find($id);

        return view("ticket", [
            "isEdit" => true,
            "current_client" => $current_client->client,
            "current_user" => $current_user->user,
            "users" => $users,
            "clients" => $clients,
            "ticket" => $ticket
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTicketsRequest $request, Ticket $ticket): View
    {
        $this->middleware('auth');

        $ticket_id = $request->route("ticket_id");
        $ticket->whereId($ticket_id)->update($request->validated());

        $tickets = Ticket::getTicketsWithStatusAndDate("all", "20", null, null, null);

        return view("home", array_merge(["tickets" => $tickets], $this->getHomeData(null, null)));
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): View
    {
        $this->middleware('auth');
        Ticket::destroy($id);

        $tickets = Ticket::getTicketsWithStatusAndDate("all", "20", null, null, null);

        return view("home", array_merge(["tickets" => $tickets], $this->getHomeData(null, null)));
    }
}
