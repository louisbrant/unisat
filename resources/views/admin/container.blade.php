@extends('layouts.app')

@section('content')
<div class="bg-base-1 flex-fill">
    <div class="container py-3 my-3">
        @include($view)
    </div>
</div>
@endsection
@include('admin.sidebar')