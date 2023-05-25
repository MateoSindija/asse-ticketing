@extends('layouts.app')
@php
    $ticketID = Request::route('ticket_id');
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

@push('head')
    <script>
        $(document).ready(() => {
            const baseUrl = "http://127.0.0.1:8000/";
            const TOAST_DURATION = 2000;

            $("#commentForm").on("submit", (event) => {
                event.preventDefault();
                const comment = $("#commentText").val();
                const ticketID = @json($ticketID);
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
                        $("#commentBody").html(response)

                    }
                });
            })
        })
    </script>
@endpush

<div id="commentBody">
    <div class="bodyModal">
        <form id="commentForm" class="bodyModal__commentAdd">
            <textarea id="commentText" class="bodyModal__commentAdd__textarea" placeholder="Add comment" cols="20" rows="10"></textarea>
            <button class="bodyModal__commentAdd__submit" type="submit">Add Comment</button>
        </form>
        <div class="bodyModal__comments">
            @foreach ($comments as $comment)
                <div class="bodyModal__comments__comment">
                    <div class="bodyModal__comments__comment__header">
                        <div class="bodyModal__comments__comment__header__name">
                            {{ $comment->first_name . ' ' . $comment->last_name }}
                        </div>
                        <div class="bodyModal__comments__comment__header__separator">&#x2022;</div>
                        <div class="bodyModal__comments__comment__header__date">
                            {{ calculateTime($comment->created_at) }}
                            {{ checkIfEdited($comment->created_at, $comment->updated_at) }}</div>
                    </div>
                    <div class="bodyModal__comments__comment__body">{{ $comment->comment }}</div>
                </div>
            @endforeach
        </div>
    </div>
</div>
