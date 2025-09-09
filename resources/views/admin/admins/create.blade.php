@extends('layouts.admin-layout')

@section('content')
  <section class="section">
    <div class="container-fluid">

      <!-- ========== title-wrapper start ========== -->
        <div class="title-wrapper pt-30">
          <div class="row align-items-center">
            <div class="col-md-6">
              <div class="title">
                <h2>Tambah Admin</h2>
              </div>
            </div>
            <!-- end col -->
            <div class="col-md-6">
              <div class="breadcrumb-wrapper">
                <nav aria-label="breadcrumb">
                  <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                      <a href="#0">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                      Admin
                    </li>
                  </ol>
                </nav>
              </div>
            </div>
            <!-- end col -->
          </div>
          <!-- end row -->
        </div>
      <!-- ========== title-wrapper end ========== -->

       <div class="card">
        <div class="card-body">
            <form action="#" method="POST">
                @csrf
                <div class="form-group">
                    <label for="name" class="text-sm">Nama</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" id="name" placeholder="Masukkan nama pengguna..." value="{{ old('name') }}">
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="form-group mt-3">
                    <label for="email" class="text-sm">Email</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" name="email" id="email" placeholder="Masukkan email..." value="{{ old('email') }}">
                    @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="form-group mt-3">
                    <label for="password" class="text-sm">Password</label>
                    <input type="password" class="form-control @error('name') is-invalid @enderror" name="password" id="password" placeholder="Masukkan password..." value="{{ old('password') }}">
                    @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="form-group mt-3">
                    <label for="role" class="text-sm">Role</label>
                    <select class="form-select text-black @error('name') is-invalid @enderror" name="role" id="role">
                    <option disabled selected>Pilih Role</option>
                    <option value="admin">Admin</option>
                    <option value="kepala sekolah">Kepala Sekolah</option>
                    </select>
                    @error('role') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>
                <div class="form-group mt-3">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <a href="{{ route('admin.data-admin.index') }}" class="btn btn-light mx-2">Kembali</a>
                </div>
            </form>
        </div>
    </div>
    </div>
  </section>
@endsection