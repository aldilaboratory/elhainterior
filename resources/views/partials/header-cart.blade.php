@php
    $fmt = fn($n) => 'Rp'.number_format($n, 0, ',', '.');
@endphp

<div class="sinlge-bar shopping">
  <a href="{{ route('cart.index') }}" class="single-icon">
    <i class="ti-bag"></i>
    <span class="total-count">{{ $miniCart->count }}</span>
  </a>

  <div class="shopping-item">
    <div class="dropdown-cart-header">
      <span>{{ $miniCart->count }} Item</span>
      <a href="{{ route('cart.index') }}">Lihat Keranjang</a>
    </div>

    <ul class="shopping-list">
      @forelse($miniCart->lines as $line)
        <li>
          <a class="cart-img" href="{{ route('customer.product-details', $line->slug) }}">
            <img src="{{ $line->image_url }}" alt="{{ $line->name }}">
          </a>

          <h4>
            <a href="{{ route('customer.product-details', $line->slug) }}">{{ $line->name }}</a>
          </h4>

          <p class="quantity">
            {{ $line->qty }}x - <span class="amount">{{ $fmt($line->unit_price) }}</span>
          </p>

          @if($line->item_id)
            <form action="{{ route('cart.item.remove', $line->item_id) }}" method="POST" class=" mt-0">
              @csrf @method('DELETE')
              <button title="Hapus" class="text-danger p-2">
                <i class="fa fa-remove"></i>
              </button>
            </form>
          @endif
        </li>
      @empty
        <li class="text-center text-muted py-3">Keranjang kosong</li>
      @endforelse
    </ul>

    <div class="bottom">
      <div class="total">
        <span>Total</span>
        <span class="total-amount">{{ $fmt($miniCart->total) }}</span>
      </div>
      <a href="{{ route('customer.checkout') }}" class="btn animate {{ $miniCart->count ? '' : 'disabled' }}">
        Checkout
      </a>
    </div>
  </div>
</div>
