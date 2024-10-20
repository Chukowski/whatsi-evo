@extends('layouts.app', ['title' => __('Setup WhatsApp Notifications')])

@section('content')
<div class="header bg-gradient-primary pb-8 pt-5 pt-md-8">
</div>
<div class="container-fluid mt--7">
    <div class="row">
        <div class="col-xl-12 order-xl-1">
            <div class="card bg-secondary shadow">
                <div class="card-header bg-white border-0">
                    <div class="row align-items-center">
                        <div class="col-8">
                            {{ __('Setup WhatsApp Notifications') }}
                        </div>
                        <!-- Go to api setup -->
                        <div class="col-4 text  text-right">
                            <a href="{{ route('whatsi.api') }}" class="btn btn-sm btn-primary">{{ __('Change API Settings') }}</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form method="post" action="{{ route('whatsi.store_campaign') }}" autocomplete="off">
                        @csrf
                        <div class="row">
                            <div class="col-lg-6">
                                <h6 class="heading-small text-muted mb-4">{{ __('Select Evolution API Campaign') }}</h6>
                                @include('partials.select', ['class'=>"col-12",'name'=>"Evolution API Campaign",'id'=>"evolution_campaign_id",'placeholder'=>"Select campaign",'data'=>$campaigns,'required'=>true, 'value'=>$selected_campaign])
                                <div class="text-center">
                                    <button type="submit" class="btn btn-success mt-4">{{ __('Save') }}</button>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                @isset($error)
                                <div class="alert alert-danger" role="alert">
                                    {{ $error }}
                                </div>
                                @endisset
                                
                                <h6 class="heading-small text-muted mb-4">{{ __('Get your clients notified via WhatsApp Automatically') }}</h6>

                                <h4>How to create your campaign?</h4>
                                <p>1. Go to <a href="{{ config('whatsi.evo_base_url') }}" target="_blank"><mark>Evolution API</mark></a> and login with your account</p>
                                <p>2. Create your API Campaign in the Evolution API dashboard</p>
                                <p>3. Refresh this page, and select your campaign from the dropdown</p>

                                <br />
                                <h4>What data we send to the Evolution API</h4>
                                <script src="{{ config('whatsi.info','https://gist.github.com/dimovdaniel/e0d2b1c146216491200bdba519dbb69f.js') }}"></script>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@include('layouts.footers.auth')
</div>
@endsection
