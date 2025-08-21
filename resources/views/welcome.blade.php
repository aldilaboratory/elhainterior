<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    </head>
    <body class="bg-light text-dark d-flex p-4 p-lg-5 align-items-center justify-content-lg-center min-vh-100 flex-column" 
          style="font-family: 'Instrument Sans', sans-serif; background-color: #FDFDFC;">
        
        <!-- Header Navigation -->
        <header class="w-100 small mb-4" style="max-width: 56rem;">
            @if (Route::has('login'))
                <nav class="d-flex align-items-center justify-content-end">
                    @auth
                        <a href="{{ url('/dashboard') }}" 
                           class="btn btn-outline-dark btn-sm border rounded-1 me-3"
                           style="font-size: 0.875rem; padding: 0.375rem 1.25rem;">
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" 
                           class="btn btn-link text-dark btn-sm me-3 border-0"
                           style="font-size: 0.875rem; padding: 0.375rem 1.25rem; text-decoration: none;">
                            Log in
                        </a>

                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" 
                               class="btn btn-outline-dark btn-sm border rounded-1"
                               style="font-size: 0.875rem; padding: 0.375rem 1.25rem;">
                                Register
                            </a>
                        @endif
                    @endauth
                </nav>
            @endif
        </header>

        <!-- Main Content -->
        <div class="d-flex align-items-center justify-content-center w-100 opacity-100 flex-grow-1">
            <main class="d-flex flex-column-reverse flex-lg-row" style="max-width: 335px; width: 100%;">
                
                <!-- Content Section -->
                <div class="flex-fill p-4 pb-5 p-lg-5 bg-white text-dark shadow-sm rounded-bottom rounded-lg-start rounded-lg-bottom-0"
                     style="font-size: 0.8125rem; line-height: 1.5; border: 1px solid rgba(26,26,0,0.16);">
                    
                    <h1 class="mb-2 fw-medium">Let's get started</h1>
                    <p class="mb-3 text-muted">Laravel has an incredibly rich ecosystem. <br>We suggest starting with the following.</p>
                    
                    <!-- Steps List -->
                    <ul class="list-unstyled mb-4 mb-lg-4">
                        <li class="d-flex align-items-center py-2 position-relative">
                            <!-- Timeline Line -->
                            <div class="position-absolute border-start border-secondary opacity-25" 
                                 style="left: 0.4rem; top: 50%; bottom: 0; height: 50%;"></div>
                            
                            <!-- Circle Icon -->
                            <span class="position-relative py-1 bg-white me-3">
                                <span class="d-flex align-items-center justify-content-center rounded-circle bg-light shadow-sm border border-secondary-subtle"
                                      style="width: 0.875rem; height: 0.875rem;">
                                    <span class="rounded-circle bg-secondary opacity-50" style="width: 0.375rem; height: 0.375rem;"></span>
                                </span>
                            </span>
                            
                            <!-- Text Content -->
                            <span>
                                Read the
                                <a href="https://laravel.com/docs" target="_blank" 
                                   class="fw-medium text-decoration-underline text-danger ms-1">
                                    <span>Documentation</span>
                                    <svg width="10" height="11" viewBox="0 0 10 11" fill="none" 
                                         xmlns="http://www.w3.org/2000/svg" class="ms-1" style="width: 0.625rem; height: 0.625rem;">
                                        <path d="M7.70833 6.95834V2.79167H3.54167M2.5 8L7.5 3.00001" 
                                              stroke="currentColor" stroke-linecap="square" />
                                    </svg>
                                </a>
                            </span>
                        </li>
                        
                        <li class="d-flex align-items-center py-2 position-relative">
                            <!-- Timeline Line -->
                            <div class="position-absolute border-start border-secondary opacity-25" 
                                 style="left: 0.4rem; bottom: 50%; top: 0; height: 50%;"></div>
                            
                            <!-- Circle Icon -->
                            <span class="position-relative py-1 bg-white me-3">
                                <span class="d-flex align-items-center justify-content-center rounded-circle bg-light shadow-sm border border-secondary-subtle"
                                      style="width: 0.875rem; height: 0.875rem;">
                                    <span class="rounded-circle bg-secondary opacity-50" style="width: 0.375rem; height: 0.375rem;"></span>
                                </span>
                            </span>
                            
                            <!-- Text Content -->
                            <span>
                                Watch video tutorials at
                                <a href="https://laracasts.com" target="_blank" 
                                   class="fw-medium text-decoration-underline text-danger ms-1">
                                    <span>Laracasts</span>
                                    <svg width="10" height="11" viewBox="0 0 10 11" fill="none" 
                                         xmlns="http://www.w3.org/2000/svg" class="ms-1" style="width: 0.625rem; height: 0.625rem;">
                                        <path d="M7.70833 6.95834V2.79167H3.54167M2.5 8L7.5 3.00001" 
                                              stroke="currentColor" stroke-linecap="square" />
                                    </svg>
                                </a>
                            </span>
                        </li>
                    </ul>
                    
                    <!-- Deploy Button -->
                    <div class="d-flex">
                        <a href="https://cloud.laravel.com" target="_blank" 
                           class="btn btn-dark btn-sm px-4 py-2 rounded-1"
                           style="font-size: 0.875rem;">
                            Deploy now
                        </a>
                    </div>
                </div>

                <!-- Laravel Logo Section -->
                <div class="bg-danger bg-opacity-10 position-relative rounded-top rounded-lg-end rounded-lg-top-0 overflow-hidden flex-shrink-0"
                     style="aspect-ratio: 335/376; width: 100%; background-color: #fff2f2;">
                    
                    <!-- Laravel Logo SVG -->
                    <svg class="w-100 text-danger opacity-100" viewBox="0 0 438 104" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M17.2036 -3H0V102.197H49.5189V86.7187H17.2036V-3Z" fill="currentColor" />
                        <path d="M110.256 41.6337C108.061 38.1275 104.945 35.3731 100.905 33.3681C96.8667 31.3647 92.8016 30.3618 88.7131 30.3618C83.4247 30.3618 78.5885 31.3389 74.201 33.2923C69.8111 35.2456 66.0474 37.928 62.9059 41.3333C59.7643 44.7401 57.3198 48.6726 55.5754 53.1293C53.8287 57.589 52.9572 62.274 52.9572 67.1813C52.9572 72.1925 53.8287 76.8995 55.5754 81.3069C57.3191 85.7173 59.7636 89.6241 62.9059 93.0293C66.0474 96.4361 69.8119 99.1155 74.201 101.069C78.5885 103.022 83.4247 103.999 88.7131 103.999C92.8016 103.999 96.8667 102.997 100.905 100.994C104.945 98.9911 108.061 96.2359 110.256 92.7282V102.195H126.563V32.1642H110.256V41.6337ZM108.76 75.7472C107.762 78.4531 106.366 80.8078 104.572 82.8112C102.776 84.8161 100.606 86.4183 98.0637 87.6206C95.5202 88.823 92.7004 89.4238 89.6103 89.4238C86.5178 89.4238 83.7252 88.823 81.2324 87.6206C78.7388 86.4183 76.5949 84.8161 74.7998 82.8112C73.004 80.8078 71.6319 78.4531 70.6856 75.7472C69.7356 73.0421 69.2644 70.1868 69.2644 67.1821C69.2644 64.1758 69.7356 61.3205 70.6856 58.6154C71.6319 55.9102 73.004 53.5571 74.7998 51.5522C76.5949 49.5495 78.738 47.9451 81.2324 46.7427C83.7252 45.5404 86.5178 44.9396 89.6103 44.9396C92.7012 44.9396 95.5202 45.5404 98.0637 46.7427C100.606 47.9451 102.776 49.5487 104.572 51.5522C106.367 53.5571 107.762 55.9102 108.76 58.6154C109.756 61.3205 110.256 64.1758 110.256 67.1821C110.256 70.1868 109.756 73.0421 108.76 75.7472Z" fill="currentColor" />
                        <!-- Add remaining paths from the original SVG -->
                    </svg>

                    <!-- 12 Pattern SVG (simplified version) -->
                    <div class="position-absolute w-100 h-100 top-0 start-0">
                        <!-- Add the 12 pattern SVG here if needed -->
                    </div>
                    
                    <!-- Border overlay -->
                    <div class="position-absolute top-0 start-0 w-100 h-100 rounded-top rounded-lg-end rounded-lg-top-0 border border-secondary border-opacity-25"></div>
                </div>
            </main>
        </div>

        @if (Route::has('login'))
            <div style="height: 3.625rem;" class="d-none d-lg-block"></div>
        @endif
    </body>
</html>