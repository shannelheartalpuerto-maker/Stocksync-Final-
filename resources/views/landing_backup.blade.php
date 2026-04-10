@extends('layouts.auth')

@section('content')
<div class="landing-shell">
    <div class="landing-shell-bg"></div>

    <div class="container-fluid px-3 px-md-4 px-xl-5 py-2 py-md-3 landing-shell-wrap">
        <div class="landing-hero-intro">
            <div class="landing-hero-intro-copy">
                <div class="landing-chip landing-chip-light">
                    <span class="landing-chip-dot"></span>
                    StockSync: Smart Inventory Management System
                </div>
                <h1 class="landing-title">Smarter stock management for staff who need speed, clarity, and control.</h1>
                <p class="landing-text">StockSync helps your team track inventory, process sales, and keep operations accurate from one clean workspace. It is built to stay fast on desktop and mobile alike.</p>
            </div>
            <div class="landing-hero-actions">
                <a href="{{ route('register') }}" class="btn btn-primary-custom landing-auth-btn">Register</a>
                <a href="{{ route('login') }}" class="btn btn-primary-custom landing-auth-btn">Log In</a>
            </div>
        </div>

        <div class="row g-4 align-items-start landing-grid">
            <div class="col-lg-7 order-2 order-lg-1">
                <div class="landing-hero-panel">
                    <div class="landing-orb landing-orb-a"></div>
                    <div class="landing-orb landing-orb-b"></div>

                    <div class="landing-intro-grid">
                        <div class="landing-intro-card landing-intro-main">
                            <div class="landing-intro-kicker">Introduction</div>
                            <h3>One system for inventory, sales, and accountability.</h3>
                            <p>From stock visibility to checkout, every part of the workflow is organized so staff can work quickly without losing control of data.</p>
                        </div>
                        <div class="landing-intro-card landing-intro-side">
                            <div class="landing-intro-kicker">What it does</div>
                            <ul>
                                <li>Monitors product stock in real time</li>
                                <li>Speeds up POS transactions and barcode scanning</li>
                                <li>Separates admin and staff access clearly</li>
                            </ul>
                        </div>
                    </div>

                    <div class="landing-goal-row">
                        <div class="landing-goal-pill"><i class="fa-solid fa-bullseye"></i> Clear goals</div>
                        <div class="landing-goal-pill"><i class="fa-solid fa-chart-line"></i> Faster operations</div>
                        <div class="landing-goal-pill"><i class="fa-solid fa-shield-halved"></i> Secure access</div>
                    </div>
                </div>
            </div>

            <div class="col-lg-5 order-1 order-lg-2 d-flex align-items-center">
                <div class="landing-side-card">
                    <div class="landing-side-head">
                        <div class="landing-side-kicker">Before you continue</div>
                        <h2 class="landing-side-title">See what StockSync is built to do.</h2>
                        <p class="landing-side-copy">Explore the platform’s purpose, then choose whether to register or log in.</p>
                    </div>

                    <div class="landing-info-list">
                        <div class="landing-info-item">
                            <div class="landing-info-icon"><i class="fa-solid fa-eye"></i></div>
                            <div>
                                <div class="landing-info-title">Mission</div>
                                <div class="landing-info-text">Keep store stock and sales visible in one place.</div>
                            </div>
                        </div>
                        <div class="landing-info-item">
                            <div class="landing-info-icon"><i class="fa-solid fa-bullseye"></i></div>
                            <div>
                                <div class="landing-info-title">Goal</div>
                                <div class="landing-info-text">Help staff work faster with fewer mistakes.</div>
                            </div>
                        </div>
                        <div class="landing-info-item">
                            <div class="landing-info-icon"><i class="fa-solid fa-location-dot"></i></div>
                            <div>
                                <div class="landing-info-title">Designed for</div>
                                <div class="landing-info-text">Retail teams that need a clean POS and inventory flow.</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection