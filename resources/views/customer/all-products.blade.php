<x-app-layout>
  <x-slot name="header">
    <h2 class="fw-semibold fs-4 text-dark">Semua Produk</h2>
  </x-slot>

  {{-- Breadcrumbs --}}
  <div class="breadcrumbs">
    <div class="container">
      <div class="row"><div class="col-12">
        <div class="bread-inner">
          <ul class="bread-list">
            <li><a href="{{ route('customer.home') }}">Beranda<i class="ti-arrow-right"></i></a></li>
            <li class="active"><span>Semua Produk</span></li>
          </ul>
        </div>
      </div></div>
    </div>
  </div>

  <section class="product-area shop-sidebar shop section">
    <div class="container">
      <div class="row">
        {{-- Sidebar kiri --}}
        <div class="col-lg-3 col-md-4 col-12">
          <div class="shop-sidebar">

            {{-- Kategori --}}
            <div class="single-widget category">
                <h3 class="title">Kategori</h3>
                <ul class="categor-list">
                    <li>
                    {{-- ke semua produk: kosongkan q --}}
                    <a href="{{ route('customer.all-products', ['sort' => $sort]) }}"
                        class="{{ !$cat ? 'fw-semibold' : '' }}">
                        Semua
                    </a>
                    </li>

                    @foreach($categories as $c)
                    <li>
                        {{-- saat klik kategori: JANGAN kirim q --}}
                        <a href="{{ route('customer.all-products', ['category' => $c->id, 'sort' => $sort]) }}"
                        class="{{ (int)$cat === $c->id ? 'fw-semibold' : '' }}">
                        {{ $c->name }}
                        </a>
                    </li>
                    @endforeach
                </ul>
            </div>

          </div>
        </div>

        {{-- Konten kanan --}}
        <div class="col-lg-9 col-md-8 col-12">
          <div class="row">
            <div class="col-12">
              {{-- Top bar: sortir + info jumlah --}}
              <div class="shop-top d-flex justify-content-between align-items-center">
                <div class="small text-muted">
                  Menampilkan {{ $products->firstItem() }}–{{ $products->lastItem() }}
                  dari {{ $products->total() }} produk
                </div>

                <form method="GET" class="d-flex align-items-center gap-2">
                    <input type="hidden" name="q" value="{{ $q }}">
                    <input type="hidden" name="category" value="{{ $cat }}">
                    <label class="me-1 mb-0">Urutkan :</label>
                    <select name="sort" class="form-select-sm" onchange="this.form.submit()">
                        <option value="latest"    {{ $sort==='latest'?'selected':'' }}>Terbaru</option>
                        <option value="name"      {{ $sort==='name'?'selected':'' }}>Nama</option>
                        <option value="price_asc" {{ $sort==='price_asc'?'selected':'' }}>Harga termurah</option>
                        <option value="price_desc"{{ $sort==='price_desc'?'selected':'' }}>Harga termahal</option>
                    </select>
                </form>
              </div>
            </div>
          </div>

          {{-- Grid produk --}}
          <div class="row">
            @forelse ($products as $p)
              <div class="col-lg-4 col-md-6 col-12">
                <div class="single-product">
                  <div class="product-img">
                    <a href="{{ route('customer.product-details', $p->slug) }}">
                      <img class="default-img" src="{{ $p->thumbnail_url ?? asset('assets/images/placeholder-600x600.png') }}" alt="{{ $p->name }}">
                      <img class="hover-img"   src="{{ $p->thumbnail_url ?? asset('assets/images/placeholder-600x600.png') }}" alt="{{ $p->name }}">
                    </a>
                    <div class="button-head">
                      <div class="product-action">
                        <a title="Lihat" href="{{ route('customer.product-details', $p->slug) }}">
                          <i class="ti-eye"></i><span>Detail</span>
                        </a>
                      </div>
                      <div class="product-action-2">
                        <form method="POST" action="{{ route('cart.add') }}">
                          @csrf
                          <input type="hidden" name="product_id" value="{{ $p->id }}">
                          <input type="hidden" name="qty" value="1">
                          <button type="submit" class="btn btn-dark w-100">Tambah</button>
                        </form>
                      </div>
                    </div>
                  </div>
                  <div class="product-content">
                    <h3 class="mb-1">
                      <a href="{{ route('customer.product-details', $p->slug) }}">{{ $p->name }}</a>
                    </h3>
                    <div class="product-price">
                      <span>Rp{{ number_format((int)$p->price,0,',','.') }}</span>
                    </div>
                  </div>
                </div>
              </div>
            @empty
              <div class="col-12 text-center text-muted py-5">Tidak ada produk.</div>
            @endforelse
          </div>

          {{-- Pagination --}}
          <div class="mt-3">
            {{ $products->links() }}
          </div>
        </div>
      </div>
    </div>
  </section>
</x-app-layout>
