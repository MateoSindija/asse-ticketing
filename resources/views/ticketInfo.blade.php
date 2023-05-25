<script hidden>
    $(document).ready(() => {
        const baseUrl = "http://127.0.0.1:8000/"
        const ticketID = @json($ticket->id);
        let isEmpty = true;


        $("#exitDetails").on("click", (event) => {
            $("#detailsModal").animate({
                width: 'toggle'
            }, 350, () => {
                $("#ticketInfoModal").empty()

            });

        })


        $("#editSelectorDetails").on("click", () => {
            setHighlight("#editSelectorDetails")
            $("#bodyDetail").empty()
            $.ajax({
                type: "GET",
                url: baseUrl + `ticket/${ticketID}/edit`,
                success: function(response) {
                    $("#bodyDetail").html(response)
                }
            })
        })
        $("#ticketSelectorDetails").on("click", () => {
            $("#bodyDetail").empty();
            getDescription();
        })
        $("#commentsSelectorDetails").on("click", () => {
            setHighlight("#commentsSelectorDetails")
            $("#bodyDetail").empty()
            $.ajax({
                type: "GET",
                url: baseUrl + `comment/ticket/${ticketID}`,
                success: function(response) {
                    $("#bodyDetail").html(response)
                }
            })
        })

        const getDescription = () => {
            setHighlight("#ticketSelectorDetails")
            $.ajax({
                type: "GET",
                url: baseUrl + `ticket/${ticketID}?description=true`,
                success: function(response) {
                    isEmpty = false
                    $("#bodyDetail").html(response)
                }
            })
        }

        const setHighlight = (type) => {
            switch (type) {
                case "#ticketSelectorDetails":
                    $(type).addClass("newModal__selector__button--highlight")
                    $("#commentsSelectorDetails").removeClass("newModal__selector__button--highlight")
                    $("#editSelectorDetails").removeClass("newModal__selector__button--highlight")
                    break;
                case "#commentsSelectorDetails":
                    $("#ticketSelectorDetails").removeClass("newModal__selector__button--highlight")
                    $(type).addClass("newModal__selector__button--highlight")
                    $("#editSelectorDetails").removeClass("newModal__selector__button--highlight")
                    break;
                case "#editSelectorDetails":
                    $("#ticketSelectorDetails").removeClass("newModal__selector__button--highlight")
                    $("#commentsSelectorDetails").removeClass("newModal__selector__button--highlight")
                    $(type).addClass("newModal__selector__button--highlight")
                    break;

                default:
                    $(type).addClass("newModal__selector__button--highlight")
                    $("#commentsSelectorDetails").removeClass("newModal__selector__button--highlight")
                    $("#editSelectorDetails").removeClass("newModal__selector__button--highlight")
                    break;
            }
        }

        if (isEmpty) {
            getDescription();
        }
    })
</script>
<div id="detailsModal" class="newModal">
    <div class="newModal__header">
        <div class="newModal__header__title">{{ $ticket->title }}</div>
        <button class="newModal__header__exit" id="exitDetails">
            <img src="/images/x-symbol.svg" alt="x">
        </button>
    </div>

    <div class="newModal__info">
        <div class="newModal__info__content">
            <div class="newModal__info__content__title">Assigne</div>
            <div class="newModal__info__content__container">
                <div class="newModal__info__content__container__name">
                    {{ $ticket->user_first_name . ' ' . $ticket->user_last_name }}</div>
                <div class="newModal__info__content__container__email">{{ $ticket->user_email }}</div>
            </div>
        </div>
        <div class="newModal__info__content">
            <div class="newModal__info__content__title">Created</div>
            <div class="newModal__info__content__value">{{ $ticket->created_at->format('d/m/Y') }}</div>
        </div>
        <div class="newModal__info__content">
            <div class="newModal__info__content__title">Status</div>
            <div @class([
                'newModal__info__content__status',
                'newModal__info__content__status--progress' =>
                    $ticket->status == 'In progress',
                'newModal__info__content__status--closed' => $ticket->status == 'Closed',
                'newModal__info__content__status--open' => $ticket->status == 'Open',
            ])> {{ $ticket->status }}</div>
        </div>
        <div class="newModal__info__content">
            <div class="newModal__info__content__title">Client</div>
            <div class="newModal__info__content__container">
                <div class="newModal__info__content__container__name">
                    {{ $ticket->client_first_name . ' ' . $ticket->client_last_name }}</div>
                <div>{{ $ticket->client_email }}</div>
            </div>

        </div>
        <div class="newModal__info__content">
            <div class="newModal__info__content__title">Phone</div>
            <div class="newModal__info__content__value"> {{ $ticket->client_phone }}</div>
        </div>
    </div>

    <div class="newModal__selector">
        <button id="ticketSelectorDetails" class='newModal__selector__button'>Description</button>
        <button id="editSelectorDetails" class='newModal__selector__button'>Edit</button>
        <button id="commentsSelectorDetails" class='newModal__selector__button'>
            <div>Comments</div>
            <div class="newModal__selector__button__commentCount">{{ $comment_count }}</div>
        </button>
    </div>
    <div id="bodyDetail"></div>
</div>
