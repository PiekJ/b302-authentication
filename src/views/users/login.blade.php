@extends('b302-auth::template')

@section('title')
    Login - @parent
@stop

@section('content')
    {{ Confide::makeLoginForm()->render() }}
@stop