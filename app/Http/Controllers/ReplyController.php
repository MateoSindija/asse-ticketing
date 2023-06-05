<?php

namespace App\Http\Controllers;

use App\Models\Reply;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreReplyRequest;
use App\Models\Comment;
use Illuminate\Support\Facades\Log;

use function Psy\debug;

class ReplyController extends Controller
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

    public function store(StoreReplyRequest $request)
    {
        Reply::query()->create($request->all() + ["user_id" => Auth::id()]);

        $comments = (new CommentController)->getComments($request->ticket_id);

        return view("comments", ["comments" => $comments, "ticket_id" => $request->ticket_id]);
    }

    public function edit(Request $request)
    {
        $reply = Reply::query()->where("id", $request->reply_id)->first();
        $comment = Comment::where("id", $reply->comment_id)->first();

        return view("commentEdit", ["reply" => $reply, "comment" => $comment]);
    }

    public function update(StoreReplyRequest $request, string $reply_id)
    {

        Reply::where("id", $reply_id)
            ->update(["reply" => $request->reply]);

        $comment = Comment::where("id", $request->commentID)->select("ticket_id")->first();
        $comments = (new CommentController)->getComments($comment->ticket_id);

        return view("comments", ["comments" => $comments, "ticket_id" => $request->ticket_id]);
    }

    public function destroy(Request $request)
    {
        $comment = Comment::where("id", $request->comment_id)->select("ticket_id")->first();
        Reply::destroy($request->reply_id);
        $comments = (new CommentController)->getComments($comment->ticket_id);
        return view("comments", ["comments" => $comments, "ticket_id" => $comment->ticket_id]);
    }
}
