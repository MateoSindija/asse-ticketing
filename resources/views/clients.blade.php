@extends('layouts.app')
@php
    $entries = $clients->perPage();
@endphp
@push('head')
    <script hidden>
        $(document).ready(() => {
            const currentPage = @json($clients->currentPage());
            const baseUrl = "http://127.0.0.1:8000/";
            const entries = @json($entries);
            $(".clients__row__actions__button").on("click", function(event) {
                const clientID = $(this).data('id')
                $.ajax({
                    type: "GET",
                    url: baseUrl +
                        `client/${clientID}/edit`,
                    success: function(response) {
                        $("#editClient").animate({
                            width: 'toggle'
                        }, 350, () => {
                            $("#bodyEditClient").html(response);
                        })
                    }
                });
            })


            $("#entriesClients").on("change", (event) => {
                const value = event.target.value;

                $.ajax({
                    type: "GET",
                    url: baseUrl +
                        `client?entries=${value}`,
                    success: function(response) {
                        $("#table").html(response)
                    }
                });
            })

            $(".footer__pagination__pages__pageButton").on("click", (event) => {
                const newPage = event.target.innerText;

                $.ajax({
                    type: "GET",
                    url: baseUrl +
                        `client?page=${newPage}&entries=${entries}`,
                    success: function(response) {
                        $("#table").html(response)
                    }
                })
            })

            $("#nextPageClient").on("click", (event) => {
                loadPage(currentPage + 1)
            })
            $("#prevPageClient").on("click", (event) => {
                loadPage(currentPage - 1)
            })

            const loadPage = (newPageIndex) => {
                $.ajax({
                    type: "GET",
                    url: baseUrl +
                        `client?page=${newPageIndex}&entries=${entries}`,
                    success: function(response) {
                        $("#table").html(response)
                    }
                });
            }
        })
    </script>
@endpush

<div class="clients">
    <div class="clients__header">
        <div class="clients__header__name">Name</div>
        <div class="clients__header__email">Email</div>
        <div class="clients__header__phone">Phone</div>
        <div class="clients__header__action">Action</div>
    </div>
    @foreach ($clients as $client)
        <div id="row" class="clients__row">
            <div class="clients__row__name">{{ $client->first_name }}
                {{ $client->last_name }}
            </div>
            <div class="clients__row__email">{{ $client->email }}
            </div>
            <div class="clients__row__phone">{{ $client->phone }}
            </div>
            <div class="clients__row__actions">
                <button data-id="{{ $client->id }}" class="clients__row__actions__button">
                    Edit
                    <img src="/images/double_arrow_right.svg" width="12" height="12" alt="double_arrow_right">
                </button>
            </div>
        </div>
    @endforeach
</div>
<div class="footer">
    @if ($clients->hasPages())
        <div class="footer__entries">
            <select class="footer__entries__select" id="entriesClients">
                <option class="footer__entries__select__option" @if ($entries == '20') selected @endif
                    value="20">20
                </option>
                <option class="footer__entries__select__option" @if ($entries == '50') selected @endif
                    value="50">50
                </option>
                <option class="footer__entries__select__option" @if ($entries == '100') selected @endif
                    value="100">
                    100
                </option>
            </select>
            <div class="footer__entries__text">Entries</div>
        </div>
        <div class="footer__pagination">
            <button class='footer__pagination__previous' @disabled($clients->currentPage() == 1) id="prevPageClient">
                <img src="/images/pagination_arrow_left.svg" alt="arrow_left" width="10" height="10" />
            </button>
            <div class="footer__pagination__pages">
                @for ($i = 1; $i <= $clients->lastPage(); $i++)
                    <button @class([
                        'footer__pagination__pages__pageButton',
                        'footer__pagination__pages__pageButton--active' =>
                            $clients->currentPage() == $i,
                    ])>{{ $i }}</button>
                @endfor
            </div>
            <button class='footer__pagination__next' @disabled($clients->currentPage() == $clients->lastPage()) id="nextPageClient">
                <img src="/images/pagination_arrow_right.svg" alt="arrow_right" width="10" height="10" />
            </button>
        </div>
    @endif
</div>
