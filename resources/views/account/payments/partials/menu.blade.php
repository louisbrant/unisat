<a href="#" class="btn d-flex align-items-center btn-sm text-primary" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">@include('icons.more-horiz', ['class' => 'fill-current width-4 height-4'])&#8203;</a>

<div class="dropdown-menu {{ (__('lang_dir') == 'rtl' ? 'dropdown-menu' : 'dropdown-menu-right') }} border-0 shadow">
    <a class="dropdown-item d-flex align-items-center" href="{{ request()->is('admin/*') ? route('admin.payments.edit', $payment->id) : route('account.payments.edit', $payment->id) }}">@include('icons.edit', ['class' => 'text-muted fill-current width-4 height-4 '.(__('lang_dir') == 'rtl' ? 'ml-3' : 'mr-3')]) {{ __('Edit') }}</a>

    @if((request()->is('admin/*') && in_array($payment->status, ['completed', 'cancelled'])) || $payment->status == 'completed')
        <a class="dropdown-item d-flex align-items-center" href="{{ request()->is('admin/*') ? route('admin.invoices.show', $payment->id) : route('account.invoices.show', $payment->id) }}">@include('icons.invoice', ['class' => 'text-muted fill-current width-4 height-4 '.(__('lang_dir') == 'rtl' ? 'ml-3' : 'mr-3')]) {{ __('Invoice') }}</a>
    @endif

    @if($payment->status == 'pending')
        @if(request()->is('admin/*'))
            <div class="dropdown-divider"></div>
            <a class="dropdown-item text-success d-flex align-items-center" href="#" data-toggle="modal" data-target="#modal" data-action="{{ route('admin.payments.approve', $payment->id) }}" data-button="btn btn-success" data-title="{{ __('Approve') }}" data-text="{{ __('Are you sure you want to approve :name?', ['name' => $payment->payment_id]) }}">@include('icons.checkmark', ['class' => 'fill-current width-4 height-4 '.(__('lang_dir') == 'rtl' ? 'ml-3' : 'mr-3')]) {{ __('Approve') }}</a>
        @endif

        <div class="dropdown-divider"></div>
        <a class="dropdown-item text-danger d-flex align-items-center" href="#" data-toggle="modal" data-target="#modal" data-action="{{ request()->is('admin/*') ? route('admin.payments.cancel', $payment->id) : route('account.payments.cancel', $payment->id) }}" data-button="btn btn-danger" data-title="{{ __('Cancel') }}" data-text="{{ __('Are you sure you want to cancel :name?', ['name' => $payment->payment_id]) }}">@include('icons.close', ['class' => 'fill-current width-4 height-4 '.(__('lang_dir') == 'rtl' ? 'ml-3' : 'mr-3')]) {{ __('Cancel') }}</a>
    @endif
</div>
