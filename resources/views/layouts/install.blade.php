@extends('layouts.wrapper')

@section('body')
    <body class="d-flex flex-column">
        <div class="d-flex flex-column flex-fill">
            @yield('content')
        </div>
    </body>
@endsection