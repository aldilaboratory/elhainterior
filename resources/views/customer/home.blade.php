<x-app-layout>
    <!-- Slider Area -->
	<section class="hero-slider">
		<!-- Single Slider -->
		<div class="single-slider" style="background-image: url('{{ asset('assets/images/hero.jpg') }}');">
			<div class="container">
				<div class="row no-gutters">
					<div class="col-lg-9 offset-lg-3 col-12">
						<div class="text-inner">
							<div class="row">
								<div class="col-lg-7 col-12">
									<div class="hero-text pt-5">
										<h1>Elha Interior</h1>
										<p>Jelajahi ragam produk kami dan<br>temukan inspirasi untuk mengubah<br>ruang Anda menjadi tempat yang<br>penuh gaya dan kenyamanan.</p>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!--/ End Single Slider -->
	</section>
	<!--/ End Slider Area -->

    <!-- Start Small Banner  -->
	<section class="small-banner section">
		<div class="container-fluid">
			<div class="row">
				<!-- Single Banner  -->
				<div class="col-lg-4 col-md-6 col-12">
					<div class="single-banner">
						<img src="{{ asset('assets/images/koleksi-bantal-thailand.jpg') }}" alt="Koleksi Bantal Thailand">
						<div class="content">
							<p>Bantal Thailand</p>
							<h3>Koleksi Bantal <br> Thailand</h3>
							<a href="#">Beli Sekarang</a>
						</div>
					</div>
				</div>
				<!-- /End Single Banner  -->
				<!-- Single Banner  -->
				<div class="col-lg-4 col-md-6 col-12">
					<div class="single-banner">
						<img src="{{ asset('assets/images/koleksi-mangkok-piring.jpg') }}" alt="#">
						<div class="content">
							<p>Mangkok & Piring</p>
							<h3>Koleksi Mangkok <br> & Piring</h3>
							<a href="#">Beli Sekarang</a>
						</div>
					</div>
				</div>
				<!-- /End Single Banner  -->
				<!-- Single Banner  -->
				<div class="col-lg-4 col-12">
					<div class="single-banner tab-height">
						<img src="{{ asset('assets/images/koleksi-tempat-sampah.jpg') }}" alt="#">
						<div class="content">
							<p>Tempat Sampah</p>
							<h3>Koleksi Tempat <br> Sampah</h3>
							<a href="#">Beli Sekarang</a>
						</div>
					</div>
				</div>
				<!-- /End Single Banner  -->
			</div>
		</div>
	</section>
	<!-- End Small Banner -->

    <!-- Start Best Seller -->
	<div class="product-area most-popular section">
        <div class="container">
            <div class="row">
				<div class="col-12">
					<div class="section-title">
						<h2>Paling Sering Dicari</h2>
					</div>
				</div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="owl-carousel popular-slider">
					@forelse($popularProducts as $p)
						<div class="single-product">
						<div class="product-img">
							<a href="{{ route('customer.product-details', $p->slug) }}">
							<img class="default-img"
								src="{{ $p->primary_image_url ?? $p->image_url ?? 'https://placehold.co/550x750' }}"
								alt="{{ $p->name }}">
							@if($p->stock <= 0)
								<span class="out-of-stock">Stok Habis</span>
							@endif
							</a>
							<div class="button-head">
							<div class="product-action">
								<a title="Lihat Detail" href="{{ route('customer.product-details', $p->slug) }}">
								<i class="ti-eye"></i><span>Lihat Detail</span>
								</a>
								<a title="Wishlist" href="#"><i class="ti-heart"></i><span>Tambah Wishlist</span></a>
							</div>
							<div class="product-action-2">
								<a title="Tambah ke Keranjang" href="#"><i class="ti-shopping-cart"></i> Tambah ke Keranjang</a>
							</div>
							</div>
						</div>
						<div class="product-content">
							<h3><a href="{{ route('customer.product-details', $p->slug) }}">{{ $p->name }}</a></h3>
							<div class="product-price">
							<span>Rp{{ number_format($p->price, 0, ',', '.') }}</span>
							</div>
						</div>
						</div>
					@empty
						<p class="px-3">Belum ada produk.</p>
					@endforelse
					</div>
                </div>
            </div>
        </div>
    </div>
	<!-- End Best Seller -->

    <!-- Start Product Area -->
    <div class="product-area section pt-0">
            <div class="container">
				<div class="row">
					<div class="col-12">
						<div class="section-title">
							<h2>Mungkin Kamu Suka</h2>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-12">
						<div class="product-info">
							<div class="nav-main">
								<ul class="nav nav-tabs" id="categoryTabs" role="tablist">
								@foreach($categories as $idx => $cat)
									@php $tabId = 'cat-'.$cat->id; @endphp
									<li class="nav-item">
									<a class="nav-link {{ $idx===0 ? 'active' : '' }}"
										data-toggle="tab" href="#{{ $tabId }}" role="tab">
										{{ $cat->name }}
									</a>
									</li>
								@endforeach
								</ul>
							</div>

							<div class="tab-content" id="categoryTabContent">
								@foreach($categories as $idx => $cat)
								@php $tabId = 'cat-'.$cat->id; @endphp
								<div class="tab-pane fade {{ $idx===0 ? 'show active' : '' }}" id="{{ $tabId }}" role="tabpanel">
									<div class="tab-single">
									<div class="row">
										@forelse($cat->products as $p)
										<div class="col-xl-3 col-lg-4 col-md-4 col-12">
											<div class="single-product">
											<div class="product-img">
												<a href="{{ route('customer.product-details', $p->slug) }}">
												<img class="default-img"
													src="{{ $p->primary_image_url ?? $p->image_url ?? 'https://placehold.co/550x750' }}"
													alt="{{ $p->name }}">
												{{-- contoh: hover-img jika kamu punya image kedua --}}
												{{-- <img class="hover-img" src="{{ ... }}" alt="{{ $p->name }}"> --}}
												@if($p->created_at->gt(now()->subDays(7)))
													<span class="new">Baru</span>
												@endif
												@if($p->stock <= 0)
													<span class="out-of-stock">Stok Habis</span>
												@endif
												</a>
												<div class="button-head">
												<div class="product-action">
													<a title="Lihat Detail" href="{{ route('customer.product-details', $p->slug) }}">
													<i class="ti-eye"></i><span>Lihat Detail</span>
													</a>
													<a title="Wishlist" href="#"><i class="ti-heart"></i><span>Tambah Wishlist</span></a>
												</div>
												<div class="product-action-2">
													<a title="Tambah ke Keranjang" href="#">Tambah ke Keranjang</a>
												</div>
												</div>
											</div>
											<div class="product-content">
												<h3><a href="{{ route('customer.product-details', $p->slug) }}">{{ $p->name }}</a></h3>
												<div class="product-price">
												<span>Rp{{ number_format($p->price, 0, ',', '.') }}</span>
												</div>
											</div>
											</div>
										</div>
										@empty
										<div class="col-12"><p class="text-muted">Belum ada produk di kategori ini.</p></div>
										@endforelse
									</div>
									</div>
								</div>
								@endforeach
							</div>
						</div>
					</div>
				</div>
            </div>
    </div>
	<!-- End Product Area -->
</x-app-layout>
