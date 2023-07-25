@extends('layouts.app')

@section('content')
<div class="bg-base-1 flex-fill">
    <div class="container py-3 my-3">
        <div class="row">
            <div class="col-12">
                @if(in_array($view, ['new', 'edit', 'show']))
                    @include('templates.' . $view)
                @else
                    @include('templates.template.' . $view)
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@include('shared.sidebars.user')
