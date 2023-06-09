@extends('layouts.app')
@php
    $status = Request::get('status') ? Request::get('status') : 'all';
    $entries = $tickets->perPage();
    $search = Request::get('search') ? Request::get('search') : '';
@endphp


@pushOnce('head')
    <script id="homeScript">
        $(document).ready(() => {
            const baseUrl = "http://127.0.0.1:8000/"
            const status = @json($status);
            const entries = @json($entries);
            const search = @json($search);
            const currentPage = @json($tickets->currentPage());
            const TOAST_DURATION = 2000;
            let dateFilterStart = @json($date_filter_start->format('Y-m-d'));
            let dateFilterEnd = @json($date_filter_end->format('Y-m-d'));
            let addActive = "ticket";
            let ticketID = "";
            Pusher.logToConsole = true;

            let pusher = new Pusher('fbdbd02a73d2e266af28', {
                cluster: 'eu'
            });

            let channelMyTickets = pusher.subscribe(@json(auth()->user()->id));
            channelMyTickets.bind('new-ticket', function(data) {
                Toastify({
                    text: "New Ticket added to your list",
                    duration: TOAST_DURATION,
                    close: true,
                    gravity: "top",
                    position: "center",
                    style: {
                        background: "#50C996",
                    },
                }).showToast();
                loadPage(currentPage);
            });


            //hide notification on outside click
            $(document).on("click", function(event) {
                if (!$(event.target).closest(".header__buttons__notification__button, .notificationItem")
                    .length) {
                    $("#notifications").slideUp("fast");
                }
            });

            $("#notificationButton").on("click", () => {
                $.ajax({
                    type: "GET",
                    url: baseUrl + "message",
                    success: function(response) {
                        $("#notifications").html(response).animate({
                            height: 'toggle'
                        }, 350)
                    }
                });
            })

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
            $('#mine').on('click', () => {
                handleStatuChange("mine")
            });
            $('#clients').on('click', () => {

                $("#closed").removeClass("statusHeader__statusButtons__statusButton__highlight");
                $("#progress").removeClass("statusHeader__statusButtons__statusButton__highlight");
                $("#open").removeClass("statusHeader__statusButtons__statusButton__highlight");
                $("#all").removeClass("statusHeader__statusButtons__statusButton__highlight");
                $("#mine").removeClass("statusHeader__statusButtons__statusButton__highlight");

                $("#clients").addClass("statusHeader__statusButtons__statusButton__highlight");

                $.ajax({
                    type: "GET",
                    url: baseUrl + "client",
                    success: function(response) {
                        $("#table").html(response);
                    }
                });
            });

            $(".footer__pagination__pages__pageButton").on("click", (event) => {
                const newPage = event.target.innerText;

                $.ajax({
                    type: "GET",
                    url: baseUrl +
                        `ticket?entries=${entries}&status=${status}${search.length ? "&search=" + search : ""}&page=${newPage}${addDateFilter()}`,
                    success: function(response) {
                        pusher.unsubscribe(@json(auth()->user()->id));
                        $("#home").replaceWith(response)
                    }
                })
            })


            $("#search").on("submit", (event) => {
                event.preventDefault()
                const value = document.getElementById("searchInput").value

                $.ajax({
                    type: "GET",
                    url: baseUrl +
                        `ticket?entries=${entries}&status=${status}&search=${value}${addDateFilter()}`,
                    success: function(response) {
                        pusher.unsubscribe(@json(auth()->user()->id));
                        $("#homeScript").remove();
                        $("#home").replaceWith(response)
                    }
                })
            })

            $("#exitAdd").on("click", (event) => {
                $("#newModal").animate({
                    width: 'toggle'
                }, 350, () => {
                    addClassToSelector(true)
                    $("#body").empty()
                });

            })

            $("#exitClient").on("click", (event) => {
                $("#editClient").animate({
                    width: 'toggle'
                }, 350, () => {
                    $("#bodyEditClient").empty()
                });

            })

            $("#new").on("click", () => {
                $("#newModal").animate({
                    width: 'toggle'
                }, 350, () => {

                    addClassToSelector(true)
                    $.ajax({
                        type: "GET",
                        url: baseUrl + `ticket/create`,
                        success: function(response) {
                            $("#body").replaceWith(response)
                        }
                    })
                });

            })

            $("#entries").on("change", (event) => {
                const value = event.target.value;

                $.ajax({
                    type: "GET",
                    url: baseUrl +
                        `ticket?entries=${value}&status=${status}${search.length ? "&search=" + search : ""}${addDateFilter()}`,
                    success: function(response) {
                        pusher.unsubscribe(@json(auth()->user()->id));
                        $("#homeScript").remove();
                        $("#home").replaceWith(response)
                    }
                });
            })

            $("#userSelector").on("click", () => {
                addClassToSelector(false)
                $("#body").empty()
                $.ajax({
                    type: "GET",
                    url: baseUrl + `client/create`,
                    success: function(response) {
                        $("#body").replaceWith(response)
                    }
                })
            })


            $("#ticketSelector").on("click", () => {
                addClassToSelector(true)
                $("#body").empty()
                $.ajax({
                    type: "GET",
                    url: baseUrl + `ticket/create`,
                    success: function(response) {
                        $("#body").replaceWith(response)
                    }
                })
            })

            $("#nextPage").on("click", (event) => {
                loadPage(currentPage + 1)
            })
            $("#prevPage").on("click", (event) => {
                loadPage(currentPage - 1)
            })

            $(".tickets__content__row__actions__button").on("click", function(event) {
                ticketID = $(this).data('id')
                $.ajax({
                    type: "GET",
                    url: baseUrl +
                        `ticket/${ticketID}`,
                    success: function(response) {
                        $("#ticketInfoModal").html(response).children().animate({
                            width: 'toggle'
                        }, 350, )
                    }
                });
            })

            $("#home").on("click", (e) => {
                if (!$(e.target).parents(".filters__calendar__popup").length && !$(e.target).parents(
                        ".filters__calendar").length) {
                    $("#calendar").empty()
                }
            });


            $(".filters__calendar__button").on("click", () => {
                let calendarEl = document.getElementById('calendar');
                const endDate = new Date(@json($latest_ticket_date));
                const startDate = new Date(@json($oldest_ticket_date));
                let calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'dayGridMonth',
                    selectable: true,
                    customButtons: {
                        defaultRange: {
                            text: 'Default',
                            click: () => {
                                $.ajax({
                                    type: "GET",
                                    url: baseUrl +
                                        `ticket?entries=${entries}&status=${status}${search.length ? "&search=" + search : ""}&startDate=${@json($oldest_ticket_date->format('Y-m-d'))}&endDate=${@json($latest_ticket_date->format('Y-m-d'))}`,
                                    success: function(response) {
                                        pusher.unsubscribe(
                                            @json(auth()->user()->id));
                                        $("#homeScript").remove();
                                        $("#home").replaceWith(response)
                                    }
                                });
                            }
                        }
                    },
                    headerToolbar: {
                        start: 'title',
                        end: 'defaultRange,prev,next',
                    },
                    events: [{
                        titlle: "selected",
                        start: dateFilterStart,
                        end: dateFilterEnd
                    }],
                    validRange: {
                        end: endDate,
                        start: startDate
                    },
                    select: (info) => {
                        dateFilterEnd = info.endStr;
                        dateFilterStart = info.startStr;
                        $.ajax({
                            type: "GET",
                            url: baseUrl +
                                `ticket?entries=${entries}&status=${status}${search.length ? "&search=" + search : ""}${addDateFilter()}`,
                            success: function(response) {
                                pusher.unsubscribe(@json(auth()->user()->id));
                                $("#homeScript").remove();
                                $("#home").replaceWith(response)
                            }
                        });
                    }

                });
                calendar.render();
            })

            const addClassToSelector = (isTicketActive) => {
                $("#clients").removeClass("statusHeader__statusButtons__statusButton__highlight");
                if (isTicketActive) {
                    $("#ticketSelector").addClass("newModal__selector__button--highlight")
                    $("#userSelector").removeClass("newModal__selector__button--highlight")
                } else {
                    $("#ticketSelector").removeClass("newModal__selector__button--highlight")
                    $("#userSelector").addClass("newModal__selector__button--highlight")
                }

            }

            const loadPage = (newPageIndex) => {
                $.ajax({
                    type: "GET",
                    url: baseUrl +
                        `ticket?entries=${entries}&status=${status}${search.length ? "&search=" + search : ""}&page=${newPageIndex}${addDateFilter()}`,
                    success: function(response) {
                        pusher.unsubscribe(@json(auth()->user()->id));
                        $("#homeScript").remove();
                        $("#home").replaceWith(response)
                    }
                });
            }

            const handleStatuChange = (filter) => {
                $.ajax({
                    type: "GET",
                    url: baseUrl + `ticket?status=${filter}`,
                    success: function(response) {
                        $("#homeScript").remove();
                        pusher.unsubscribe(@json(auth()->user()->id));
                        $("#home").replaceWith(response)
                    }
                });
            }


            const addDateFilter = () => {
                if (dateFilterEnd != @json($latest_ticket_date->format('Y-m-d')) || dateFilterStart !=
                    @json($oldest_ticket_date->format('Y-m-d'))) {
                    return `&startDate=${dateFilterStart}&endDate=${dateFilterEnd}`;
                }
                return "";
            }
        });
    </script>
@endPushOnce
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
                <div class="header__buttons__notification">
                    <button id="notificationButton" class="header__buttons__notification__button">
                        <img src="/images/bell.svg" alt="">
                        <div class="header__buttons__notification__button__counter">{{ $message_count }}</div>
                    </button>
                    <div class="header__buttons__notification__list" id="notifications"></div>
                </div>

                <button id="new" class="header__buttons__addButton">
                    <img src="/images/plus_icon.svg" alt="plus">
                    Add new
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

                <button id="mine" @class([
                    'statusHeader__statusButtons__statusButton',
                    'statusHeader__statusButtons__statusButton__highlight' => $status == 'mine',
                ])>My tickets</button>
                <button id="clients" class='statusHeader__statusButtons__statusButton'>Clients</button>
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

                <div class="filters__calendar">
                    <button type="button" class="filters__calendar__button">
                        <img src="/images/calendar.svg" alt="calendar" class="filters__calendar__button__icon">
                        <div class="filters__calendar__button__dates">
                            {{ $date_filter_end->format('d/m/Y') }} - {{ $date_filter_start->format('d/m/Y') }}
                        </div>
                    </button>
                    <div class="filters__calendar__popup">
                        <div id="calendar"></div>
                    </div>
                </div>
            </div>
        </form>
        <div id="table">
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
                        <div class="tickets__content__row__name">
                            {{ $ticket->client->first_name }}
                            {{ $ticket->client->last_name }}
                        </div>
                        <div class="tickets__content__row__agent">
                            @if ($ticket->user != null)
                                {{ $ticket->user->first_name }}
                                {{ $ticket->user->last_name }}
                            @endif
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
                            <button data-id="{{ $ticket->id }}" class="tickets__content__row__actions__button">
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
                        <option class="footer__entries__select__option"
                            @if ($entries == '20') selected @endif value="20">20
                        </option>
                        <option class="footer__entries__select__option"
                            @if ($entries == '50') selected @endif value="50">50
                        </option>
                        <option class="footer__entries__select__option"
                            @if ($entries == '100') selected @endif value="100">
                            100
                        </option>
                    </select>
                    <div class="footer__entries__text">Entries</div>
                </div>
                @if ($tickets->hasPages())
                    <div class="footer__pagination">
                        <button class='footer__pagination__previous' @disabled($tickets->currentPage() == 1) id="prevPage">
                            <img src="/images/pagination_arrow_left.svg" alt="arrow_left" width="10"
                                height="10" />
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
        <div id="newModal" class="newModal">
            <div class="newModal__header">
                <div class="newModal__header__title">Add new</div>
                <button class="newModal__header__exit" id="exitAdd">
                    <img src="/images/x-symbol.svg" alt="x">
                </button>
            </div>

            <div class="newModal__selector">
                <button id="ticketSelector" class="newModal__selector__button">Ticket</button>
                <button id="userSelector" class="newModal__selector__button">Client</button>
            </div>
            <div id="body"></div>
        </div>
        <div id="editClient" class="newModal">
            <div class="newModal__header">
                <div class="newModal__header__title">Client</div>
                <button class="newModal__header__exit" id="exitClient">
                    <img src="/images/x-symbol.svg" alt="x">
                </button>
            </div>
            <div class="newModal__selector">
                <button id="clientSelectorEdit" class='newModal__selector__button'>Edit</button>
                <button id="clientSelectorTickets" class='newModal__selector__button'>
                    Tickets
                </button>
            </div>
            <div id="bodyEditClient"></div>
        </div>
        <div id="ticketInfoModal"></div>
    </div>
</div>
