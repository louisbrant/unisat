@extends('layouts.app')

@section('content')
    <div class="bg-base-1 d-flex flex-column flex-fill">
        <div class="container py-3 my-3 d-flex flex-column flex-fill">
            <div class="row d-flex flex-column flex-fill">
                <div class="col-12 d-flex flex-column flex-fill">
                    @include('chats.' . $view)
                </div>
            </div>
        </div>
    </div>
@endsection

@include('shared.sidebars.user')
