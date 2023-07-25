@section('site_title', formatTitle([__('New'), __('Tax rate'), config('settings.title')]))

@include('shared.breadcrumbs', ['breadcrumbs' => [
    ['url' => route('admin.dashboard'), 'title' => __('Admin')],
    ['url' => route('admin.tax_rates'), 'title' => __('Tax rates')],
    ['title' => __('New')],
]])

<h1 class="h2 mb-3 d-inline-block">{{ __('New') }}</h1>

<div class="card border-0 shadow-sm">
    <div class="card-header"><div class="font-weight-medium py-1">{{ __('Tax rate') }}</div></div>
    <div class="card-body">
        @include('shared.message')

        <form action="{{ route('admin.tax_rates.new') }}" method="post" enctype="multipart/form-data">
            @csrf

            <div class="form-group">
                <label for="i-name">{{ __('Name') }}</label>
                <input type="text" name="name" id="i-name" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" value="{{ old('name') }}">
                @if ($errors->has('name'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('name') }}</strong>
                    </span>
                @endif
            </div>

            <div class="form-group">
                <label for="i-regions">{{ __('Regions') }}</label>
                <select name="regions[]" id="i-regions" class="custom-select{{ $errors->has('regions') ? ' is-invalid' : '' }}" multiple>
                    @foreach(config('countries') as $key => $value)
                        <option value="{{ $key }}" @if(old('regions') !== null && in_array($key, old('regions'))) selected @endif>{{ __($value) }}</option>
                    @endforeach
                </select>
                @if ($errors->has('regions'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('regions') }}</strong>
                    </span>
                @endif
                <small class="form-text text-muted">{{ __('Leave empty to apply the tax rate on all regions.') }}</small>
            </div>
            
            <div class="row mx-n2">
                <div class="col-12 col-md-6 px-2">
                    <div class="form-group">
                        <label for="i-percentage">{{ __('Percentage') }}</label>
                        <div class="input-group">
                            <input type="text" name="percentage" id="i-percentage" class="form-control{{ $errors->has('percentage') ? ' is-invalid' : '' }}" value="{{ old('percentage') }}">
                            <div class="input-group-append">
                                <span class="input-group-text">%</span>
                            </div>
                        </div>
                        @if ($errors->has('percentage'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('percentage') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
                
                <div class="col-12 col-md-6 px-2">
                    <label for="i-type">{{ __('Type') }}</label>
                    <select name="type" id="i-type" class="custom-select{{ $errors->has('type') ? ' is-invalid' : '' }}">
                        @foreach([0 => __('Inclusive'), 1 => __('Exclusive')] as $key => $value)
                            <option value="{{ $key }}" @if (old('type') == $key) selected @endif>{{ $value }}</option>
                        @endforeach
                    </select>
                    @if ($errors->has('type'))
                        <span class="invalid-feedback d-block" role="alert">
                            <strong>{{ $errors->first('type') }}</strong>
                        </span>
                    @endif
                </div>
            </div>

            <button type="submit" name="submit" class="btn btn-primary">{{ __('Save') }}</button>
        </form>
    </div>
</div>