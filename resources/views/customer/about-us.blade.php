<x-app-layout>
    <x-slot name="header">
        <h2 class="fw-semibold fs-4 text-dark">
            {{ __('About Us') }}
        </h2>
    </x-slot>

    <!-- Breadcrumbs -->
	<div class="breadcrumbs">
		<div class="container">
			<div class="row">
				<div class="col-12">
					<div class="bread-inner">
						<ul class="bread-list">
							<li><a href="index1.html">Home<i class="ti-arrow-right"></i></a></li>
							<li class="active"><a href="blog-single.html">About Us</a></li>
						</ul>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- End Breadcrumbs -->
	
	<!-- About Us -->
	<section class="about-us section">
			<div class="container">
				<div class="row">
					<div class="col-lg-6 col-12">
						<div class="about-content">
							<h3>Welcome To <span>Elha Interior</span></h3>
							<p class="text-justify">
								Kami sangat menyukai ide pemakaian material alami untuk menciptakan setiap produk. Kami merasa sangat diberkati dengan kekayaan yang ada di negara kami. Ada berbagai macam serat alami, jenis tanaman, akar, rotan, kayu dan masih banyak lagi. Begitu banyak kreatifitas yang bisa dimunculkan. Selama kurang lebih 20 tahun ini kami membuat berbagai produk home decor baik untuk villa, resort dan hotel. Kami juga mengirimkan produk ke berbagai negara tujuan. Apabila memerlukan custom made kami siap membantu anda. Layanan pengiriman ke seluruh negara didunia juga bisa kami bantu.
							</p>
							<div class="button">
								<a href="{{ route('customer.contact-us') }}" class="btn primary">Contact Us</a>
							</div>
						</div>
					</div>
					<div class="col-lg-6 col-12">
						<div class="about-img overlay">
							<div class="button">
								<a href="https://www.youtube.com/watch?v=nh2aYrGMrIE" class="video video-popup mfp-iframe"><i class="fa fa-play"></i></a>
							</div>
							<img src="https://via.placeholder.com/775x550" alt="#">
						</div>
					</div>
				</div>
			</div>
	</section>
	<!-- End About Us -->
</x-app-layout>
