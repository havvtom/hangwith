@extends('layouts.app')

@section('content')
    <my-app :user="{{Auth()->user()}}"></my-app>
@endsection
