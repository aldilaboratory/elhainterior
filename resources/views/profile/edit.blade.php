<x-app-layout>
    <x-slot name="header">
        <h2 class="fw-semibold fs-4 text-dark lh-tight mb-0">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-10 px-sm-3 px-lg-4">

                {{-- Update Profile Information --}}
                <div class="card shadow-sm mb-4">
                    <div class="card-body p-4 p-md-5">
                        <div class="mx-auto" style="max-width: 36rem;">
                            @include('profile.partials.update-profile-information-form')
                        </div>
                    </div>
                </div>

                {{-- Update Addresses --}}
                <div class="card shadow-sm mb-4">
                    <div class="card-body p-4 p-md-5">
                        <div class="mx-auto" style="max-width: 36rem;">
                            @include('profile.partials.addresses-form')
                        </div>
                    </div>
                </div>

                {{-- Update Password --}}
                <div class="card shadow-sm mb-4">
                    <div class="card-body p-4 p-md-5">
                        <div class="mx-auto" style="max-width: 36rem;">
                            @include('profile.partials.update-password-form')
                        </div>
                    </div>
                </div>

                {{-- Delete User --}}
                <div class="card shadow-sm mb-4">
                    <div class="card-body p-4 p-md-5">
                        <div class="mx-auto" style="max-width: 36rem;">
                            @include('profile.partials.delete-user-form')
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
