<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Http\Requests\StoreCommentsRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
    public function index(string $ticket_id): View
    {
        $comments = Comment::getComments($ticket_id);


        return view("comments", ["comments" => $comments, "ticket_id" => $ticket_id]);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCommentsRequest $request): View
    {

        Comment::query()->create($request->all() + ["user_id" => Auth::id()]);
        $comments = Comment::getComments($request->ticket_id);

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
    public function edit(Request $request): View
    {
        $comment = Comment::query()->where("id", $request->comment_id)->first();

        return view("commentEdit", ["comment" => $comment]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreCommentsRequest $request, Comment $comment): View
    {

        $comment_id = $request->route("comment_id");
        $comment->whereId($comment_id)->update($request->validated());

        $comment = Comment::whereId($request->comment_id)->select("ticket_id")->first();
        $comments = Comment::getComments($comment->ticket_id);
        return view("comments", ["comments" => $comments, "ticket_id" => $comment->ticket_id]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request): View
    {
        $comment = Comment::where("id", $request->comment_id)->select("ticket_id")->first();
        Comment::destroy($request->comment_id);
        $comments = Comment::getComments($comment->ticket_id);
        return view("comments", ["comments" => $comments, "ticket_id" => $comment->ticket_id]);
    }
}
