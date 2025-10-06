<x-app-layout>
    <x-slot name="header"><h2 class="fw-semibold fs-4 text-dark">Bayar Pesanan</h2></x-slot>

    <div class="container py-5">
        <div class="card">
            <div class="card-body">
                <h5 class="mb-3">Kode Pesanan: {{ $order->order_code }}</h5>
                <p class="mb-4">Total pembayaran: <strong>Rp{{ number_format($order->total,0,',','.') }}</strong></p>

                <button id="btn-pay" class="btn btn-dark">Bayar Sekarang</button>
                <a href="{{ route('customer.my-orders') }}" class="btn btn-outline-secondary ms-2">Kembali</a>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://app.sandbox.midtrans.com/snap/snap.js"
                data-client-key="{{ $clientKey }}"></script>
        <script>
            document.getElementById('btn-pay').addEventListener('click', function () {
                window.snap.pay(@json($snapToken), {
                    onSuccess:  function(){ window.location = @json(route('customer.my-orders')); },
                    onPending:  function(){ window.location = @json(route('customer.my-orders')); },
                    onError:    function(){ alert('Terjadi kesalahan pembayaran.'); },
                    onClose:    function(){ /* user menutup popup */ }
                });
            });
        </script>
    @endpush
</x-app-layout>
