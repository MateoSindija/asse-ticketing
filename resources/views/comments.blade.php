@extends('layouts.app')
@php
    $userID = Auth::id();
    define('MINUTES_IN_MONTH', 43800);
    define('MINUTES_IN_DAY', 1440);
    define('MINUTES_IN_HOUR', 60);
    function calculateTime($created_at)
    {
        $delta_time = time() - strtotime($created_at);
        $minutes = floor($delta_time / 60);
    
        if ($minutes > MINUTES_IN_MONTH) {
            return floor($minutes / MINUTES_IN_MONTH) . ' months ago';
        } elseif ($minutes > MINUTES_IN_DAY) {
            return floor($minutes / MINUTES_IN_DAY) . ' days ago';
        } elseif ($minutes > MINUTES_IN_HOUR) {
            return floor($minutes / MINUTES_IN_HOUR) . ' hours ago';
        } elseif ($minutes <= 1) {
            return 'now';
        }
    
        return $minutes . ' minutes ago';
    }
    
    function checkIfEdited($created_at, $updated_at)
    {
        $isEdited = $created_at->format('Y-m-d') != $updated_at->format('Y-m-d');
    
        return $isEdited ? '(edited)' : '';
    }
@endphp
@pushOnce('head')
    <script>
        $(document).ready(() => {
            const baseUrl = "http://127.0.0.1:8000/";
            const TOAST_DURATION = 2000;
            const ticketID = @json($ticket_id);

            $(".bodyModal__comments__comment__buttons__delete").on("click", function() {
                const commentID = $(this).data('id');

                deleteComment(commentID);

            })

            $(".bodyModal__comments__comment__replies__buttons__edit").on("click", function() {
                const replyID = $(this).data('id');

                $(".bodyModal").animate({
                    width: "toggle"
                }, 350, () => {
                    $.ajax({
                        type: "GET",
                        url: baseUrl + `comment/${replyID}/edit`,
                        success: function(response) {
                            $("#bodyDetail").html(response);
                        }

                    })
                })
            });

            $(".bodyModal__comments__comment__replyForm").on("submit", function(event) {
                event.preventDefault();
                const commentID = event.currentTarget.id.replace("reply", "");
                const reply = event.currentTarget[0].value;

                $.ajax({
                    type: "POST",
                    url: baseUrl + "comment",
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                    },
                    data: {
                        comment: reply,
                        ticket_id: ticketID,
                        parent_id: commentID,
                    },
                    success: function(response) {
                        $("#bodyDetail").html(response);
                        Toastify({
                            text: "Reply added",
                            duration: TOAST_DURATION,
                            close: true,
                            gravity: "top",
                            position: "center",
                            style: {
                                background: "#50C996",
                            },
                        }).showToast();
                    }
                });

            })

            $(".bodyModal__comments__comment__replies__buttons__delete").on("click", function() {
                const commentID = $(this).data('reply');

                deleteComment(commentID);
            })


            $(".bodyModal__comments__comment__replyForm__buttons__cancel").on("click", function() {
                const commentID = $(this).data('id');

                $(`#reply${commentID}`).hide();
            })

            $(".bodyModal__comments__comment__buttons__reply").on("click", function() {
                const commentID = $(this).data('id');

                $(`#reply${commentID}`).animate({
                    width: "toggle"
                }, 350)
            })

            $(".bodyModal__comments__comment__buttons__edit").on("click", function() {
                const commentID = $(this).data('id');

                $(".bodyModal").animate({
                    width: "toggle"
                }, 350, () => {
                    $.ajax({
                        type: "GET",
                        url: baseUrl + `comment/${commentID}/edit`,
                        success: function(response) {
                            $("#bodyDetail").html(response);
                        }

                    })
                })

            })

            $("#commentForm").on("submit", (event) => {
                event.preventDefault();
                const comment = $("#commentText").val();

                $.ajax({
                    type: "POST",
                    url: baseUrl + 'comment',
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                    },
                    data: {
                        comment: comment,
                        ticket_id: ticketID
                    },
                    success: function(response) {
                        Toastify({
                            text: "Comment added",
                            duration: TOAST_DURATION,
                            close: true,
                            gravity: "top",
                            position: "center",
                            style: {
                                background: "#50C996",
                            },
                        }).showToast();
                        $("#commentBody").html(response);
                        const commentCountDiv = $(
                            ".newModal__selector__button__commentCount");
                        const currentCommentCount = parseInt(commentCountDiv
                            .text());

                        commentCountDiv.text(currentCommentCount + 1);

                    },
                    error: function(response) {
                        Toastify({
                            text: response.responseJSON.message,
                            duration: TOAST_DURATION,
                            close: true,
                            gravity: "top",
                            position: "center",
                            style: {
                                background: "lightcoral",
                            },
                        }).showToast();
                    },
                });
            })

            const deleteComment = (commentID) => {
                $.ajax({
                    type: "DELETE",
                    url: baseUrl + `comment/${commentID}`,
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                    },
                    success: function(response) {
                        Toastify({
                            text: "Comment deleted",
                            duration: TOAST_DURATION,
                            close: true,
                            gravity: "top",
                            position: "center",
                            style: {
                                background: "#50C996",
                            },
                        }).showToast();
                        $("#commentBody").html(response)
                        const commentCountDiv = $(
                            ".newModal__selector__button__commentCount");
                        const currentCommentCount = parseInt(commentCountDiv.text());

                        commentCountDiv.text(currentCommentCount - 1);

                    }
                });
            }

        })
    </script>
@endPushOnce
<div id="commentBody">
    <div class="bodyModal">
        <form id="commentForm" class="bodyModal__commentAdd">
            <textarea id="commentText" class="bodyModal__commentAdd__textarea" placeholder="Add comment" cols="20" rows="10"></textarea>
            <div class="bodyModal__commentAdd__buttons">
                <button class="bodyModal__commentAdd__buttons__submit" type="submit">Add Comment</button>
            </div>
        </form>
        <div class="bodyModal__comments">
            @foreach ($comments as $comment)
                <div class="bodyModal__comments__comment">
                    <div class="bodyModal__comments__comment__header">
                        <div class="bodyModal__comments__comment__header__name">
                            {{ $comment->user->first_name . ' ' . $comment->user->last_name }}
                        </div>
                        <div class="bodyModal__comments__comment__header__separator">&#x2022;</div>
                        <div class="bodyModal__comments__comment__header__date">
                            {{ calculateTime($comment->created_at) }}
                            {{ checkIfEdited($comment->created_at, $comment->updated_at) }}
                        </div>
                    </div>
                    <div class="bodyModal__comments__comment__body">{{ $comment->comment }}</div>
                    <div class="bodyModal__comments__comment__buttons">
                        <button type="button" class="bodyModal__comments__comment__buttons__reply"
                            data-id="{{ $comment->id }}">Reply</button>
                        @if ($userID == $comment->user_id)
                            <button type="button" class="bodyModal__comments__comment__buttons__edit"
                                data-id="{{ $comment->id }}">Edit</button>
                            <button type="button" class="bodyModal__comments__comment__buttons__delete"
                                data-id="{{ $comment->id }}">Delete</button>
                        @endif
                    </div>

                    <form id="{{ 'reply' . $comment->id }}" class="bodyModal__comments__comment__replyForm">
                        <textarea name="reply" placeholder="Add your reply" class="bodyModal__comments__comment__replyForm__textarea"
                            cols="30" rows="10"></textarea>
                        <div class="bodyModal__comments__comment__replyForm__buttons">
                            <button class="bodyModal__comments__comment__replyForm__buttons__cancel"
                                data-id="{{ $comment->id }}" type="button">Cancel</button>
                            <button class="bodyModal__comments__comment__replyForm__buttons__reply" type="submit">Add
                                Reply</button>
                        </div>
                    </form>

                    @foreach ($comment->children as $reply)
                        <div class="bodyModal__comments__comment__replies">
                            <div class="bodyModal__comments__comment__replies__header">
                                <div class="bodyModal__comments__comment__replies__header__name">
                                    {{ $reply->user->first_name . ' ' . $reply->user->last_name }}</div>
                                <div class="bodyModal__comments__comment__replies__header__separator">&#x2022;
                                </div>
                                <div class="bodyModal__comments__comment__replies__header__date">
                                    {{ calculateTime($reply->created_at) }}
                                    {{ checkIfEdited($reply->created_at, $reply->updated_at) }}
                                </div>
                            </div>
                            <div class="bodyModal__comments__comment__replies__reply">{{ $reply->comment }}</div>

                            @if ($userID == $reply->user_id)
                                <div class="bodyModal__comments__comment__replies__buttons">
                                    <button type="button" class="bodyModal__comments__comment__replies__buttons__edit"
                                        data-id="{{ $reply->id }}">Edit</button>
                                    <button type="button"
                                        class="bodyModal__comments__comment__replies__buttons__delete"
                                        data-reply="{{ $reply->id }}">Delete</button>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>
    </div>
</div>
