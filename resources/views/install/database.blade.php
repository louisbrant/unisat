@section('site_title', formatTitle([__('Installation'), config('info.software.name')]))

<form action="{{ route('install.database') }}" method="post">
    @csrf

    <div class="card border-0 shadow-sm overflow-hidden">
        <div class="card-header">
            <div class="font-weight-medium py-1">{{ __('Database configuration') }}</div>
        </div>

        <div class="card-body">
            @include('shared.message')

            <div class="row mx-n2">
                <div class="col px-2">
                    <div class="form-group">
                        <label for="i-database-hostname">
                            {{ __('Hostname') }}
                        </label>
                        <input type="text" name="database_hostname" id="i-database-hostname" value="{{ old('database_hostname') ?? '127.0.0.1' }}" class="form-control{{ $errors->has('database_hostname') ? ' is-invalid' : '' }}">
                        @if ($errors->has('database_hostname'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('database_hostname') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="col px-2">
                    <div class="form-group">
                        <label for="i-database-port">
                            {{ __('Port') }}
                        </label>
                        <input type="number" name="database_port" id="i-database-port" value="{{ old('database_port') ?? '3306' }}" class="form-control{{ $errors->has('database_port') ? ' is-invalid' : '' }}">
                        @if ($errors->has('database_port'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('database_port') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="form-group {{ $errors->has('database_name') ? ' has-error ' : '' }}">
                <label for="i-database-name">
                    {{ __('Name') }}
                </label>
                <input type="text" name="database_name" id="i-database-name" class="form-control{{ $errors->has('database_name') ? ' is-invalid' : '' }}" value="{{ old('database_name') }}">
                @if ($errors->has('database_name'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('database_name') }}</strong>
                    </span>
                @endif
            </div>

            <div class="form-group">
                <label for="i-database-username">
                    {{ __('User') }}
                </label>
                <input type="text" name="database_username" id="i-database-username" class="form-control{{ $errors->has('database_username') ? ' is-invalid' : '' }}" value="{{ old('database_username') }}">
                @if ($errors->has('database_username'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('database_username') }}</strong>
                    </span>
                @endif
            </div>

            <div class="form-group">
                <label for="i-database-password">
                    {{ __('Password') }}
                </label>
                <input type="password" name="database_password" id="i-database-password" class="form-control{{ $errors->has('database_password') ? ' is-invalid' : '' }}" value="{{ old('database_password') }}">
                @if ($errors->has('database_password'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('database_password') }}</strong>
                    </span>
                @endif
            </div>
        </div>
    </div>

    <button type="submit" class="btn btn-block btn-primary d-inline-flex align-items-center mt-3 py-2">
        <span class="d-inline-flex align-items-center mx-auto">
            {{ __('Next') }} @include((__('lang_dir') == 'rtl' ? 'icons.chevron-left' : 'icons.chevron-right'), ['class' => 'width-3 height-3 fill-current '.(__('lang_dir') == 'rtl' ? 'mr-2' : 'ml-2')])
        </span>
    </button>
</form>