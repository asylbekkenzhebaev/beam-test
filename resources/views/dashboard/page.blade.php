@extends('layouts.main', ['title' => $title])

@section('content')
    @livewire($component, $parameters, key($component . '-' . md5(json_encode($parameters))))
@endsection
