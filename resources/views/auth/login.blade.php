@extends('layouts.app')

@section('content')

<div class="login">
    <div class="login__card">
        <h1 class="login__card__head">{{ __('Login') }}</h1>

        <form class="login__card__body" method="POST" action="{{ route('login') }}">
            @csrf
            <div class="login__card__body__field">
                <label for="email" class="login__card__body__field__label">{{ __('Email Address')
                    }}</label>

                <div class="login__card__body__field__container">
                    <input id="email" type="email"
                        class="login__card__body__field__container__input @error('email') login__card__body__field__container__input--error  @enderror"
                        name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

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
                        name="password" required autocomplete="current-password">

                    @error('password')
                    <span class="login__card__body__field__container__error" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>
            </div>

            <div class="login__card__body__remember">
                <input class="login__card__body__remember__checkbox" type="checkbox" name="remember" id="remember" {{
                    old('remember') ? 'checked' : '' }}>

                <label class="login__card__body__remember__label" for="remember">
                    {{ __('Remember Me') }}
                </label>
            </div>

            <div class="login__card__body__confirm">
                <button type="submit" class="login__card__body__confirm__login">
                    {{ __('Login') }}
                </button>

                @if (Route::has('password.request'))
                <a class="login__card__body__confirm__forgot" href="{{ route('password.request') }}">
                    {{ __('Forgot Your Password?') }}
                </a>
                @endif
            </div>
        </form>
    </div>
</div>

@endsection