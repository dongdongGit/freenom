@extends('layouts.app')

@section('content')
<div class="wrapper-page">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-5 col-md-12 col-xs-12">
                <div class="card">
                    <div class="card-header border-bottom text-center">
                        <h4 class="card-title">{{ __('Login') }}</h4>
                    </div>
                    <div class="card-body">
                        <form class="form-horizontal m-t-20" method="POST" action="{{ route('login') }}">
                            @csrf
                            <div class="form-group">
                                <input class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}"
                                    id="email" type="email" name="email"
                                    value="{{ old('email') }}" required 
                                    placeholder="{{ __('E-Mail Address') }}">

                                @if ($errors->has('email'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('email') }}</strong>
                                </span>
                                @endif
                            </div>
                            <div class="form-group">
                                <input class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}"
                                    id="password" type="password" name="password" required
                                    placeholder="{{ __('Password') }}">

                                @if ($errors->has('password'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('password') }}</strong>
                                </span>
                                @endif
                            </div>
                            <div class="form-group">
                                <div class="custom-control custom-checkbox">

                                    <input type="checkbox" class="custom-control-input" id="customCheck1"
                                        {{ old('remember') ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="customCheck1">Remember me</label>
                                </div>
                            </div>
                            <div class="form-group text-center m-t-20">
                                <button class="btn btn-common btn-block" type="submit">{{ __('Login') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection