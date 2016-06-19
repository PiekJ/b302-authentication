@extends('b302-auth::template')

@section('title')
    Signup - @parent
@stop

@section('content')
    {{ Confide::makeSignupForm()->render() }}
@stop