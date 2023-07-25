@if(request()->is('admin/*') || request()->is('dashboard') || request()->is('transcriptions') || request()->is('transcriptions/new') || request()->is('transcriptions/*/edit') || request()->is('templates/*'))
    <a href="#" class="btn d-flex align-items-center btn-sm text-primary" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">@include('icons.more-horiz', ['class' => 'fill-current width-4 height-4'])&#8203;</a>
@endif

<div class="dropdown-menu {{ (__('lang_dir') == 'rtl' ? 'dropdown-menu' : 'dropdown-menu-right') }} border-0 shadow">
    <a class="dropdown-item d-flex align-items-center" href="{{ request()->is('admin/*') || (Auth::user()->role == 1 && $transcription->user_id != Auth::user()->id) ? route('admin.transcriptions.edit', $transcription->id) : route('transcriptions.edit', $transcription->id) }}">@include('icons.edit', ['class' => 'text-muted fill-current width-4 height-4 '.(__('lang_dir') == 'rtl' ? 'ml-3' : 'mr-3')]) {{ __('Edit') }}</a>

    <a class="dropdown-item d-flex align-items-center" href="{{ route('transcriptions.show', $transcription->id) }}">@include('icons.eye', ['class' => 'text-muted fill-current width-4 height-4 '.(__('lang_dir') == 'rtl' ? 'ml-3' : 'mr-3')]) {{ __('View') }}</a>

    @if(Auth::check() && Auth::user()->id == $transcription->user_id && !request()->is('admin/*'))
        <div class="dropdown-divider"></div>
        <a class="dropdown-item {{ ($transcription->favorite ? 'text-warning' : '') }} d-flex align-items-center" href="#" data-toggle="modal" data-target="#modal" data-action="{{ route('transcriptions.edit', $transcription->id) }}" data-button-name="favorite" data-button-value="{{ ($transcription->favorite ? '0' : '1') }}" data-button="btn {{ ($transcription->favorite ? 'btn-danger' : 'btn-warning') }}" data-title="{{ ($transcription->favorite ? __('Delete') : __('Favorite')) }}" data-text="{{ ($transcription->favorite ? __('Are you sure you want to remove :name from favorites?', ['name' => $transcription->name]) : __('Are you sure you want to add :name as favorite?', ['name' => $transcription->name])) }}">@include('icons.' . ($transcription->favorite ? 'star' : 'grade'), ['class' => 'fill-current width-4 height-4 ' . ($transcription->favorite ? 'text-warning' : 'text-muted') . (__('lang_dir') == 'rtl' ? ' ml-3' : ' mr-3')]) {{ __('Favorite') }}</a>
    @endif

    <div class="dropdown-divider"></div>
    <a class="dropdown-item text-danger d-flex align-items-center" href="#" data-toggle="modal" data-target="#modal" data-action="{{ request()->is('admin/*') || (Auth::user()->role == 1 && $transcription->user_id != Auth::user()->id) ? route('admin.transcriptions.destroy', $transcription->id) : route('transcriptions.destroy', $transcription->id) }}" data-button="btn btn-danger" data-title="{{ __('Delete') }}" data-text="{{ __('Deleting this transcription is permanent, and will remove all the data associated with it.') }}" data-sub-text="{{ __('Are you sure you want to delete :name?', ['name' => $transcription->name]) }}">@include('icons.delete', ['class' => 'fill-current width-4 height-4 '.(__('lang_dir') == 'rtl' ? 'ml-3' : 'mr-3')]) {{ __('Delete') }}</a>
</div>
