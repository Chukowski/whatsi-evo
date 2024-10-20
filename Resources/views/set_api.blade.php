@extends('layouts.app', ['title' => __('Setup API')])

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
                            {{ __('Setup API Key') }}
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form method="post" action="{{ route('whatsi.store_api') }}" autocomplete="off">
                        @csrf
                        <div class="row">
                            <div class="col-lg-6">
                                <h6 class="heading-small text-muted mb-4">{{ __('Evolution API Key') }}</h6>
                                @include('partials.input',['class'=>"col-12", 'ftype'=>'input','name'=>"Evolution API Key",'id'=>"evo_api_key" ,'placeholder'=>"Enter Evolution API Key",'required'=>true, 'value'=>config('whatsi.evo_api_key')])
                                <h6 class="heading-small text-muted mb-4 mt-4">{{ __('Evolution Base URL') }}</h6>
                                @include('partials.input',['class'=>"col-12", 'ftype'=>'input','name'=>"Evolution Base URL",'id'=>"evo_base_url" ,'placeholder'=>"Enter Evolution Base URL",'required'=>true, 'value'=>config('whatsi.evo_base_url')])
                                <div class="text-center">
                                    <button type="submit" class="btn btn-success mt-4">{{ __('Save') }}</button>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <h6 class="heading-small text-muted mb-4">{{ __('WhatsApp Connection') }}</h6>
                                <div id="qrcode-container" class="text-center mb-4">
                                    <!-- QR code will be displayed here -->
                                </div>
                                <div class="text-center">
                                    <button type="button" id="connect-btn" class="btn btn-primary mr-2">{{ __('Connect') }}</button>
                                    <button type="button" id="status-btn" class="btn btn-info mr-2">{{ __('Check Status') }}</button>
                                    <button type="button" id="delete-btn" class="btn btn-danger">{{ __('Delete') }}</button>
                                </div>
                                <div id="status-container" class="mt-4 text-center">
                                    <!-- Status will be displayed here -->
                                </div>
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

@push('js')
<script src="https://cdn.jsdelivr.net/npm/qrcode.js@1.0.0/qrcode.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const connectBtn = document.getElementById('connect-btn');
        const statusBtn = document.getElementById('status-btn');
        const deleteBtn = document.getElementById('delete-btn');
        const qrcodeContainer = document.getElementById('qrcode-container');
        const statusContainer = document.getElementById('status-container');

        connectBtn.addEventListener('click', async () => {
            try {
                const response = await fetch('{{ route("whatsi.connect") }}', { method: 'POST' });
                const data = await response.json();
                if (data.qrcode) {
                    QRCode.toCanvas(qrcodeContainer, data.qrcode, function (error) {
                        if (error) console.error(error);
                        console.log('QR code generated!');
                    });
                }
            } catch (error) {
                console.error('Error connecting:', error);
            }
        });

        statusBtn.addEventListener('click', async () => {
            try {
                const response = await fetch('{{ route("whatsi.status") }}');
                const data = await response.json();
                statusContainer.textContent = `Status: ${data.status}`;
            } catch (error) {
                console.error('Error checking status:', error);
            }
        });

        deleteBtn.addEventListener('click', async () => {
            if (confirm('Are you sure you want to delete the WhatsApp connection?')) {
                try {
                    const response = await fetch('{{ route("whatsi.delete") }}', { method: 'DELETE' });
                    const data = await response.json();
                    if (data.success) {
                        qrcodeContainer.innerHTML = '';
                        statusContainer.textContent = 'Connection deleted';
                    }
                } catch (error) {
                    console.error('Error deleting connection:', error);
                }
            }
        });
    });
</script>
@endpush

@endsection
