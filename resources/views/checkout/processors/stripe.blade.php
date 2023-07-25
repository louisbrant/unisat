@extends('layouts.wrapper')

@section('site_title', formatTitle([__('Confirm payment'), config('settings.title')]))

@section('body')
    <script src="https://js.stripe.com/v3/"></script>

    <script>
        'use strict';

        const stripe = Stripe('{{ config('settings.stripe_key') }}');

        stripe.redirectToCheckout({
            sessionId: '{{ $stripeSession->id }}'
        });
    </script>
@endsection