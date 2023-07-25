@component('mail::message')

{{-- Greeting --}}
# @lang('Hello!')

@lang('Please use the security code below to verify your identity.')

@component('mail::table')
<table>
<tbody>
<tr>
<td style="text-align: center;"><h1 style="margin-bottom: 0;">{{ $code }}</h1></td>
</tr>
</tbody>
</table>
@endcomponent

@lang('If you did not made this request, change your password immediately.')

{{-- Salutation --}}
@lang('Regards'),

{{ config('app.name') }}

@endcomponent