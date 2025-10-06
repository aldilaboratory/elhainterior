<!-- Header -->
<header class="header shop">
		<!-- Topbar -->
		<div class="topbar">
			<div class="container">
				<div class="row">
					<div class="col-lg-4 col-md-12 col-12">
						<!-- Top Left -->
						<div class="top-left">
							<ul class="list-main">
								<li><i class="ti-headphone-alt"></i> +62 893 664 678</li>
								<li><i class="ti-email"></i> support@elhainterior.com</li>
							</ul>
						</div>
						<!--/ End Top Left -->
					</div>
					<div class="col-lg-8 col-md-12 col-12">
						<!-- Top Right -->
						<div class="right-content">
							<ul class="list-main">
								<li><a href="https://maps.app.goo.gl/T6VH6xM3qneNVyR27"><i class="ti-location-pin"></i> Lokasi toko</a></li>
								<li><i class="ti-user"></i> <a href="{{ route('profile.edit') }}"> Akun saya</a></li>
                                @if (Auth::check())
                                    {{-- User sudah login, maka tampil tombol logout --}}
                                    <li>
                                        <form method="POST" action="{{ route('logout') }}" class="text-danger d-inline">
                                        @csrf
                                            <button type="submit" class="dropdown-item">
                                            <i class="ti-power-off text-danger"></i>{{ __('Keluar') }}
                                            </button>
                                        </form>
                                    </li>
                                @else
                                    {{-- User belum login, maka tampil tombol login --}}
								    <li><i class="ti-power-off"></i><a href="{{ route('login') }}"> Login</a></li>
                                @endif
							</ul>
						</div>
						<!-- End Top Right -->
					</div>
				</div>
			</div>
		</div>
		<!-- End Topbar -->
		<div class="middle-inner">
			<div class="container">
				<div class="row">
					<div class="col-lg-2 col-md-2 col-12">
						<!-- Logo -->
						<div class="logo">
							<a href="{{ route('customer.home') }}"><img src="{{ asset('assets/images/logo.png') }}" alt="logo"></a>
						</div>
						<!--/ End Logo -->
						<!-- Search Form -->
						<div class="search-top">
							<div class="top-search"><a href="#0"><i class="ti-search"></i></a></div>
							<!-- Search Form -->
							<div class="search-top">
								<form class="search-form" method="GET" action="{{ route('customer.all-products') }}">
									<input type="text" name="q" value="{{ request('q') }}" placeholder="Cari produk...">
									<button type="submit"><i class="ti-search"></i></button>
								</form>
							</div>
							<!--/ End Search Form -->
						</div>
						<!--/ End Search Form -->
						<div class="mobile-nav"></div>
					</div>
					<div class="col-lg-8 col-md-7 col-12">
						<div class="search-bar-top">
							<div class="search-bar">
								<form method="GET" action="{{ route('customer.all-products') }}">
									<input name="q" placeholder="Cari produk di sini..." type="search" value="{{ request('q') }}">
									<button class="btnn" type="submit"><i class="ti-search"></i></button>
								</form>
							</div>
						</div>
					</div>
					<div class="col-lg-2 col-md-3 col-12">
						<div class="right-bar">
							<!-- Search Form -->
							<div class="sinlge-bar">
								<a href="#" class="single-icon"><i class="fa fa-heart-o" aria-hidden="true"></i></a>
							</div>
							@include('partials.header-cart')
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- Header Inner -->
		<div class="header-inner">
			<div class="container">
				<div class="cat-nav-head">
					<div class="row">
						<div class="col-lg-3">
							@if (Route::is('customer.home'))
							<div class="all-category">
								<h3 class="cat-heading"><i class="fa fa-bars" aria-hidden="true"></i>KATEGORI</h3>
								<ul class="main-category">
								@foreach($headerCategories as $cat)
									<li>
									<a href="{{ route('customer.category', $cat->slug) }}">
										{{ $cat->name }}
										@if($cat->subcategories->isNotEmpty())
										<i class="fa fa-angle-right" aria-hidden="true"></i>
										@endif
									</a>

									@if($cat->subcategories->isNotEmpty())
										<ul class="sub-category">
										@foreach($cat->subcategories as $sub)
											<li>
											<a href="{{ route('customer.subcategory', [$cat->slug, $sub->slug]) }}">
												{{ $sub->name }}
											</a>
											</li>
										@endforeach
										</ul>
									@endif
									</li>
								@endforeach
								</ul>
							</div>
							@endif
						</div>
						<div class="col-lg-9 col-12">
							<div class="menu-area">
								<!-- Main Menu -->
								<nav class="navbar navbar-expand-lg">
									<div class="navbar-collapse">	
										<div class="nav-inner">	
											<ul class="nav main-menu menu navbar-nav">
												<li><a href="{{ route('customer.home') }}">Beranda</a></li>
												<li><a href="{{ route('customer.all-products') }}">Semua Produk</a></li>
												<li><a href="{{ route('customer.my-orders') }}">Pesanan Saya</a></li>
											</ul>
										</div>
									</div>
								</nav>
								<!--/ End Main Menu -->	
							</div>
						</div>
					</div>
				</div>
			</div>
    </div>
    <!--/ End Header Inner -->
</header>
<!--/ End Header -->

{{-- <nav class="navbar navbar-expand-sm navbar-light bg-white border-bottom">
    <div class="container-fluid" style="max-width: 80rem;">
        <!-- Logo -->
        <a class="navbar-brand d-flex align-items-center" href="{{ route('dashboard') }}">
            <x-application-logo class="text-dark" style="height: 2.25rem; width: auto;" />
        </a>

        <!-- Navigation Links (Desktop) -->
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" 
                       href="{{ route('dashboard') }}">
                        {{ __('Dashboard') }}
                    </a>
                </li>
            </ul>

            <!-- Settings Dropdown (Desktop) -->
            <div class="navbar-nav">
                <div class="nav-item dropdown">
                    <button class="btn btn-link nav-link dropdown-toggle text-secondary border-0 bg-transparent" 
                            type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        {{ Auth::user()->name }}
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                {{ __('Profile') }}
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item">
                                    {{ __('Log Out') }}
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Mobile Toggle Button -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" 
                data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false">
            <span class="navbar-toggler-icon"></span>
        </button>
    </div>

    <!-- Mobile Navigation -->
    <div class="collapse navbar-collapse d-sm-none" id="navbarNav">
        <!-- Mobile Navigation Links -->
        <div class="navbar-nav p-3 border-top">
            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" 
               href="{{ route('dashboard') }}">
                {{ __('Dashboard') }}
            </a>
        </div>

        <!-- Mobile User Info -->
        <div class="border-top pt-3 pb-1">
            <div class="px-3 mb-3">
                <div class="fw-medium text-dark">{{ Auth::user()->name }}</div>
                <div class="text-muted small">{{ Auth::user()->email }}</div>
            </div>

            <div class="navbar-nav px-3">
                <a class="nav-link" href="{{ route('profile.edit') }}">
                    {{ __('Profile') }}
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="nav-link btn btn-link text-start p-0 border-0">
                        {{ __('Log Out') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav> --}}