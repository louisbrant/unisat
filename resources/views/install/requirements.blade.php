@section('site_title', formatTitle([__('Installation'), config('info.software.name')]))

<div class="card border-0 shadow-sm overflow-hidden">
    <div class="card-header">
        <div class="font-weight-medium py-1">{{ __('Requirements') }}</div>
    </div>

    <div class="card-body">
        @foreach($results['extensions'] as $type => $extension)
            <div class="list-group list-group-flush {{ $loop->index == 0 ? 'mb-n3 mt-n3' : 'mt-3 mb-n3 pt-3' }}">
                <div class="list-group-item px-0">
                    <div class="row align-items-center">
                        <div class="col">
                            <span class="font-weight-medium">{{ mb_strtoupper($type) }}</span>
                            @if($type == 'php')
                                {{ config('install.php_version') }}+
                            @endif
                        </div>

                        <div class="col-auto d-flex align-items-center">
                            @if($type == 'php')
                                @if(version_compare(PHP_VERSION, config('installer.php_version')) >= 0)
                                    @include('icons.checkmark', ['class' => 'text-success width-4 height-4 fill-current'])
                                @else
                                    @include('icons.close', ['class' => 'text-danger width-4 height-4 fill-current'])
                                @endif
                            @endif
                        </div>
                    </div>
                </div>

                @foreach($extension as $name => $enabled)
                    <div class="list-group-item px-0 text-muted">
                        <div class="row align-items-center">
                            <div class="col">
                                {{ $name }}
                            </div>
                            <div class="col-auto d-flex align-items-center">
                                @if($enabled)
                                    @include('icons.checkmark', ['class' => 'text-success width-4 height-4 fill-current'])
                                @else
                                    @include('icons.close', ['class' => 'text-danger width-4 height-4 fill-current'])
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endforeach
    </div>
</div>

@if(isset($results['errors']) == false)
    <a href="{{ route('install.permissions') }}" class="btn btn-block btn-primary d-inline-flex align-items-center mt-3 py-2">
        <span class="d-inline-flex align-items-center mx-auto">
            {{ __('Next') }} @include((__('lang_dir') == 'rtl' ? 'icons.chevron-left' : 'icons.chevron-right'), ['class' => 'width-3 height-3 fill-current '.(__('lang_dir') == 'rtl' ? 'mr-2' : 'ml-2')])
        </span>
    </a>
@endif