<x-app-layout>
  <x-slot name="header">
    <h2 class="fw-semibold fs-4 text-dark mb-0">Pesanan Saya</h2>
  </x-slot>

  <section class="section">
    <div class="container">
      <div class="card border-0">
        <div class="card-body">

          @if($orders->isEmpty())
            <div class="alert alert-light border mb-0">
              Belum ada pesanan. <a href="{{ route('customer.home') }}" class="alert-link">Belanja sekarang →</a>
            </div>
          @else
            <div class="table-responsive">
              <table class="table align-middle">
                <thead class="small text-muted">
                  <tr>
                    <th>Kode</th>
                    <th>Tanggal</th>
                    <th class="text-center">Item</th>
                    <th class="text-end">Total</th>
                    <th>Status Pembayaran</th>
                    <th>Status Pesanan</th>
                    <th class="text-end">Aksi</th>
                  </tr>
                </thead>
                <tbody>
                @foreach($orders as $o)
                  @php
                    $payBadge = [
                      'pending'  => 'badge-warning',
                      'paid'     => 'badge-success',
                      'failed'   => 'badge-danger',
                      'refunded' => 'badge-info',
                    ][$o->payment_status] ?? 'badge-secondary';

                    $ordBadge = [
                      'unconfirmed' => 'badge-secondary',
                      'packing'     => 'badge-info',
                      'shipped'     => 'badge-primary',
                      'completed'    => 'badge-success',
                      'cancelled'   => 'badge-danger',
                    ][$o->order_status] ?? 'badge-secondary';
                  @endphp

                  <tr>
                    <td class="fw-semibold">
                      <a href="{{ route('customer.my-orders.show', $o) }}">{{ $o->order_code }}</a>
                    </td>
                    <td>{{ $o->created_at->format('d M Y H:i') }}</td>
                    <td class="text-center">{{ $o->items_count }}</td>
                    <td class="text-end">Rp{{ number_format($o->total,0,',','.') }}</td>
                    <td><span class="badge {{ $payBadge }} text-uppercase">{{ $o->payment_status }}</span></td>
                    <td><span class="badge {{ $ordBadge }} text-uppercase">{{ $o->order_status }}</span></td>
                    <td class="text-end">
                        <a href="{{ route('customer.my-orders.show', $o) }}" class="btn btn-sm btn-outline-secondary">Detail</a>
                        {{-- Download Invoice --}}
                        <a href="{{ route('customer.my-orders.invoice', $o) }}"
                          class="btn btn-sm btn-outline-primary">Invoice</a>

                        @if ($o->payment_status === 'pending')
                            <a href="{{ route('customer.pay.snap', $o) }}" class="btn btn-sm btn-dark">Bayar</a>
                        @endif
                    </td>
                  </tr>
                @endforeach
                </tbody>
              </table>
            </div>

            <div class="mt-3">
              {{ $orders->withQueryString()->links() }}
            </div>
          @endif

        </div>
      </div>
    </div>
  </section>
</x-app-layout>
