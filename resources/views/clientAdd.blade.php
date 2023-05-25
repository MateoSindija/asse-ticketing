@extends('layouts.app')



@push('head')
    <script>
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

            $("#ticketForm").on("submit", (event) => {
                event.preventDefault();
                const firstName = $("#name").val();
                const lastName = $("#surname").val();
                const phone = $("#phone").val();
                const email = $("#email").val();

                $.ajax({
                    type: "POST",
                    url: baseUrl + `client`,
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
                            text: "Client added",
                            duration: TOAST_DURATION,
                            close: true,
                            gravity: "top",
                            position: "center",
                            style: {
                                background: "#50C996",
                            },
                        }).showToast();
                        $(":input", "#ticketForm").val("")
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
                    },
                });
            });
        });
    </script>
@endpush


<form class="bodyModal" id="ticketForm">
    <div class="bodyModal__text">
        <label for="name" class="bodyModal__text__label">First name</label>
        <input type="text" id="name" class="bodyModal__text__input" placeholder="Client first name" />
    </div>
    <div class="bodyModal__text" id="clientContainer">
        <label for="surname" class="bodyModal__search__label">Last Name</label>
        <input type="text" id="surname" class="bodyModal__text__input" placeholder="Client last name" />
    </div>
    <div class="bodyModal__text" id="clientContainer">
        <label for="phone" class="bodyModal__search__label">Phone</label>
        <input type="text" id="phone" class="bodyModal__text__input" placeholder="Client phone number" />
    </div>
    <div class="bodyModal__text" id="clientContainer">
        <label for="email" class="bodyModal__search__label">Email</label>
        <input type="email" id="email" class="bodyModal__text__input" placeholder="Client email" />
    </div>

    <div class="bodyModal__buttons">
        <button type="submit" class="bodyModal__buttons__add">Add Client</button>
    </div>
</form>
