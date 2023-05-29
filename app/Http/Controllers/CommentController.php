<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Http\Requests\StoreCommentsRequest;
use App\Http\Requests\UpdateCommentsRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

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
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    private function validate_update(array $data)
    {
        return Validator::make($data, [
            'comment' => ['required', 'string', 'max:300'],
        ]);
    }

    /**
     * @param string $ticket_id
     * @return array App\Models\Comment
     */
    public function index(string $ticket_id)
    {
        $comments = $this->getComments($ticket_id);

        return view("comments", ["comments" => $comments]);
    }

    private function getComments(string $ticket_id)
    {
        $comments = Comment::where("ticket_id", $ticket_id)
            ->join("user", "comment.user_id", "=", "user.id")
            ->select(
                "user.id as user_id",
                "comment.id as comment_id",
                "comment.ticket_id",
                "user.first_name",
                "user.last_name",
                "comment.created_at",
                "comment.updated_at",
                "comment.comment"
            )
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

        return view("comments", ["comments" => $comments]);
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
    public function edit(Comment $comments)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreCommentsRequest $request, string $id)
    {

        $validator = $this->validate_update($request->all());
        if ($validator->fails()) {
            return response($validator->errors()->first(), 403);
        }


        Comment::where("id", $id)
            ->update(["comment" => $request->comment]);

        return redirect("/home")->with("status", "Updated successfully");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $comment = Comment::where("id", $request->comment_id)->select("ticket_id")->first();
        Comment::destroy($request->comment_id);
        $comments = $this->getComments($comment->ticket_id);

        return view("comments", ["comments" => $comments]);
    }
}
