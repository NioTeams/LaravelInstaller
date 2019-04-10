@extends('vendor.installer.layouts.master')

@section('template_title')
    {{ trans('installer_messages.environment.classic.templateTitle') }}
@endsection

@section('title')
    <i class="fa fa-code fa-fw" aria-hidden="true"></i> {{ trans('installer_messages.environment.classic.title') }}
@endsection

@section('container')

    <form method="post" action="{{ route('LaravelInstaller::environmentSaveClassic') }}">
        {!! csrf_field() !!}
        <textarea class="textarea" name="envConfig">{{ (session()->has('envConfigData') && !empty(session('envConfigData'))) ? session('envConfigData') : old('envConfig', $envConfig) }}</textarea>
        <p class="alert"><strong>Copy the above code and replace the current code of ".env" file. Then refresh this page.</strong></p>
        @if ($checkConnection == false)
        <p class="alert"><strong>Or please set ".env" file permission to `664` and then click "Save .env" button, if saved successfully then click to Install button. </strong></p>
        <div class="buttons buttons--right">
            <button class="button button--light" type="submit">
                <i class="fa fa-floppy-o fa-fw" aria-hidden="true"></i>
                {!! trans('installer_messages.environment.classic.save') !!}
            </button>
        </div>
        @endif
    </form>

    @if( ! isset($environment['errors']))
        <div class="buttons-container">
            {{-- <a class="button float-left" href="{{ route('LaravelInstaller::environmentWizard') }}">
                <i class="fa fa-sliders fa-fw" aria-hidden="true"></i>
                {!! trans('installer_messages.environment.classic.back') !!}
            </a> --}}
            <a class="button float-right" href="{{ route('LaravelInstaller::database') }}">
                <i class="fa fa-check fa-fw" aria-hidden="true"></i>
                {!! trans('installer_messages.environment.classic.install') !!}
                <i class="fa fa-angle-double-right fa-fw" aria-hidden="true"></i>
            </a>
        </div>
    @endif

@endsection