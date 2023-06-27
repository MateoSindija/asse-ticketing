@php
    $reply_id = $reply->id ?? '';
@endphp

<script hidden>
    $(document).ready(() => {
        const baseUrl = "http://127.0.0.1:8000/";
        const commentID = @json($comment->id);

        $(".bodyModal__commentAdd__buttons__cancel").on("click", () => {
            $(".bodyModal").animate({
                width: "toggle"
            }, 350, () => {
                refetchComments();
            })
        })


        $("#commentEdit").on("submit", (event) => {
            event.preventDefault();
            const comment = $("#commentTextEdit").val();

            $.ajax({
                type: "PATCH",
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                },
                url: @json(isset($reply)) ? baseUrl +
                    `reply/${@json($reply_id)}` : baseUrl +
                    `comment/${commentID}`,
                data: @json(isset($reply)) ? {
                    commentID: commentID,
                    reply: comment,
                } : {
                    comment: comment
                },
                success: function(response) {
                    $(".bodyModal").animate({
                        width: "toggle"
                    }, 350, () => {
                        $("#bodyDetail").html(response);
                    })

                }
            });
        })

        const refetchComments = () => {
            const ticketID = @json($comment->ticket_id);
            $.ajax({
                type: "GET",
                url: baseUrl + `comment/ticket/${ticketID}`,
                success: function(response) {
                    $("#bodyDetail").html(response)
                }
            })
        }
    })
</script>

<div class="bodyModal">
    <form id="commentEdit" class="bodyModal__commentAdd">
        <textarea id="commentTextEdit" class="bodyModal__commentAdd__textarea" cols="20" rows="10">{{ isset($reply) ? $reply->reply : $comment->comment }}</textarea>
        <div class="bodyModal__commentAdd__buttons">
            <button class="bodyModal__commentAdd__buttons__cancel" type="button">Cancel</button>
            <button class="bodyModal__commentAdd__buttons__submit"
                type="submit">{{ isset($reply) ? 'Edit Reply' : 'Edit Comment' }}</button>
        </div>
    </form>
</div>
