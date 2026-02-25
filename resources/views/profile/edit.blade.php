@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-4 fade-in-up">
    <!-- Header -->
    <div class="mb-4 text-center">
        <h2 class="fw-bold text-dark"><i class="fa-solid fa-user-gear me-2 text-primary"></i>My Profile</h2>
        <p class="text-muted">Manage your account settings and personal information.</p>
    </div>

    <div class="row g-4">
        <!-- Left Column: Profile Card -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100 rounded-4 overflow-hidden">
                <div class="card-body p-0">
                    <!-- Profile Header / Cover -->
                    <div class="profile-cover bg-gradient-primary text-white text-center py-5">
                        <div class="avatar-wrapper mb-3 position-relative d-inline-block">
                            <div class="avatar-circle bg-white text-primary shadow overflow-hidden">
                                @if($user->profile_image)
                                    <img src="{{ asset('storage/' . $user->profile_image) }}" alt="Profile" class="w-100 h-100 object-fit-cover">
                                @else
                                    <span class="display-4 fw-bold">{{ substr($user->name, 0, 1) }}</span>
                                @endif
                            </div>
                            <!-- Camera Icon for Upload Trigger -->
                            <label for="profile_image" class="position-absolute bottom-0 end-0 bg-white text-primary rounded-circle shadow p-2 cursor-pointer hover-scale" style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; transform: translate(25%, 25%); cursor: pointer;" title="Change Profile Picture">
                                <i class="fa-solid fa-camera small"></i>
                            </label>
                        </div>
                        <h4 class="fw-bold mb-1">{{ $user->name }}</h4>
                        <p class="mb-2 opacity-75">{{ $user->email }}</p>
                        @if($user->isAdmin())
                            <span class="badge bg-white text-primary rounded-pill px-3 py-2 fw-bold shadow-sm">
                                <i class="fa-solid fa-shield-halved me-1"></i> Administrator
                            </span>
                        @else
                            <span class="badge bg-white text-info rounded-pill px-3 py-2 fw-bold shadow-sm">
                                <i class="fa-solid fa-id-card me-1"></i> Staff Member
                            </span>
                        @endif
                    </div>
                    
                    <!-- Profile Stats / Info -->
                    <div class="p-4">
                        <h6 class="text-uppercase fw-bold text-muted small mb-3 ls-1">Account Information</h6>
                        
                        <div class="d-flex align-items-center mb-3">
                            <div class="icon-square bg-light text-primary rounded-3 me-3">
                                <i class="fa-solid fa-calendar-check"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block">Joined Date</small>
                                <span class="fw-semibold">{{ $user->created_at->format('F d, Y') }}</span>
                            </div>
                        </div>

                        <div class="d-flex align-items-center mb-3">
                            <div class="icon-square bg-light text-success rounded-3 me-3">
                                <i class="fa-solid fa-toggle-on"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block">Status</small>
                                <span class="fw-semibold text-capitalize">{{ $user->status ?? 'Active' }}</span>
                            </div>
                        </div>

                        <hr class="my-4 border-light">
                        
                        <div class="alert alert-light border border-start-0 border-end-0 border-bottom-0 border-top-0 border-primary border-3 bg-light-subtle">
                            <small class="text-muted"><i class="fa-solid fa-circle-info me-1"></i> Need to change your password? Please contact your system administrator.</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Edit Form -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white border-bottom-0 py-4 px-4">
                    <h5 class="fw-bold mb-0 text-dark">Edit Details</h5>
                </div>
                
                <div class="card-body p-4 pt-0">
                    @if (session('success'))
                        <div class="alert alert-success d-flex align-items-center mb-4 rounded-3 shadow-sm border-0" role="alert">
                            <i class="fa-solid fa-circle-check fa-lg me-3"></i>
                            <div>
                                <strong>Success!</strong> {{ session('success') }}
                            </div>
                            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- Hidden File Input -->
                        <input type="file" id="profile_image" name="profile_image" class="d-none" accept="image/*" onchange="previewImage(this)">

                        <div class="row g-4">
                            <!-- Full Name -->
                            <div class="col-md-6">
                                <label for="name" class="form-label fw-semibold text-secondary small text-uppercase ls-1">Full Name</label>
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-solid fa-user"></i></span>
                                    <input id="name" type="text" class="form-control border-start-0 bg-light-subtle ps-0 @error('name') is-invalid @enderror" name="name" value="{{ old('name', $user->name) }}" required autocomplete="name" placeholder="Enter your full name">
                                </div>
                                @error('name')
                                    <span class="text-danger small mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Email Address -->
                            <div class="col-md-6">
                                <label for="email" class="form-label fw-semibold text-secondary small text-uppercase ls-1">Email Address</label>
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-solid fa-envelope"></i></span>
                                    <input id="email" type="email" class="form-control border-start-0 bg-light-subtle ps-0 @error('email') is-invalid @enderror" name="email" value="{{ old('email', $user->email) }}" required autocomplete="email" placeholder="name@company.com">
                                </div>
                                @error('email')
                                    <span class="text-danger small mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Phone Number -->
                            <div class="col-md-6">
                                <label for="phone_number" class="form-label fw-semibold text-secondary small text-uppercase ls-1">Phone Number</label>
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-solid fa-phone"></i></span>
                                    <input id="phone_number" type="text" class="form-control border-start-0 bg-light-subtle ps-0 @error('phone_number') is-invalid @enderror" name="phone_number" value="{{ old('phone_number', $user->phone_number) }}" autocomplete="tel" placeholder="+63 900 000 0000">
                                </div>
                                @error('phone_number')
                                    <span class="text-danger small mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Address -->
                            <div class="col-12">
                                <label for="address" class="form-label fw-semibold text-secondary small text-uppercase ls-1">Complete Address</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted pt-3 align-items-start"><i class="fa-solid fa-location-dot"></i></span>
                                    <textarea id="address" class="form-control border-start-0 bg-light-subtle ps-0 py-3 @error('address') is-invalid @enderror" name="address" autocomplete="street-address" placeholder="House No., Street, City, Province" rows="3">{{ old('address', $user->address) }}</textarea>
                                </div>
                                @error('address')
                                    <span class="text-danger small mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-5">
                            <button type="submit" class="btn btn-primary btn-lg rounded-pill px-5 fw-bold shadow-sm hover-scale">
                                <i class="fa-solid fa-floppy-disk me-2"></i> Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Custom Styling for Profile Page */
    .ls-1 { letter-spacing: 1px; }
    
    .bg-gradient-primary { 
        background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); 
    }
    
    .avatar-wrapper {
        position: relative;
        display: inline-block;
    }
    
    .avatar-circle {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto;
        border: 4px solid rgba(255,255,255,0.3);
    }
    
    .icon-square {
        width: 48px;
        height: 48px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
    }
    
    .form-control:focus { 
        box-shadow: none; 
        border-color: #4f46e5; 
        background-color: #fff !important;
    }
    
    .input-group-text { 
        border-color: #dee2e6; 
    }
    
    .form-control { 
        border-color: #dee2e6; 
    }
    
    .input-group:focus-within .input-group-text { 
        border-color: #4f46e5; 
        color: #4f46e5; 
        background-color: #fff !important;
    }
    
    .input-group:focus-within .form-control { 
        border-color: #4f46e5; 
    }

    .hover-scale {
        transition: transform 0.2s ease;
    }
    
    .hover-scale:hover {
        transform: translateY(-2px);
    }
    
    .bg-light-subtle {
        background-color: #f8f9fa !important;
    }
    
    .object-fit-cover {
        object-fit: cover;
    }
    
    .cursor-pointer {
        cursor: pointer;
    }
</style>

<script>
    function previewImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            
            reader.onload = function(e) {
                // Check if we are updating an image or a text placeholder
                var container = document.querySelector('.avatar-circle');
                var existingImg = container.querySelector('img');
                
                if (existingImg) {
                    existingImg.src = e.target.result;
                } else {
                    // Replace text placeholder with image
                    container.innerHTML = '<img src="' + e.target.result + '" alt="Profile" class="w-100 h-100 object-fit-cover">';
                }
            }
            
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endsection