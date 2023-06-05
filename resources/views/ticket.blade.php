@extends('layouts.app')


@push('head')
    <script>
        $(document).ready(() => {
            let userID = @json(isset($current_user->id) ? $current_user->id : '');
            let clientID = @json(isset($current_client->id) ? $current_client->id : '');
            const TOAST_DURATION = 2000;
            const baseUrl = "http://127.0.0.1:8000/";
            $("#ticketForm").on("click", (e) => {
                if (e.target.id !== "bodyModal__search__dropDown" && !$(e.target).parents("#userDropdown")
                    .length && !$(e.target).parents("#clientDropdown").length) {
                    $("#clientDropdown").remove()
                    $("#userDropdown").remove()
                }
            });

            $("#user").on("input", (event) => {
                const value = event.target.value;
                getSearchValues("user", value);

            })

            $("#client").on("input", (event) => {
                const value = event.target.value;
                getSearchValues("client", value);

            })

            $("#addTicket").on("click", (event) => {
                event.preventDefault();
                const title = $("#title").val();
                const description = $("#desc").val();
                const ticketStatus = $("#status").val();

                $.ajax({
                    type: "POST",
                    url: baseUrl + `ticket`,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    },
                    data: {
                        "client_id": clientID,
                        "user_id": userID,
                        "status": ticketStatus,
                        "title": title,
                        "description": description
                    },
                    success: function(response) {
                        refetchBody("Ticket added");
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
                    }
                })
            })
            $("#saveChanges").on("click", (event) => {
                event.preventDefault();
                const title = $("#title").val();
                const description = $("#desc").val();
                const ticketID = @json(isset($ticket->id) ? $ticket->id : '');
                const ticketStatus = $("#status").val();

                $.ajax({
                    type: "PATCH",
                    url: baseUrl + `ticket/${ticketID}`,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    },
                    data: {
                        "client_id": clientID,
                        "user_id": userID,
                        "status": ticketStatus,
                        "title": title,
                        "description": description
                    },
                    success: function(response) {
                        refetchBody("Ticket updated");
                    },
                    error: function(response) {
                        if (response.status === 403) {
                            Toastify({
                                text: response.responseText,
                                duration: TOAST_DURATION,
                                close: true,
                                gravity: "top",
                                position: "center",
                                style: {
                                    background: "lightcoral",
                                },
                            }).showToast();
                        }
                    }
                })
            })
            $("#deleteTicket").on("click", (event) => {
                event.preventDefault();
                const ticketID = @json(isset($ticket->id) ? $ticket->id : '');

                $.ajax({
                    type: "DELETE",
                    url: baseUrl + `ticket/${ticketID}`,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    },
                    success: function(response) {
                        refetchBody("Ticket deleted");
                    },
                    error: function(response) {
                        if (response.status === 403) {
                            Toastify({
                                text: response.responseText,
                                duration: TOAST_DURATION,
                                close: true,
                                gravity: "top",
                                position: "center",
                                style: {
                                    background: "lightcoral",
                                },
                            }).showToast();
                        }
                    }
                })
            })


            $(document).on('click', '#userResultButton', (event) => {
                userID = event.currentTarget.value;
                const userName = event.currentTarget.children[0].innerText;
                $("#user").val(userName);
                $("#userDropdown").remove()
            });

            $(document).on('click', '#clientResultButton', (event) => {
                clientID = event.currentTarget.value;
                const userName = event.currentTarget.children[0].innerText;
                $("#client").val(userName);
                $("#clientDropdown").remove()

            });



            const refetchBody = (message) => {
                $.ajax({
                    type: "GET",
                    async: true,
                    url: baseUrl +
                        `ticket`,
                    success: function(newBody) {
                        Toastify({
                            text: message,
                            duration: TOAST_DURATION,
                            close: true,
                            gravity: "top",
                            position: "center",
                            style: {
                                background: "#50C996",
                            },
                        }).showToast();
                        $(":input", "#ticketForm").val("")
                        $("#home").html(newBody)
                    }
                });
            }

            const getSearchValues = (type, value) => {
                if (!value.length) {
                    $(`#${type}Dropdown`).remove();
                    return;
                }

                $.ajax({
                    type: "GET",
                    url: baseUrl + `${type}?search=${value}`,
                    success: function(response) {
                        const responseArray = JSON.parse(response);
                        $(`#${type}Dropdown`).remove();

                        if (!responseArray.length) return;


                        $(`#${type}Container`).append(
                            `<div class="bodyModal__search__dropDown" id="${type}Dropdown"></div>`
                        );

                        responseArray.map((user) => {
                            $(`#${type}Dropdown`).append(
                                `<button value=${user.id} class="bodyModal__search__dropDown__button" id="${type}ResultButton" type="button">
                                     <div class="bodyModal__search__dropDown__button__name">${user.first_name} ${user.last_name}</div>
                                     <div class="bodyModal__search__dropDown__button__email">${user.email}</div>
                                 </button>`
                            )
                        })
                    }
                })
            }
        })
    </script>
@endpush


<form class="bodyModal" id="ticketForm">
    <div class="bodyModal__text">
        <label for="title" class="bodyModal__text__label">Title</label>
        <input required type="text" @if (isset($ticket)) value="{{ $ticket->title }}" @endif
            id="title" class="bodyModal__text__input" placeholder="Your ticket title" />
    </div>
    <div class="bodyModal__search" id="clientContainer">
        <label for="client" class="bodyModal__search__label">Client</label>
        <input
            @if (isset($current_client)) value="{{ $current_client->first_name . ' ' . $current_client->last_name }}" @endif
            id="client" autocomplete="off" type="search" class="bodyModal__search__select"
            placeholder="The user for whom the ticket is" />
    </div>
    <div class="bodyModal__search" id="userContainer">
        <label for="user" class="bodyModal__search__label">Assign to</label>
        <input
            @if (isset($current_user)) value="{{ $current_user->first_name . ' ' . $current_user->last_name }}" @endif
            required id="user" autocomplete="off" type="search" class="bodyModal__search__select"
            placeholder="Assign an agent to resolve the ticket" />
    </div>
    <div class="bodyModal__text">
        <label for="status" class="bodyModal__text__label">Status</label>
        <select id="status" class="bodyModal__text__select">
            <option value="Open" @if (isset($ticket) && $ticket->status == 'Open') selected @endif>Open</option>
            <option value="In progress" @if (isset($ticket) && $ticket->status == 'In progress') selected @endif>In progress</option>
            @if (isset($isEdit))
                <option value="Closed" @if (isset($ticket) && $ticket->status == 'Closed') selected @endif>Closed</option>
            @endif

        </select>
    </div>
    <div class="bodyModal__text">
        <label for="desc" class="bodyModal__text__label">Description</label>
        <textarea required id="desc" cols="20" rows="10" class="bodyModal__text__input"
            placeholder="Description of a problem">{{ isset($ticket) ? $ticket->description : null }}
        </textarea>
    </div>
    <div class="bodyModal__buttons">
        @if (isset($isEdit))
            <button type="button" id="saveChanges" class="bodyModal__buttons__add">Save changes</button>
            <button type="button" id="deleteTicket" class="bodyModal__buttons__delete">Delete Ticket</button>
        @else
            <button type="submit" id="addTicket" class="bodyModal__buttons__add">Add ticket</button>
        @endif
    </div>
</form>
