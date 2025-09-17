<x-app-layout>

<style>
	/* ukuran kotak gambar utama (ganti sesuai kebutuhan) */
	.product-gallery-main .img-frame{
		width: 100%;
		max-width: 550px;      /* opsional */
		aspect-ratio: 1 / 1;   /* kotak 1:1; bisa 4/3, 3/2, 16/9, dll */
		background:#fff;       /* biar rapi saat ada ruang kosong */
		display:grid;          /* center */
		place-items:center;    /* center */
		overflow:hidden;       /* rapikan */
		margin-inline:auto;    /* center container */
	}

	/* pastikan gambar tak melar & selalu masuk frame */
	.product-gallery-main .img-frame > img{
		max-width:100%;
		max-height:100%;
		object-fit:contain;    /* TANPA crop. Kalau mau memenuhi frame dgn crop -> 'cover' */
	}

	/* kotak kecil 84px, seragam */
	.flexslider-thumbnails .flex-control-thumbs li{
		width:auto;            /* biarkan plugin hitung kolom */
		aspect-ratio:1/1;
	}

	.flexslider-thumbnails .flex-control-thumbs img{
		width:100%;
		height:100%;
		object-fit:cover;      /* thumbnail biasanya lebih enak di-crop agar rapi */
		display:block;
		border-radius:6px;
	}
</style>

  <x-slot name="header">
    <h2 class="fw-semibold fs-4 text-dark">Detail Produk</h2>
  </x-slot>

  {{-- Breadcrumbs --}}
  <div class="breadcrumbs">
    <div class="container">
      <div class="row">
        <div class="col-12">
          <div class="bread-inner">
            <ul class="bread-list">
              <li><a href="{{ route('customer.home') }}">Home<i class="ti-arrow-right"></i></a></li>
              <li><a href="#">{{ $product->category->name ?? 'Kategori' }}</a><i class="ti-arrow-right"></i></li>
              <li class="active"><a href="#">{{ $product->name }}</a></li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- Shop Single --}}
  <section class="shop single section">
    <div class="container">
      <div class="row">
        <div class="col-12">

          <div class="row">
            {{-- Gallery kiri --}}
            <div class="col-lg-6 col-12">
              <div class="product-gallery">
                <div class="flexslider-thumbnails product-gallery-main">
                  <ul class="slides">
                    @forelse($gallery as $img)
                      <li data-thumb="{{ $img }}" class="img-frame">
                        <img src="{{ $img }}" alt="{{ $product->name }}" loading="lazy" decoding="async">
                      </li>
                    @empty
                      <li data-thumb="https://placehold.co/550x550">
                        <img src="https://placehold.co/550x550" alt="{{ $product->name }}">
                      </li>
                    @endforelse
                  </ul>
                </div>
              </div>
            </div>

            {{-- Detail kanan --}}
            <div class="col-lg-6 col-12">
              <div class="product-des">
                <div class="short">
                  <h4>{{ $product->name }}</h4>

                  {{-- stok / info singkat (optional rating placeholder) --}}
                  {{-- <div class="rating-main">
                    <ul class="rating">
                      <li><i class="fa fa-star"></i></li>
                      <li><i class="fa fa-star"></i></li>
                      <li><i class="fa fa-star"></i></li>
                      <li><i class="fa fa-star-half-o"></i></li>
                      <li class="dark"><i class="fa fa-star-o"></i></li>
                    </ul>
                    <a href="#" class="total-review">(0) Review</a>
                  </div> --}}

                  <p class="price">
                    <span class="discount">Rp{{ number_format($product->price, 0, ',', '.') }}</span>
                  </p>

				          <p class="cat mt-4">
                    Kategori :
                    @if($product->category)
                      {{-- <a href="{{ route('customer.category', $product->category->slug) }}">{{ $product->category->name }}</a> --}}
                      <span>{{ $product->category->name }} / </span>
                    @endif
                    @if($product->subcategory)
                      {{-- &nbsp;/<a href="{{ route('customer.subcategory', [$product->category->slug, $product->subcategory->slug]) }}">{{ $product->subcategory->name }}</a> --}}
                      <span>{{ $product->subcategory->name }}</span>
                    @endif
                  </p>

                  <p class="description">{{ $product->description }}</p>
                </div>

                {{-- Beli --}}
                <div class="product-buy">
                  <div class="quantity">
                    <h6>Jumlah :</h6>
                    <div class="input-group">
                      <div class="button minus">
                        <button type="button" class="btn btn-primary btn-number" data-type="minus" data-field="qty" disabled>
                          <i class="ti-minus"></i>
                        </button>
                      </div>
                      @php
                        $minQty   = $product->stock > 0 ? 1 : 0;
                        $maxQty   = $product->stock > 0 ? $product->stock : 0;
                        $initQty  = $minQty;
                      @endphp
                      <input type="text" name="qty" class="input-number" data-min="{{ $minQty }}" data-max="{{ $maxQty }}" value="{{ $initQty }}" {{ $product->stock <= 0 ? 'readonly' : '' }}>
                      <div class="button plus">
                        <button type="button" class="btn btn-primary btn-number" data-type="plus" data-field="qty">
                          <i class="ti-plus"></i>
                        </button>
                      </div>
                    </div>
					          <p class="availability mt-4">
                      Ketersediaan :
                      @if($product->stock > 0)
                        <span class="text-success">{{ $product->stock }} stok</span>
                      @else
                        <span class="text-danger">Habis</span>
                      @endif
                    </p>
                    <p class="availability mt-2">
                      Berat : <span>{{ $product->weight ?? '-' }} gram</span>
                    </p>
                  </div>

                  <div class="add-to-cart">
                    {{-- ganti ke route cart mu sendiri --}}
                    <form action="{{ route('cart.add') }}" method="POST" class="d-inline">
						@csrf
						<input type="hidden" name="product_id" value="{{ $product->id }}">
						<input type="hidden" name="qty" value="1" id="hiddenQty">
						<button class="btn mt-4" type="submit" {{ $product->stock <= 0 ? 'disabled' : '' }}>
							<i class="ti-shopping-cart"></i> Tambah ke Keranjang
						</button>
					</form>
                    <a href="#" class="btn min"><i class="ti-heart"></i></a>
                  </div>
                </div>
              </div>
            </div>
          </div>

          {{-- Tabs Deskripsi / Review --}}
          {{-- <div class="row mt-4">
            <div class="col-12">
              <div class="product-info">
                <div class="nav-main">
                  <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#description" role="tab">Deskripsi</a></li>
                    <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#reviews" role="tab">Ulasan</a></li>
                  </ul>
                </div>

                <div class="tab-content" id="myTabContent">
                  <div class="tab-pane fade show active" id="description" role="tabpanel">
                    <div class="tab-single">
                      <div class="single-des">
                        {!! nl2br(e($product->description)) !!}
                      </div>
                    </div>
                  </div>

                  <div class="tab-pane fade" id="reviews" role="tabpanel">
                    <div class="tab-single review-panel">
                      <p>Belum ada ulasan.</p>
                    </div>
                  </div>
                </div>

              </div>
            </div>
          </div> --}}

        </div>
      </div>
    </div>
  </section>

  {{-- Related products --}}
  <hr>
  <div class="product-area most-popular related-product section mt-5">
    <div class="container">
      <div class="row"><div class="col-12"><div class="section-title"><h2>Produk Terkait</h2></div></div></div>
      <div class="row">
        <div class="col-12">
          <div class="owl-carousel popular-slider">
            @forelse($related as $rel)
              <div class="single-product">
                <div class="product-img">
                  <a href="{{ route('customer.product-details', $rel->slug) }}">
                    <img class="default-img" src="{{ $rel->image_url ?? 'https://placehold.co/550x750' }}" alt="{{ $rel->name }}">
                    <img class="hover-img"   src="{{ $rel->image_url ?? 'https://placehold.co/550x750' }}" alt="{{ $rel->name }}">
                    @if($rel->created_at->gt(now()->subDays(7)))
                      <span class="new">Baru</span>
                    @endif
                    @if($rel->stock <= 0)
                      <span class="out-of-stock">Stok Habis</span>
                    @endif
                  </a>
                  <div class="button-head">
                    <div class="product-action">
                      <a title="Lihat" href="{{ route('customer.product-details', $rel->slug) }}"><i class="ti-eye"></i><span>Lihat</span></a>
                      {{-- <a title="Wishlist" href="#"><i class="ti-heart"></i><span>Wishlist</span></a> --}}
                    </div>
                    <div class="product-action-2">
                      <form action="{{ route('cart.add') }}" method="POST">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $rel->id }}">
                        <input type="hidden" name="qty" value="1">
                        <button class="btn" type="submit" {{ $rel->stock <= 0 ? 'disabled' : '' }}>
                          Tambah ke Keranjang
                        </button>
                      </form>
                    </div>
                  </div>
                </div>
                <div class="product-content">
                  <h3><a href="{{ route('customer.product-details', $rel->slug) }}">{{ $rel->name }}</a></h3>
                  <div class="product-price">
                    <span>Rp{{ number_format($rel->price, 0, ',', '.') }}</span>
                  </div>
                </div>
              </div>
            @empty
              <p class="text-muted px-3">Belum ada produk terkait.</p>
            @endforelse
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- Sinkronkan qty input ke hidden form --}}
  {{-- <script>
    document.addEventListener('DOMContentLoaded', () => {
      const qtyInput  = document.querySelector('input[name="qty"]');
      const hiddenQty = document.getElementById('hiddenQty');
      const minusBtn  = document.querySelector('.btn-number[data-type="minus"]');
      const plusBtn   = document.querySelector('.btn-number[data-type="plus"]');

      function clamp() {
        const min = parseInt(qtyInput.dataset.min || '1', 10);
        const max = parseInt(qtyInput.dataset.max || '9999', 10);
        let val = parseInt(qtyInput.value || '1', 10);
        val = isNaN(val) ? min : Math.max(min, Math.min(max, val));
        qtyInput.value = val;
        hiddenQty.value = val;
        minusBtn.disabled = (val <= min);
        plusBtn.disabled  = (val >= max);
      }

      minusBtn?.addEventListener('click', () => { qtyInput.value = (parseInt(qtyInput.value||'1',10)-1); clamp(); });
      plusBtn?.addEventListener('click',  () => { qtyInput.value = (parseInt(qtyInput.value||'1',10)+1); clamp(); });
      qtyInput?.addEventListener('input', clamp);
      clamp();
    });
  </script> --}}
</x-app-layout>