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
    private function validate_create(array $data)
    {
        return Validator::make($data, [
            'comment' => ['required', 'string', 'max:300'],
            'ticket_id' => ['required', 'uuid', 'exists:ticket,id'],
        ]);
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
        $comments = Comment::where("ticket_id", $ticket_id)->get();


        return $comments;
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



        $comment = new Comment();
        $comment->comment = $request->comment;
        $comment->ticket_id = $request->ticket_id;
        $comment->user_id = Auth::id();
        $comment->save();

        // return view("home", ["newComment" => $comment]);
        return $comment;
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
    public function update(Request $request, string $id)
    {

        $validator = $this->validate_update($request->all());
        if ($validator->fails()) {
            return response($validator->errors()->first());
        }


        Comment::where("id", $id)
            ->update(["comment" => $request->comment]);

        return redirect("/home")->with("status", "Updated successfully");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Comment::destroy($id);
        return redirect("/home")->with("status", "Deleted successfully");
    }
}
