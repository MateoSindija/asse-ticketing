<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class MessageController extends Controller
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
    public function index(Request $request): View
    {
        $messages = Message::where("user_id", auth()->user()->id)->get();

        return view("notificationList", ["messages" => $messages]);
    }

    public function destroy(string $message_id)
    {
        Message::destroy($message_id);
        $messages = Message::where("user_id", auth()->user()->id)->get();
        return view("notificationList", ["messages" => $messages]);
    }
}
