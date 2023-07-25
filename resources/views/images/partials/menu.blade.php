@if(request()->is('admin/*') || request()->is('dashboard') || request()->is('images') || request()->is('images/new') || request()->is('images/*/edit'))
    <a href="#" class="btn d-flex align-items-center btn-sm text-primary" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">@include('icons.more-horiz', ['class' => 'fill-current width-4 height-4'])&#8203;</a>
@endif

<div class="dropdown-menu {{ (__('lang_dir') == 'rtl' ? 'dropdown-menu' : 'dropdown-menu-right') }} border-0 shadow">
    <a class="dropdown-item d-flex align-items-center" href="{{ request()->is('admin/*') ? route('admin.images.edit', $image->id) : route('images.edit', $image->id) }}">@include('icons.edit', ['class' => 'text-muted fill-current width-4 height-4 '.(__('lang_dir') == 'rtl' ? 'ml-3' : 'mr-3')]) {{ __('Edit') }}</a>

    <a class="dropdown-item d-flex align-items-center" href="{{ route('images.show', $image->id) }}">@include('icons.eye', ['class' => 'text-muted fill-current width-4 height-4 '.(__('lang_dir') == 'rtl' ? 'ml-3' : 'mr-3')]) {{ __('View') }}</a>

    <a class="dropdown-item d-flex align-items-center" href="{{ $image->url }}" rel="download">@include('icons.export', ['class' => 'text-muted fill-current width-4 height-4 '.(__('lang_dir') == 'rtl' ? 'ml-3' : 'mr-3')]) {{ __('Download') }}</a>

    @if(Auth::check() && Auth::user()->id == $image->user_id && !request()->is('admin/*'))
        <div class="dropdown-divider"></div>
        <a class="dropdown-item {{ ($image->favorite ? 'text-warning' : '') }} d-flex align-items-center" href="#" data-toggle="modal" data-target="#modal" data-action="{{ route('images.edit', $image->id) }}" data-button-name="favorite" data-button-value="{{ ($image->favorite ? '0' : '1') }}" data-button="btn {{ ($image->favorite ? 'btn-danger' : 'btn-warning') }}" data-title="{{ ($image->favorite ? __('Delete') : __('Favorite')) }}" data-text="{{ ($image->favorite ? __('Are you sure you want to remove :name from favorites?', ['name' => $image->name]) : __('Are you sure you want to add :name as favorite?', ['name' => $image->name])) }}">@include('icons.' . ($image->favorite ? 'star' : 'grade'), ['class' => 'fill-current width-4 height-4 ' . ($image->favorite ? 'text-warning' : 'text-muted') . (__('lang_dir') == 'rtl' ? ' ml-3' : ' mr-3')]) {{ __('Favorite') }}</a>
    @endif

    <div class="dropdown-divider"></div>
    <a class="dropdown-item text-danger d-flex align-items-center" href="#" data-toggle="modal" data-target="#modal" data-action="{{ request()->is('admin/*') || (Auth::user()->role == 1 && $image->user_id != Auth::user()->id) ? route('admin.images.destroy', $image->id) : route('images.destroy', $image->id) }}" data-button="btn btn-danger" data-title="{{ __('Delete') }}" data-text="{{ __('Deleting this image is permanent, and will remove all the data associated with it.') }}" data-sub-text="{{ __('Are you sure you want to delete :name?', ['name' => $image->name]) }}">@include('icons.delete', ['class' => 'fill-current width-4 height-4 '.(__('lang_dir') == 'rtl' ? 'ml-3' : 'mr-3')]) {{ __('Delete') }}</a>
</div>
