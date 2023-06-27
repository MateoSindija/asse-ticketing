@extends('layouts.app')

@php
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
@endphp
@pushOnce('head')
    <script hidden>
        $(document).ready(() => {
            const baseUrl = "http://127.0.0.1:8000/"
            $(".notificationItem__button").on("click", function() {
                const notificationId = $(this).data('id')
                $.ajax({
                    type: "DELETE",
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                    },
                    url: baseUrl + "message/" + notificationId,
                    success: function(response) {
                        $("#notifications").empty();
                        $("#notifications").html(response);
                        const notificationCountDiv = $(
                            ".header__buttons__notification__button__counter");
                        const currentNotificationCount = parseInt(notificationCountDiv
                            .text());

                        notificationCountDiv.text(currentNotificationCount - 1);
                    }
                });
            })
        })
    </script>
@endPushOnce



@foreach ($messages as $message)
    <div class="notificationItem">
        <div class="notificationItem__info">
            <div class="notificationItem__info__title">{{ $message->title }}</div>
            <div class="notificationItem__info__time">{{ calculateTime($message->created_at) }}</div>
        </div>
        <button data-id="{{ $message->id }}" class="notificationItem__button">
            <img src="/images/check.svg" alt="check">
        </button>
    </div>
@endforeach
