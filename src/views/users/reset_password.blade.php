@extends('b302-auth::template')

@section('title')
    Reset password - @parent
@stop

@section('content')
    {{ Confide::makeResetPasswordForm()->render() }}
@stop