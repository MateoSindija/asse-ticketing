@extends('layouts.app')
@pushOnce('head')
    <script hidden>
        $(document).ready(() => {
            const TOAST_DURATION = 2000;
            const baseUrl = "http://127.0.0.1:8000/";

            $("#clientForm").on("submit", (event) => {
                event.preventDefault();
                const title = $("#ticketTitle").val();
                const description = $("#description").val();
                const firstName = $("#firstName").val();
                const lastName = $("#lastName").val();
                const phone = $("#phone").val();
                const email = $("#email").val();
                $.ajax({
                    type: "POST",
                    url: baseUrl + "ticket/store-from-client",
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                    },
                    data: {
                        "first_name": firstName,
                        "last_name": lastName,
                        "title": title,
                        "description": description,
                        "phone": phone,
                        "email": email,
                    },
                    success: function(response) {
                        Toastify({
                            text: response,
                            duration: TOAST_DURATION,
                            close: true,
                            gravity: "top",
                            position: "center",
                            style: {
                                background: "lightcoral",
                            },
                        }).showToast();
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
        })
    </script>
@endPushOnce
<div class="clientForm" id="clientForm">
    <form class="clientForm__form">
        <div class="clientForm__form__title">Submit your problem</div>
        <div class="clientForm__form__container">
            <div class="clientForm__form__container__inputLabel">
                <label class="label clientForm__form__container__inputLabel__label" for="firstName">First Name</label>
                <input class="input clientForm__form__container__inputLabel__input" type="text" id="firstName"
                    required>
            </div>
            <div class="clientForm__form__container__inputLabel">
                <label class="label clientForm__form__container__inputLabel__label" for="lastName">Last Name</label>
                <input class="input clientForm__form__container__inputLabel__input" type="text" id="lastName"
                    required>
            </div>
        </div>
        <div class="clientForm__form__container">
            <div class="clientForm__form__container__inputLabel">
                <label class="label clientForm__form__container__inputLabel__label" for="email">Email</label>
                <input class="input clientForm__form__container__inputLabel__input" id="email" type="email"
                    required>
            </div>
            <div class="clientForm__form__container__inputLabel">
                <label class="label clientForm__form__container__inputLabel__label" for="phone">Phone Number</label>
                <input class="input clientForm__form__container__inputLabel__input" id="phone" type="text"
                    required>
            </div>
        </div>
        <label class="label clientForm__form__label" for="ticketTitle">Ticket Title</label>
        <input class="input clientForm__form__input" type="text" id="ticketTitle" required>
        <label class="label clientForm__form__label" for="description">Problem Description</label>
        <textarea class="input clientForm__form__input" cols="20" rows="10" type="text" id="description" required></textarea>
        <button class="clientForm__form__button" type="submit">Submit</button>
    </form>
</div>
