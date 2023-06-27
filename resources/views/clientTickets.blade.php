<script>
    $(document).ready(() => {
        const baseUrl = "http://127.0.0.1:8000/"

        $(".bodyModal__clientTicket__details").on("click", function() {
            const ticketID = $(this).data("id");
            $.ajax({
                type: "GET",
                url: baseUrl + `ticket/${ticketID}`,
                success: function(response) {
                    $("#ticketInfoModal").html(response).children().animate({
                        width: 'toggle'
                    }, 350)
                }
            });
        })
    })
</script>


<div class="bodyModal">
    @foreach ($tickets as $ticket)
        <div class="bodyModal__clientTicket">
            <div class="bodyModal__clientTicket__info">
                <div class="bodyModal__clientTicket__info__titleDate">
                    <div class="bodyModal__clientTicket__info__titleDate__title">{{ $ticket->title }}</div>
                    <div class="bodyModal__clientTicket__info__titleDate__separator">&#x2022;</div>
                    <div class="bodyModal__clientTicket__info__titleDate__date">
                        {{ $ticket->created_at->format('Y/m/d') }}</div>
                </div>
                <div class="bodyModal__clientTicket__info__desc">{{ $ticket->description }}</div>
            </div>
            <button class="bodyModal__clientTicket__details" data-id="{{ $ticket->id }}">
                Details
                <img src="/images/double_arrow_right.svg" width="12" height="12" alt="double_arrow_right">
            </button>
        </div>
    @endforeach
</div>
