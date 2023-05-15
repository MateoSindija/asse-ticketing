@extends('layouts.app')
@php

$status = Request::get("status") ? Request::get("status") : "all";
$entries = Request::get("entries") ? Request::get("entries") : "20";
@endphp

@section('content')

<div class="home">
    <div class="header">
        <h1>Tickets</h1>
        <button class="header__addButton">
            <img src="/images/plus_icon.svg" alt="plus">
            Add new ticket</button>
    </div>

    <div class="completion">
        <div class="completion__item">
            <h6 class="completion__item__h6">Total tickets</h6>
            <h1 class="completion__item__h1">{{$number_of_tickets}}</h1>
        </div>
        <img src="/images/arrow_right.svg">
        <div class="completion__item">
            <h6 class="completion__item__h6">Open</h6>
            <h1 class="completion__item__h1">{{$number_of_opened_tickets}}</h1>
        </div>
        <div class="completion__item">
            <h6 class="completion__item__h6">In Progress</h6>
            <h1 class="completion__item__h1">{{$number_of_progress_tickets}}</h1>
        </div>
        <div class="completion__item">
            <h6 class="completion__item__h6">Closed</h6>
            <h1 class="completion__item__h1">{{$number_of_completed_tickets}}</h1>
        </div>
        <div class="completion__bar">
            <div class="completion__bar__header">
                <h6 class="completion__bar__header__h6">Completion percentage</h6>
                <h2>{{$completion_percentage}}%</h2>
            </div>
            <div class=" completion__bar__progressBar">
                <div class="completion__bar__progressBar__progress" style="width: {{$completion_percentage}}%">
                </div>
            </div>
        </div>
    </div>
    <div class="statusHeader">
        <div class="statusHeader__statusButtons">
            <button
                @class(['statusHeader__statusButtons__statusButton', 'statusHeader__statusButtons__statusButton__highlight'=>
                $status == "all"]) onclick="event.preventDefault();
                document.getElementById('status-all').submit();"
                >All</button>
            <form id="status-all" action="/ticket" method="GET">
            </form>
            <button
                @class(['statusHeader__statusButtons__statusButton', 'statusHeader__statusButtons__statusButton__highlight'=>
                $status == "Open"]) onclick="event.preventDefault();
                document.getElementById('status-open').submit();">
                Open

            </button>
            <form id="status-open" action="/ticket?status=Open" method="POST">
            </form>
            <button
                @class(['statusHeader__statusButtons__statusButton', 'statusHeader__statusButtons__statusButton__highlight'=>
                $status == "In progress"]) onclick="event.preventDefault();
                document.getElementById('status-progress').submit();">In progress</button>

            <form id="status-progress" action="/ticket?status=In+progress" method="POST">
            </form>
            <button
                @class(['statusHeader__statusButtons__statusButton', 'statusHeader__statusButtons__statusButton__highlight'=>
                $status == "Closed"]) onclick="event.preventDefault();
                document.getElementById('status-closed').submit();">Closed</button>
            <form id="status-closed" action="/ticket?status=Closed" method="POST">
            </form>
        </div>
    </div>
    <form action="/ticket?entries={{$entries}}&status={{$status}}" method="POST">
        <div class="filters">
            <div class="filters__search">
                <img class="filters__search__icon" src="/images/search.svg" width="20" height="20" />
                <input type="search" name="search" class="filters__search__input" placeholder="Search">
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
        <div class="tickets__content__row">
            <div class="tickets__content__row__info">
                <div class="tickets__content__row__info__title">{{$ticket->title}}</div>
                <div class="tickets__content__row__info__desc">{{$ticket->description}}</div>
            </div>
            <div class="tickets__content__row__name">{{$ticket->client->first_name}} {{$ticket->client->last_name}}
            </div>
            <div class="tickets__content__row__agent">{{$ticket->user->first_name}} {{$ticket->user->last_name}}</div>
            <div class="tickets__content__row__date">{{$ticket->created_at->format("d/m/Y");}}</div>
            <div class="tickets__content__row__status">
                <div @class([ "tickets__content__row__status__content"
                    ,"tickets__content__row__status__content--closed"=>
                    $ticket->status ==
                    "Closed",
                    "tickets__content__row__status__content--open"=>$ticket->status == "Open",
                    "tickets__content__row__status__content--progress"=>$ticket->status ==
                    "In progress"])>{{$ticket->status}}
                </div>
            </div>
            <div class="tickets__content__row__button">Actions</div>
        </div>
        @endforeach
    </div>
    <div class="footer">
        <div class="footer__entries">
            <select id="">
                <option selected value="20">20</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </select>
            <div>Entries</div>
        </div>
        {{-- {{$tickets->links()}} --}}
    </div>
    <form action="/home?status=open"></form>
</div>

@endsection