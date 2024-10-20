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
                    </div>
                </div>
                <div class="card-body">
                    <form method="post" action="{{ route('whatsi.store_api_key') }}" autocomplete="off">
                        @csrf
                        <div class="row">
                            <div class="col-lg-6">
                                <h6 class="heading-small text-muted mb-4">{{ __('Evolution API Key') }}</h6>
                                <div class="form-group">
                                    <label class="form-control-label" for="evolution_api_key">{{ __('Evolution API Key') }}</label>
                                    <input type="text" name="evolution_api_key" id="evolution_api_key" class="form-control form-control-alternative" placeholder="{{ __('Enter Evolution API Key') }}" value="{{ old('evolution_api_key', config('whatsi.evo_api_key')) }}" required>
                                </div>
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
                                
                                <h6 class="heading-small text-muted mb-4">{{ __('Connect to WhatsApp') }}</h6>
                                <div id="qrcode-container">
                                    <!-- QR code will be displayed here -->
                                </div>
                                <div id="connection-status">
                                    <!-- Connection status will be displayed here -->
                                </div>
                                <button type="button" id="generate-qr" class="btn btn-primary mt-4">{{ __('Generate QR Code') }}</button>

                                <h4 class="mt-5">How to connect WhatsApp?</h4>
                                <ol>
                                    <li>Enter your Evolution API Key above and save it.</li>
                                    <li>Click on "Generate QR Code" button.</li>
                                    <li>Scan the QR code with your WhatsApp app to connect.</li>
                                </ol>
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
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
document.getElementById('generate-qr').addEventListener('click', function() {
    const apiKey = document.getElementById('evolution_api_key').value;
    if (!apiKey) {
        alert('Please enter and save the Evolution API Key first.');
        return;
    }

    axios.post('{{ route("whatsi.generate_qr") }}', { api_key: apiKey })
        .then(function (response) {
            if (response.data.qrcode) {
                document.getElementById('qrcode-container').innerHTML = `<img src="${response.data.qrcode}" alt="WhatsApp QR Code">`;
                checkConnectionStatus();
            } else {
                alert('Failed to generate QR code. Please try again.');
            }
        })
        .catch(function (error) {
            console.error('Error:', error);
            alert('An error occurred while generating the QR code.');
        });
});

function checkConnectionStatus() {
    const statusElement = document.getElementById('connection-status');
    statusElement.textContent = 'Checking connection status...';

    const checkStatus = () => {
        axios.get('{{ route("whatsi.check_connection") }}')
            .then(function (response) {
                if (response.data.status === 'connected') {
                    statusElement.textContent = 'Connected to WhatsApp!';
                    statusElement.style.color = 'green';
                } else if (response.data.status === 'disconnected') {
                    statusElement.textContent = 'Disconnected. Please scan the QR code.';
                    statusElement.style.color = 'red';
                } else {
                    statusElement.textContent = 'Waiting for connection...';
                    setTimeout(checkStatus, 5000); // Check again after 5 seconds
                }
            })
            .catch(function (error) {
                console.error('Error checking status:', error);
                statusElement.textContent = 'Error checking connection status.';
                statusElement.style.color = 'red';
            });
    };

    checkStatus();
}
</script>
@endpush

@endsection
