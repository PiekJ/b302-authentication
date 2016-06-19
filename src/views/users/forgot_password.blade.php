@extends('b302-auth::template')

@section('title')
    Forgot password - @parent
@stop

@section('content')
    {{ Confide::makeForgotPasswordForm()->render() }}
@stop