@extends('layouts.app')

@section('content')
<div class="bg-base-1 flex-fill">
    <div class="container py-3 my-3">
        <div class="row">
            <div class="col-12">
                @include('images.' . $view)
            </div>
        </div>
    </div>
</div>
@endsection

@include('shared.sidebars.user')
