@extends('layouts.app')
@php
    
    $status = Request::get('status') ? Request::get('status') : 'all';
    $entries = $tickets->perPage();
    $search = Request::get('search') ? Request::get('search') : '';
@endphp

@push('head')
    <script>
        $(document).ready(() => {
            const baseUrl = "http://127.0.0.1:8000/"
            const status = @json($status);
            const entries = @json($entries);
            const search = @json($search);
            const currentPage = @json($tickets->currentPage())

            $('#open').on('click', () => {

                handleStatuChange("Open")
            });

            $('#all').on('click', () => {
                handleStatuChange("")
            });

            $('#progress').on('click', () => {
                handleStatuChange("In+progress")
            });

            $('#closed').on('click', () => {
                handleStatuChange("Closed")
            });

            $(".footer__pagination__pages__pageButton").on("click", (event) => {
                const newPage = event.target.innerText;

                $.ajax({
                    type: "GET",
                    url: baseUrl +
                        `ticket?entries=${entries}&status=${status}${search.length ? "&search=" + search : ""}&page=${newPage}`,
                    success: function(response) {
                        $("#home").html(response)
                    }
                })
            })

            $("#search").on("submit", (event) => {
                event.preventDefault()
                const value = document.getElementById("searchInput").value

                $.ajax({
                    type: "GET",
                    url: baseUrl + `ticket?entries=${entries}&status=${status}&search=${value}`,
                    success: function(response) {
                        $("#home").html(response)
                    }
                })
            })

            $("#entries").on("change", (event) => {
                const value = event.target.value;

                $.ajax({
                    type: "GET",
                    url: baseUrl +
                        `ticket?entries=${value}&status=${status}${search.length ? "&search=" + search : ""}`,
                    success: function(response) {
                        $("#home").html(response)
                    }
                });
            })

            $("#nextPage").on("click", (event) => {
                loadPage(currentPage + 1)
            })
            $("#prevPage").on("click", (event) => {
                loadPage(currentPage - 1)
            })

            const loadPage = (newPageIndex) => {
                $.ajax({
                    type: "GET",
                    url: baseUrl +
                        `ticket?entries=${entries}&status=${status}${search.length ? "&search=" + search : ""}&page=${newPageIndex}`,
                    success: function(response) {
                        $("#home").html(response)
                    }
                });
            }

            const handleStatuChange = (filter) => {
                $.ajax({
                    type: "GET",
                    url: baseUrl + `ticket?status=${filter}`,
                    success: function(response) {
                        $("#home").html(response)
                    }
                });
            }
        });
    </script>
@endpush
<div id="home">
    <div class="home">
        <div class="header">
            <h1>Tickets</h1>
            <div class="header__buttons">
                <button class="header__buttons__logout"
                    onclick="event.preventDefault();
                document.getElementById('logout-form').submit();">
                    <img src="/images/logout.svg" height="20" width="20" alt="">
                    Logout
                </button>
                <form id="logout-form" action="{{ route('logout') }}" method="POST">
                    @csrf
                </form>
                <button class="header__buttons__addButton">
                    <img src="/images/plus_icon.svg" alt="plus">
                    Add new ticket
                </button>
            </div>
        </div>

        <div class="completion">
            <div class="completion__item">
                <h6 class="completion__item__h6">Total tickets</h6>
                <h1 class="completion__item__h1">{{ $number_of_tickets }}</h1>
            </div>
            <img src="/images/arrow_right.svg">
            <div class="completion__item">
                <h6 class="completion__item__h6">Open</h6>
                <h1 class="completion__item__h1">{{ $number_of_opened_tickets }}</h1>
            </div>
            <div class="completion__item">
                <h6 class="completion__item__h6">In Progress</h6>
                <h1 class="completion__item__h1">{{ $number_of_progress_tickets }}</h1>
            </div>
            <div class="completion__item">
                <h6 class="completion__item__h6">Closed</h6>
                <h1 class="completion__item__h1">{{ $number_of_completed_tickets }}</h1>
            </div>
            <div class="completion__bar">
                <div class="completion__bar__header">
                    <h6 class="completion__bar__header__h6">Completion percentage</h6>
                    <h2>{{ $completion_percentage }}%</h2>
                </div>
                <div class=" completion__bar__progressBar">
                    <div class="completion__bar__progressBar__progress" style="width: {{ $completion_percentage }}%">
                    </div>
                </div>
            </div>
        </div>
        <div class="statusHeader">
            <div class="statusHeader__statusButtons">
                <button id="all" @class([
                    'statusHeader__statusButtons__statusButton',
                    'statusHeader__statusButtons__statusButton__highlight' => $status == 'all',
                ])>All
                </button>
                <button id="open" @class([
                    'statusHeader__statusButtons__statusButton',
                    'statusHeader__statusButtons__statusButton__highlight' => $status == 'Open',
                ])>
                    Open
                </button>
                <button id="progress" @class([
                    'statusHeader__statusButtons__statusButton',
                    'statusHeader__statusButtons__statusButton__highlight' =>
                        $status == 'In progress',
                ])>
                    In progress
                </button>

                <button id="closed" @class([
                    'statusHeader__statusButtons__statusButton',
                    'statusHeader__statusButtons__statusButton__highlight' =>
                        $status == 'Closed',
                ])>Closed</button>
            </div>
        </div>
        <form id="search">
            <div class="filters">
                <div class="filters__search">
                    <button type="submit" class="filters__search__button">
                        <img class="filters__search__button__icon" src="/images/search.svg" width="20"
                            height="20" />
                    </button>
                    @csrf
                    <input id="searchInput" value="{{ $search }}" type="text" name="search"
                        class="filters__search__input" placeholder="Search">
                </div>
            </div>
        </form>
        <div class="tickets">
            <div class="tickets__content__header">
                <div class="tickets__content__header__name">Name</div>
                <div class="tickets__content__header__user">Client</div>
                <div class="tickets__content__header__agent">Assigne</div>
                <div class="tickets__content__header__date">Created At</div>
                <div class="tickets__content__header__status">Status</div>
                <div class="tickets__content__header__action">Action</div>
            </div>
            @foreach ($tickets as $ticket)
                <div id="row" class="tickets__content__row">
                    <div class="tickets__content__row__info">
                        <div class="tickets__content__row__info__title">{{ $ticket->title }}</div>
                        <div class="tickets__content__row__info__desc">{{ $ticket->description }}</div>
                    </div>
                    <div class="tickets__content__row__name">{{ $ticket->client->first_name }}
                        {{ $ticket->client->last_name }}
                    </div>
                    <div class="tickets__content__row__agent">{{ $ticket->user->first_name }}
                        {{ $ticket->user->last_name }}
                    </div>
                    <div class="tickets__content__row__date">{{ $ticket->created_at->format('d/m/Y') }}</div>
                    <div class="tickets__content__row__status">
                        <div @class([
                            'tickets__content__row__status__content',
                            'tickets__content__row__status__content--closed' =>
                                $ticket->status == 'Closed',
                            'tickets__content__row__status__content--open' => $ticket->status == 'Open',
                            'tickets__content__row__status__content--progress' =>
                                $ticket->status == 'In progress',
                        ])>{{ $ticket->status }}
                        </div>
                    </div>
                    <div class="tickets__content__row__actions">
                        <button class="tickets__content__row__actions__button">
                            Details
                            <img src="/images/double_arrow_right.svg" width="12" height="12"
                                alt="double_arrow_right">
                        </button>
                    </div>
                </div>
            @endforeach

        </div>
        <div class="footer">
            <div class="footer__entries">
                <select class="footer__entries__select" id="entries">
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
            @if ($tickets->hasPages())
                <div class="footer__pagination">
                    <button class='footer__pagination__previous' @disabled($tickets->currentPage() == 1) id="prevPage">
                        <img src="/images/pagination_arrow_left.svg" alt="arrow_left" width="10" height="10" />
                    </button>
                    <div class="footer__pagination__pages">
                        @for ($i = 1; $i <= $tickets->lastPage(); $i++)
                            <button @class([
                                'footer__pagination__pages__pageButton',
                                'footer__pagination__pages__pageButton--active' =>
                                    $tickets->currentPage() == $i,
                            ])>{{ $i }}</button>
                        @endfor
                    </div>
                    <button class='footer__pagination__next' @disabled($tickets->currentPage() == $tickets->lastPage()) id="nextPage">
                        <img src="/images/pagination_arrow_right.svg" alt="arrow_right" width="10"
                            height="10" />
                    </button>
                </div>
            @endif
        </div>
    </div>
</div>
