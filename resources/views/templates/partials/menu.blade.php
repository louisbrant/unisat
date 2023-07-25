@if($template->user_id == Auth::user()->id || request()->is('admin/*'))
    <a href="#" class="btn d-flex align-items-center btn-sm text-primary" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">@include('icons.more-horiz', ['class' => 'fill-current width-4 height-4'])&#8203;</a>

    <div class="dropdown-menu {{ (__('lang_dir') == 'rtl' ? 'dropdown-menu' : 'dropdown-menu-right') }} border-0 shadow">
        <a class="dropdown-item d-flex align-items-center" href="{{ request()->is('admin/*') ? route('admin.templates.edit', $template->id) : route('templates.edit', $template->id) }}">@include('icons.edit', ['class' => 'text-muted fill-current width-4 height-4 '.(__('lang_dir') == 'rtl' ? 'ml-3' : 'mr-3')]) {{ __('Edit') }}</a>

        @if(request()->is('admin/*'))
            <a class="dropdown-item d-flex align-items-center" href="{{ request()->is('admin/*') ? route('admin.documents', ['template_id' => $template->id]) : route('documents', ['template_id' => $template->id]) }}">@include('icons.document', ['class' => 'text-muted fill-current width-4 height-4 '.(__('lang_dir') == 'rtl' ? 'ml-3' : 'mr-3')]) {{ __('Documents') }}</a>
        @endif

        <div class="dropdown-divider"></div>

        <a class="dropdown-item text-danger d-flex align-items-center" href="#" data-toggle="modal" data-target="#modal" data-action="{{ request()->is('admin/*') ? route('admin.templates.destroy', $template->id) : route('templates.destroy', $template->id) }}" data-button="btn btn-danger" data-title="{{ __('Delete') }}" data-text="{{ __('Deleting this template is permanent, and will remove all the data associated with it.') }}" data-sub-text="{{ __('Are you sure you want to delete :name?', ['name' => $template->name]) }}">@include('icons.delete', ['class' => 'fill-current width-4 height-4 '.(__('lang_dir') == 'rtl' ? 'ml-3' : 'mr-3')]) {{ __('Delete') }}</a>
    </div>
@endif
