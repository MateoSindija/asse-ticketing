<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Http\Requests\StoreCommentsRequest;
use App\Http\Requests\UpdateCommentsRequest;
use Illuminate\Http\Request;
use Illuminate\Log\Logger;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

use function Psy\debug;

class CommentController extends Controller
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
     * @param string $ticket_id
     * @return array App\Models\Comment
     */
    public function index(string $ticket_id)
    {
        $comments = $this->getComments($ticket_id);


        return view("comments", ["comments" => $comments, "ticket_id" => $ticket_id]);
    }

    public function getComments(string $ticket_id)
    {

        $comments = Comment::where("ticket_id", $ticket_id)
            ->with("replies", "user", "replies.user")
            ->orderBy("comment.created_at", "asc")
            ->get();

        return $comments;
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCommentsRequest $request)
    {

        Comment::query()->create($request->all() + ["user_id" => Auth::id()]);

        $comments = $this->getComments($request->ticket_id);

        return view("comments", ["comments" => $comments, "ticket_id" => $request->ticket_id]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $comment = Comment::find($id);

        // return view("home", compact("comment", $comment));
        return $comment;
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request)
    {
        $comment = Comment::query()->where("id", $request->comment_id)->first();

        return view("commentEdit", ["comment" => $comment]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreCommentsRequest $request, string $id)
    {

        Comment::where("id", $id)
            ->update(["comment" => $request->comment]);

        $comment = Comment::where("id", $request->comment_id)->select("ticket_id")->first();
        $comments = $this->getComments($comment->ticket_id);
        return view("comments", ["comments" => $comments, "ticket_id" => $comment->ticket_id]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $comment = Comment::where("id", $request->comment_id)->select("ticket_id")->first();
        Comment::destroy($request->comment_id);
        $comments = $this->getComments($comment->ticket_id);
        return view("comments", ["comments" => $comments, "ticket_id" => $comment->ticket_id]);
    }
}
