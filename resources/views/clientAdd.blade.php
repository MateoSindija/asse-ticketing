@extends('layouts.app')
@push('head')
    <script hidden>
        $(function() {
            const TOAST_DURATION = 2000;
            const baseUrl = "http://127.0.0.1:8000/";

            $("#first").on("input", (event) => {
                const value = event.target.value;
                getSearchValues("user", value);
            });

            $("#client").on("input", (event) => {
                const value = event.target.value;
                getSearchValues("client", value);
            });

            $(".bodyModal__buttons__delete").on("click", () => {
                $.ajax({
                    type: "DELETE",
                    url: baseUrl + `client/${@json($client->id)}`,
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                    },
                    success: function(response) {
                        $(".tickets").html(response)
                        $("#editClient").animate({
                            width: 'toggle'
                        }, 350, () => {
                            $("#bodyEditClient").empty()
                        });
                    }
                });
            })

            $("#ticketForm").on("submit", (event) => {
                event.preventDefault();
                const firstName = $("#name").val();
                const lastName = $("#surname").val();
                const phone = $("#phone").val();
                const email = $("#email").val();
                const url = @json(isset($client)) ? `client/${@json($client->id)}` :
                    "client";

                $.ajax({
                    type: @json(isset($client)) ? "PATCH" : "POST",
                    url: baseUrl + url,
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                    },
                    data: {
                        first_name: firstName,
                        last_name: lastName,
                        phone: phone,
                        email: email,
                    },
                    success: function(response) {
                        Toastify({
                            text: @json(isset($client)) ? "Client updated" :
                                "Client added",
                            duration: TOAST_DURATION,
                            close: true,
                            gravity: "top",
                            position: "center",
                            style: {
                                background: "#50C996",
                            },
                        }).showToast();
                        $(":input", "#ticketForm").val("")

                        if (@json(isset($client))) {
                            $(".tickets").html(response)
                            $("#editClient").animate({
                                width: 'toggle'
                            }, 350, () => {
                                $("#bodyEditClient").empty()
                            });

                        }
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
            });
        });
    </script>
@endpush


<form class="bodyModal" id="ticketForm">
    <div class="bodyModal__text">
        <label for="name" class="bodyModal__text__label">First name</label>
        <input type="text" id="name" class="bodyModal__text__input"
            @if (isset($client)) { value="{{ $client->first_name }}"} @endif
            placeholder="Client first name" />
    </div>
    <div class="bodyModal__text" id="clientContainer">
        <label for="surname" class="bodyModal__search__label">Last Name</label>
        <input type="text" @if (isset($client)) { value="{{ $client->last_name }}"} @endif
            id="surname" class="bodyModal__text__input" placeholder="Client last name" />
    </div>
    <div class="bodyModal__text" id="clientContainer">
        <label for="phone" class="bodyModal__search__label">Phone</label>
        <input type="text" id="phone" class="bodyModal__text__input"
            @if (isset($client)) { value="{{ $client->phone }}"} @endif
            placeholder="Client phone number" />
    </div>
    <div class="bodyModal__text" id="clientContainer">
        <label for="email" class="bodyModal__search__label">Email</label>
        <input type="email" @if (isset($client)) { value="{{ $client->email }}"} @endif id="email"
            class="bodyModal__text__input" placeholder="Client email" />
    </div>

    <div class="bodyModal__buttons">
        <button type="submit" class="bodyModal__buttons__add">
            @if (isset($client))
                Edit client
            @else
                Add Client
            @endif
        </button>
        @if (isset($client))
            <button type="button" class="bodyModal__buttons__delete">Delete Client</button>
        @endif
    </div>
</form>
