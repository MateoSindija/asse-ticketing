@extends('layouts.app')

@section('content')

<div class="login">
    <div class="login__card">
        <h1 class="login__card__head">{{ __('Register') }}</h1>

        <form method="POST" class="login__card__body" action="{{ route('register') }}">
            @csrf

            <div class="login__card__body__field">
                <label for="first_name" class="login__card__body__field__label">{{ __('First Name')
                    }}</label>

                <div class="login__card__body__field__container">
                    <input id="first_name" type="text"
                        class="login__card__body__field__container__input @error('first_name') login__card__body__field__container__input--error  @enderror"
                        name="first_name" value="{{ old('first_name') }}" required autocomplete="first_name" autofocus>

                    @error('first_name')
                    <span class="login__card__body__field__container__error" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>
            </div>
            <div class="login__card__body__field">
                <label for="last_name" class="login__card__body__field__label">{{ __('Last Name')
                    }}</label>

                <div class="login__card__body__field__container">
                    <input id="last_name" type="text"
                        class="login__card__body__field__container__input @error('last_name') login__card__body__field__container__input--error  @enderror"
                        name="last_name" value="{{ old('last_name') }}" required autocomplete="name" autofocus>

                    @error('last_name')
                    <span class="login__card__body__field__container__error" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>
            </div>

            <div class="login__card__body__field">
                <label for="email" class="login__card__body__field__label">{{ __('Email Address')
                    }}</label>

                <div class="login__card__body__field">
                    <input id="email" type="email"
                        class="login__card__body__field__container__input @error('email') login__card__body__field__container__input--error @enderror"
                        name="email" value="{{ old('email') }}" required autocomplete="email">

                    @error('email')
                    <span class="login__card__body__field__container__error" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>
            </div>

            <div class="login__card__body__field">
                <label for="password" class="login__card__body__field__label">{{ __('Password')
                    }}</label>

                <div class="login__card__body__field__container">
                    <input id="password" type="password"
                        class="login__card__body__field__container__input @error('password') login__card__body__field__container__input--error @enderror"
                        name="password" required autocomplete="new-password">

                    @error('password')
                    <span class="login__card__body__field__container__error" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>
            </div>

            <div class="login__card__body__field">
                <label for="password-confirm" class="login__card__body__field__label">{{ __('Confirm
                    Password') }}</label>

                <div class="login__card__body__field__container">
                    <input id="password-confirm" type="password" class="login__card__body__field__container__input"
                        name="password_confirmation" required autocomplete="new-password">
                </div>
            </div>

            <div class="login__card__body__confirm">
                <button type="submit" class="login__card__body__confirm__login">
                    {{ __('Register') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection