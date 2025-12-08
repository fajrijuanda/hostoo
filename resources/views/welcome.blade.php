@extends('layouts.app')

@section('content')
    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <h3 class="tagline">Best Hosting Around The World</h3>
            <h1 class="hero-title">
                Host, <span style="color: var(--primary);">enjoy</span> <br>
                and live a new <br>
                digital life
            </h1>
            <p class="hero-text">
                Experience lightning-fast hosting with 99.9% uptime. Secure, scalable, and built for your digital success. Start your journey with us today.
            </p>
            <!-- CTA Buttons Removed as per request -->
        </div>
        <div class="hero-image">
            <!-- Placeholder for Hero Image - utilizing a nice illustration style if available, otherwise text/abstract -->
            <img src="{{ Vite::asset('resources/images/hero-illustration-transparent.png') }}" alt="Hosting Illustration" style="max-width: 100%; border-radius: 20px;">
        </div>
        
        <!-- Animated Clouds -->
        <div class="cloud-container">
            <div class="dynamic-cloud" style="--delay: 0s; --duration: 25s; --top: 10%; --scale: 1.2;"></div>
            <div class="dynamic-cloud" style="--delay: 5s; --duration: 30s; --top: 30%; --scale: 0.8;"></div>
            <div class="dynamic-cloud" style="--delay: 2s; --duration: 22s; --top: 15%; --scale: 1;"></div>
            <div class="dynamic-cloud" style="--delay: 8s; --duration: 28s; --top: 5%; --scale: 1.5;"></div>
            <div class="dynamic-cloud" style="--delay: 12s; --duration: 35s; --top: 25%; --scale: 0.9;"></div>
            <div class="dynamic-cloud" style="--delay: 15s; --duration: 26s; --top: 12%; --scale: 1.1;"></div>
        </div>
    </section>

    <!-- Services Section -->
    <section class="services" id="features">
        <div class="section-title">
            <h3 class="section-subtitle">FEATURES</h3>
            <h2 class="section-heading">We Offer Best Services</h2>
        </div>
        <div class="services-grid">
            <div class="service-card">
                <div class="service-icon">
                    <i class="fas fa-satellite-dish fa-3x" style="color: var(--primary);"></i>
                </div>
                <h4>High Speed</h4>
                <p>Powered by NVMe SSDs and Litespeed for instant loading times and superior performance.</p>
            </div>
            <div class="service-card">
                <div class="service-icon">
                    <i class="fas fa-shield-alt fa-3x" style="color: var(--primary);"></i>
                </div>
                <h4>Secure & Safe</h4>
                <p>Free SSL certificates, DDoS protection, and daily backups to keep your data safe.</p>
            </div>
            <div class="service-card">
                <div class="service-icon">
                    <i class="fas fa-headset fa-3x" style="color: var(--primary);"></i>
                </div>
                <h4>24/7 Support</h4>
                <p>Our expert technical team is available around the clock to assist you with any issues.</p>
            </div>
            <div class="service-card">
                <div class="service-icon">
                    <i class="fas fa-cog fa-3x" style="color: var(--primary);"></i>
                </div>
                <h4>Easy Customization</h4>
                <p>Control Panel included with one-click installers for WordPress and other popular apps.</p>
            </div>
        </div>
    </section>

    <!-- Plans Section (Destinations Reframed) -->
    <section class="plans" id="plans">
        <div class="section-title">
            <h3 class="section-subtitle">Top Selling</h3>
            <h2 class="section-heading">Hosting Plans</h2>
        </div>
        <!-- Carousel Container -->
        <div class="carousel-scene" style="perspective: 1000px; width: 100%; height: 760px; display: flex; justify-content: center; align-items: center; overflow: hidden; position: relative;">
            <div class="carousel-track" id="landingPlanCarousel" style="width: 100%; height: 100%; position: relative; transform-style: preserve-3d;">
                @foreach($plans as $index => $plan)
                <div class="carousel-cell" style="position: absolute; width: 320px; height: 660px; left: 50%; top: 50%; margin-left: -160px; margin-top: -330px; display: flex; flex-direction: column; align-items: center; justify-content: center;">
                    <!-- Original Plan Card Structure -->
                    <div class="plan-card" style="width: 100%; height: 100%; background: white; border-radius: 20px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.1); display: flex; flex-direction: column; backface-visibility: hidden;">
                        <div class="plan-image" style="height: 220px; width: calc(100% - 3rem); margin: 1.5rem; border-radius: 8px; background: url('{{ asset('images/' . $plan->image) }}'); background-size: cover; background-repeat: no-repeat; background-position: center; flex-shrink: 0;"></div>
                        <div class="plan-content" style="padding: 0 1.5rem 6rem 1.5rem; flex: 1; display: flex; flex-direction: column;">
                            <div class="plan-price" style="margin-bottom: 1rem;">
                                <span style="display: block; font-size: 1.1rem; color: #5e6282; margin-bottom: 0.5rem; font-weight: 600;">{{ $plan->name }}</span>
                                @if($plan->price == 0)
                                    <span class="plan-cost" style="font-size: 1.5rem; font-weight: 800; color: #181e4b;">-</span>
                                @elseif($plan->hasActiveDiscount())
                                    <div class="price-container">
                                        <span class="original-price" style="text-decoration: line-through; color: #888; font-size: 0.9em; display: block;">Rp {{ $plan->price >= 1000 ? round($plan->price / 1000) . 'K' : number_format($plan->price, 0, ',', '.') }}</span>
                                        <span class="plan-cost" style="font-size: 1.5rem; font-weight: 800; color: #e63946;">Rp {{ $plan->discount_price >= 1000 ? round($plan->discount_price / 1000) . 'K' : number_format($plan->discount_price, 0, ',', '.') }}</span>
                                    </div>
                                    @if($plan->discount_end_date)
                                        <div class="countdown-timer" data-end-time="{{ $plan->discount_end_date->toIso8601String() }}" style="margin-top: 5px; font-weight: 700; color: #e63946; font-size: 0.85rem;">
                                            Ends in: <span class="time-remaining">Loading...</span>
                                        </div>
                                    @endif
                                @else
                                    <span class="plan-cost" style="font-size: 1.5rem; font-weight: 800; color: #181e4b;">Rp {{ $plan->price >= 1000 ? round($plan->price / 1000) . 'K' : number_format($plan->price, 0, ',', '.') }}</span>
                                @endif
                            </div>
                            
                            <div style="text-align: left; margin-bottom: 1rem; color: #5e6282; font-size: 0.9rem; flex: 1; overflow-y: auto; padding-right: 5px;">
                                <p style="margin-bottom: 15px; font-style: italic; color: #84829a;">{{ Str::limit($plan->description, 60) }}</p>
                                @if($plan->features)
                                    @foreach($plan->features as $feature)
                                        <div style="margin-bottom: 5px;">
                                            <i class="fas fa-check" style="margin-right: 10px; color: var(--primary);"></i> {{ $feature }}
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                            
                            <div style="position: absolute; bottom: 2rem; left: 1.5rem; right: 1.5rem; z-index: 5;">
                                @if($plan->price == 0)
                                    <button class="plan-btn plan-action-btn" style="background-color: #eee; color: #999; cursor: not-allowed;" disabled>Coming Soon</button>
                                @else
                                    <a href="{{ route('plan.select', ['plan_type' => Str::slug($plan->name), 'price' => $plan->hasActiveDiscount() ? $plan->discount_price : $plan->price]) }}" class="plan-btn plan-action-btn" style="background: #df6951; color: white; box-shadow: 0 5px 15px rgba(223, 105, 81, 0.3);">Subscribe Now</a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Timer Logic
                const timers = document.querySelectorAll('.countdown-timer');
                timers.forEach(timer => {
                    const endTime = new Date(timer.dataset.endTime).getTime();
                    const updateTimer = () => {
                        const now = new Date().getTime();
                        const distance = endTime - now;
                        if (distance < 0) {
                            timer.innerHTML = "Offer Expired";
                            return;
                        }
                        const days = Math.floor(distance / (1000 * 60 * 60 * 24));
                        const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                        const seconds = Math.floor((distance % (1000 * 60)) / 1000);
                        timer.querySelector('.time-remaining').innerText = `${days}d ${hours}h ${minutes}m ${seconds}s`;
                    };
                    updateTimer(); setInterval(updateTimer, 1000);
                });

                // Carousel Logic (Tank Tread)
                const track = document.getElementById('landingPlanCarousel');
                if(!track) return;
                
                const cells = Array.from(track.querySelectorAll('.carousel-cell'));
                const count = cells.length;
                
                // Config State
                let gap, visibleCount, totalFrontWidth, leftLimit;
                
                function updateDimensions() {
                    const isMobile = window.innerWidth < 768;
                    
                    if (isMobile) {
                        gap = 0; // Stacked/Centered
                        visibleCount = 1; // Only 1 card in front
                    } else {
                        gap = 340;
                        visibleCount = 4;
                    }

                    // Center if fewer than visibleCount
                    const effectiveVisible = Math.min(count, visibleCount);
                    // For gap=0, totalFrontWidth is 0, leftLimit is 0. Perfect for centering.
                    totalFrontWidth = gap * (effectiveVisible - 1);
                    leftLimit = -totalFrontWidth / 2;
                }
                
                // Initial calc
                updateDimensions();
                
                // Update on resize
                window.addEventListener('resize', updateDimensions);
                
                let targetProgress = 0;
                let isDragging = false;
                let startDragX = 0;
                let startDragProgress = 0;

                function getTransform(p) {
                    let norm = p % count;
                    if (norm < 0) norm += count;
                    
                    const frontCount = 4;
                    let x = 0, z = 0, rY = 0, zIndex = 0, opacity = 1;
                    
                    if (norm < frontCount) {
                        // FRONT
                        x = leftLimit + (norm * gap);
                        z = 0; rY = 0; zIndex = 100; opacity = 1;
                    } 
                    else if (norm >= frontCount && norm < frontCount + 0.5) {
                        // RIGHT FLIP
                        let t = (norm - frontCount) / 0.5;
                        x = leftLimit + ((frontCount-1) * gap) + (t * 50); 
                        z = -200 * t;
                        rY = 180 * t;
                        zIndex = 50;
                        opacity = 1 - (t * 0.2);
                    }
                    else if (norm >= frontCount + 0.5 && norm < count - 0.5) {
                        // BACK RETURN
                        let backRange = count - 1 - frontCount;
                        if(backRange <= 0) backRange = 1; 
                        let t = (norm - (frontCount + 0.5)) / backRange;
                        
                        let rightEdge = leftLimit + ((frontCount-1) * gap) + 50;
                        let leftEdge = leftLimit - 50;
                        x = rightEdge - (t * (rightEdge - leftEdge));
                        z = -200; rY = 180; zIndex = 0; opacity = 0.8;
                    }
                    else {
                        // LEFT FLIP
                        let t = (norm - (count - 0.5)) / 0.5;
                        x = (leftLimit - 50) + (t * 50);
                        z = -200 * (1-t);
                        rY = 180 * (1-t);
                        zIndex = 50;
                        opacity = 0.8 + (t * 0.2);
                    }
                    return `translateX(${x}px) translateZ(${z}px) rotateY(${rY}deg)`;
                }

                function update() {
                    let p = targetProgress;
                    cells.forEach((cell, i) => {
                        let pos = i - p;
                        while(pos < 0) pos += count;
                        while(pos >= count) pos -= count;
                        
                        cell.style.transform = getTransform(pos);
                        
                        // Z-index fix
                        let z = parseFloat(cell.style.transform.match(/translateZ\((-?\d+)/)?.[1] || 0);
                        cell.style.zIndex = z > -50 ? 100 : 10;
                    });
                    requestAnimationFrame(update);
                }
                update();

                // Drag Interaction
                const scene = document.querySelector('.carousel-scene');
                function handleStart(x) { isDragging = true; startDragX = x; startDragProgress = targetProgress; scene.style.cursor = 'grabbing'; }
                function handleMove(x) { 
                    if(!isDragging) return; 
                    const diff = x - startDragX; 
                    targetProgress = startDragProgress - (diff / 350); 
                }
                function handleEnd() { isDragging = false; scene.style.cursor = 'default'; targetProgress = Math.round(targetProgress); }

                scene.addEventListener('mousedown', e => handleStart(e.pageX));
                window.addEventListener('mousemove', e => handleMove(e.pageX));
                window.addEventListener('mouseup', handleEnd);
                scene.addEventListener('touchstart', e => handleStart(e.touches[0].clientX));
                window.addEventListener('touchmove', e => handleMove(e.touches[0].clientX));
                window.addEventListener('touchend', handleEnd);
            });
        </script>
    </section>
@push('styles')
<style>
    /* Global Font Override */
    body, h1, h2, h3, h4, h5, p, span, a, button, input, div {
        font-family: 'Poppins', sans-serif !important;
    }

    /* Force background wave to top for Landing Page (consistent with theme.css) */
    body {
        background-position: center 0 !important;
    }
    body.dark-mode {
        background-position: center 0 !important;
    }

    /* Hero Override for Tighter Spacing */
    .hero {
        padding-top: 2rem !important;
        padding-bottom: 4rem !important;
        min-height: auto !important; /* Allow it to shrink if excessively tall */
    }

    /* Responsive Carousel & Layout */
    @media (max-width: 768px) {
        .steps-section, .testimonials-section, .newsletter-section {
            flex-direction: column;
            padding: 2rem 5% !important;
            text-align: center;
        }
        .steps-content, .steps-image, .testimonial-left, .testimonial-right {
            width: 100% !important;
            margin-bottom: 2rem;
        }
        .steps-image .trip-card {
            margin: 0 auto;
        }
        .step-item {
            justify-content: flex-start; /* Keep steps aligned left or center? Center likely for mobile */
            text-align: left;
        }
        

        /* Carousel Native Resizing for Mobile (One Card View) */
        .carousel-scene {
            height: 700px !important;
            width: 100% !important;
            margin-left: 0;
            perspective: 800px;
        }
        .carousel-cell {
            width: 280px !important;
            height: 620px !important;
            margin-left: -140px !important;
            margin-top: -310px !important;
        }
        .plan-image {
            height: 180px !important;
            width: calc(100% - 3rem) !important;
            margin: 1.5rem !important;
            border-radius: 8px !important;
        }
        .plan-content {
            padding: 0 1.5rem 6rem 1.5rem !important; /* Adjusted bottom padding to 6rem for absolute button */
        }
        .plan-price .plan-cost {
            font-size: 1.3rem !important;
        }
        .plan-btn {
            padding: 12px !important;
            font-size: 1rem !important;
        }
    }

    /* Steps Section */
    .steps-section {
        padding: 3rem 8%;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 3rem;
        background: transparent;
    }
    .steps-content {
        flex: 1;
    }
    .steps-subtitle {
        color: #5e6282;
        font-weight: 600;
        margin-bottom: 0.5rem;
    }
    .steps-title {
        font-size: 2.5rem;
        font-weight: 800;
        color: #181e4b;
        margin-bottom: 1.5rem;
        line-height: 1.2;
        /* font-family: 'Volkhov', serif; -- REMOVED for Poppins */
    }
    .step-item {
        display: flex;
        gap: 1.2rem;
        margin-bottom: 1.5rem;
        align-items: flex-start;
    }
    .step-icon {
        width: 45px;
        height: 45px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        flex-shrink: 0;
    }
    .step-icon.yellow { background-color: #f1a501; }
    .step-icon.red { background-color: #df6951; }
    .step-icon.blue { background-color: #006380; }
    
    .step-text h4 {
        color: #5e6282;
        font-weight: 700;
        margin-bottom: 0.3rem;
        font-size: 1rem;
    }
    .step-text p {
        color: #5e6282;
        font-size: 0.9rem;
        line-height: 1.4;
        margin: 0;
    }

    /* Steps Image Card */
    .steps-image {
        flex: 1;
        position: relative;
    }
    .trip-card {
        background: white;
        border-radius: 24px;
        padding: 18px;
        box-shadow: 0 40px 60px rgba(0,0,0,0.05); /* Reduced shadow spread */
        max-width: 320px;
        margin: 0 auto;
        position: relative;
        z-index: 2;
    }
    .trip-image {
        width: 100%;
        height: 150px;
        border-radius: 20px;
        background: url('{{ Vite::asset("resources/images/hostoo-success-bg.png") }}');
        background-size: cover;
        background-position: center;
        margin-bottom: 1rem;
    }
    .trip-title {
        color: #080809;
        font-size: 1.1rem;
        font-weight: 500;
        margin-bottom: 0.5rem;
    }
    .trip-meta {
        color: #84829a;
        font-size: 0.85rem;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .trip-icons {
        display: flex;
        gap: 12px;
        margin-bottom: 1rem;
    }
    .trip-icon-circle {
        width: 32px;
        height: 32px;
        background: #f5f5f5;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #84829a;
        font-size: 0.7rem;
    }
    .trip-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .trip-people {
        display: flex;
        align-items: center;
        gap: 8px;
        color: #84829a;
        font-size: 0.85rem;
    }

    /* Floating Review Card */
    .float-review-card {
        position: absolute;
        bottom: 40px;
        right: -20px;
        background: white;
        padding: 12px;
        border-radius: 16px;
        box-shadow: 0 15px 30px rgba(0,0,0,0.08);
        display: flex;
        gap: 12px;
        align-items: flex-start;
        z-index: 3;
        width: 240px;
    }
    .float-avatar {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        object-fit: cover;
    }
    .float-content h5 {
        color: #080809;
        font-size: 0.85rem;
        margin-bottom: 2px;
        font-weight: 600;
    }
    .float-content p {
        color: #84829a;
        font-size: 0.75rem;
        margin-bottom: 6px;
    }
    .float-progress {
        height: 4px;
        background: #f5f5f5;
        border-radius: 2px;
        overflow: hidden;
    }
    .float-progress-bar {
        width: 40%;
        height: 100%;
        background: #8a79df;
    }

    /* Testimonials Section */
    .testimonials-section {
        padding: 3rem 8%;
        display: flex;
        gap: 3rem;
        background: transparent;
        margin-bottom: 3rem;
    }
    .testimonial-left {
        width: 40%;
    }
    .testimonial-subtitle {
        color: #5e6282;
        font-weight: 600;
        margin-bottom: 0.5rem;
        text-transform: uppercase;
        letter-spacing: 2px;
        font-size: 0.9rem;
    }
    .testimonial-title {
        font-size: 2.5rem;
        font-weight: 800;
        color: #181e4b;
        line-height: 1.2;
        /* font-family: 'Volkhov', serif; -- REMOVED for Poppins */
        margin-bottom: 1rem;
    }
    .testimonial-right {
        flex: 1;
        position: relative;
    }
    .testimonial-card-main {
        background: white;
        border-radius: 12px;
        padding: 2rem;
        box-shadow: 0 8px 30px rgba(0,0,0,0.05);
        position: relative;
        z-index: 2;
        max-width: 500px;
    }
    .testimonial-avatar {
        position: absolute;
        top: -25px;
        left: -25px;
        width: 50px;
        height: 50px;
        border-radius: 50%;
        overflow: hidden;
        border: 4px solid white; /* Border to separate from background */
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }
    .testimonial-text {
        color: #5e6282;
        line-height: 1.8;
        font-size: 0.95rem;
        margin-bottom: 1rem;
    }
    .testimonial-author h4 {
        color: #181e4b;
        font-weight: 700;
        margin-bottom: 3px;
        font-size: 1rem;
    }
    .testimonial-author p {
        color: #5e6282;
        font-size: 0.85rem;
    }

    .plan-action-btn {
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        width: 100% !important;
        height: 50px !important; /* Force fixed height */
        margin: 0 !important;
        padding: 0 !important;
        border-radius: 10px !important;
        font-weight: 600 !important;
        font-size: 1rem !important;
        border: none !important;
        text-align: center !important;
        text-decoration: none !important;
        cursor: pointer;
    }

    /* Dark Mode Overrides for Welcome Page Cards */
    body.dark-mode .trip-card,
    body.dark-mode .float-review-card,
    body.dark-mode .testimonial-card-main {
        background: #1e1e1e;
        color: #f1f1f1;
        box-shadow: 0 10px 30px rgba(0,0,0,0.3);
    }
    body.dark-mode .trip-title,
    body.dark-mode .testimonial-author h4,
    body.dark-mode .float-content h5 {
        color: #f1f1f1;
    }
    body.dark-mode .trip-meta,
    body.dark-mode .trip-people,
    body.dark-mode .float-content p,
    body.dark-mode .testimonial-text,
    body.dark-mode .testimonial-author p {
        color: #aaa;
    }
    body.dark-mode .trip-icon-circle {
        background: #2d2d2d;
        color: #aaa;
    }
    body.dark-mode .testimonial-avatar {
        border-color: #1e1e1e;
    }

    /* Newsletter Dark Mode */
    body.dark-mode .newsletter-container {
        background: #1e1e1e !important;
        box-shadow: 0 10px 40px rgba(0,0,0,0.3) !important;
    }
    body.dark-mode .newsletter-container h2 {
        color: #f1f1f1 !important;
    }
    body.dark-mode .email-input-box {
        background: #2d2d2d !important;
        border-color: #444 !important;
    }
    body.dark-mode .email-input-box input {
        background: transparent !important;
        color: #f1f1f1 !important;
    }
    body.dark-mode .email-input-box i {
        color: #aaa !important;
    }
</style>
@endpush

    <!-- Steps Section -->
    <section class="steps-section">
        <div class="steps-content">
            <h3 class="steps-subtitle">Easy and Fast</h3>
            <h1 class="steps-title">Launch Your Next Site<br>In 3 Easy Steps</h1>
            
            <div class="step-item">
                <div class="step-icon yellow">
                    <i class="fas fa-mouse-pointer"></i>
                </div>
                <div class="step-text">
                    <h4>Choose Your Plan</h4>
                    <p>Select the perfect hosting plan that suits your needs, from Starter to Business.</p>
                </div>
            </div>
            
            <div class="step-item">
                <div class="step-icon red">
                    <i class="fas fa-wallet"></i>
                </div>
                <div class="step-text">
                    <h4>Make Payment</h4>
                    <p>Securely process your payment using our wide range of supported gateways.</p>
                </div>
            </div>
            
            <div class="step-item">
                <div class="step-icon blue">
                    <i class="fas fa-rocket"></i>
                </div>
                <div class="step-text">
                    <h4>Launch Website</h4>
                    <p>Get instant access to your control panel and publish your site to the world.</p>
                </div>
            </div>
        </div>
        
        <div class="steps-image">
            <div class="trip-card">
                <div class="trip-image"></div>
                <h3 class="trip-title">My Awesome Website</h3>
                <div class="trip-meta">
                    <span>Active Status</span> | <span>Managed</span>
                </div>
                <div class="trip-icons">
                    <div class="trip-icon-circle"><i class="fas fa-leaf"></i></div>
                    <div class="trip-icon-circle"><i class="fas fa-map"></i></div>
                    <div class="trip-icon-circle"><i class="fas fa-paper-plane"></i></div>
                </div>
                <div class="trip-footer">
                    <div class="trip-people">
                        <i class="fas fa-server"></i> 99.9% Uptime
                    </div>
                </div>
            </div>
            
            <div class="float-review-card">
                <img src="{{ Vite::asset('resources/images/avatar-mike.png') }}" class="float-avatar" alt="Avatar">
                <div class="float-content">
                    <h5>Deployment</h5>
                    <p>Server Config</p>
                    <p><span style="color: #8a79df; font-weight: bold;">40%</span> completed</p>
                    <div class="float-progress">
                        <div class="float-progress-bar"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="testimonials-section">
        <div class="testimonial-left">
            <h3 class="testimonial-subtitle">TESTIMONIALS</h3>
            <h1 class="testimonial-title">What People Say<br>About Us.</h1>
        </div>
        <div class="testimonial-right">
            <div class="testimonial-card-main">
                <img src="{{ Vite::asset('resources/images/avatar-mike.png') }}" class="testimonial-avatar" alt="Mike Taylor">
                <p class="testimonial-text">
                    "On the Windows talking painted pasture yet its express parties use. Sure last upon he same as knew next. Of believed or diverted no."
                </p>
                <div class="testimonial-author">
                    <h4>Mike Taylor</h4>
                    <p>Lahore, Pakistan</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Newsletter Section -->
    <section class="newsletter-section" style="padding: 4rem 10%; margin-bottom: 5rem; position: relative;">
        <div class="newsletter-container" style="
            background: #f5f0ff; 
            background: linear-gradient(135deg, #e5dfff 0%, #fcfcff 100%);
            border-radius: 20px 120px 20px 20px; 
            padding: 5rem 2rem; 
            text-align: center; 
            position: relative; 
            box-shadow: 0 10px 40px rgba(0,0,0,0.03);">
            
            <!-- Decor: Faint Rings Left -->
            <div style="
                position: absolute; 
                bottom: -50px; 
                left: -50px; 
                width: 300px; 
                height: 300px; 
                border-radius: 50%; 
                border: 1px solid rgba(138, 121, 223, 0.1); 
                z-index: 1;">
            </div>
            <div style="
                position: absolute; 
                bottom: -30px; 
                left: -30px; 
                width: 260px; 
                height: 260px; 
                border-radius: 50%; 
                border: 1px solid rgba(138, 121, 223, 0.1); 
                z-index: 1;">
            </div>
            <div style="
                position: absolute; 
                bottom: -10px; 
                left: -10px; 
                width: 220px; 
                height: 220px; 
                border-radius: 50%; 
                border: 1px solid rgba(138, 121, 223, 0.1); 
                z-index: 1;">
            </div>

             <!-- Decor: Faint Rings Right (Top) -->
             <div style="
                position: absolute; 
                top: -50px; 
                right: -50px; 
                width: 300px; 
                height: 300px; 
                border-radius: 50%; 
                border: 1px solid rgba(138, 121, 223, 0.1); 
                z-index: 1;">
            </div>
            <div style="
                position: absolute; 
                top: -30px; 
                right: -30px; 
                width: 260px; 
                height: 260px; 
                border-radius: 50%; 
                border: 1px solid rgba(138, 121, 223, 0.1); 
                z-index: 1;">
            </div>

            <h2 style="
                color: #5e6282; 
                font-size: 1.8rem; 
                font-family: 'Volkhov', serif; 
                line-height: 1.4; 
                margin-bottom: 3rem; 
                max-width: 800px; 
                margin-left: auto; 
                margin-right: auto;
                position: relative;
                z-index: 2;
            ">
                Subscribe to get information, latest news and other<br>interesting offers about Hostoo
            </h2>

            @if(session('success'))
                <div style="background: rgba(72, 187, 120, 0.1); color: #2f855a; padding: 10px; border-radius: 8px; margin-bottom: 1rem; font-weight: 500;">
                    <i class="fas fa-check-circle" style="margin-right: 5px;"></i> {{ session('success') }}
                </div>
            @endif
            @error('email')
                <div style="color: #e53e3e; font-size: 0.9rem; margin-bottom: 1rem;">{{ $message }}</div>
            @enderror

            <form action="{{ route('subscribe') }}" method="POST" style="display: flex; justify-content: center; gap: 1rem; flex-wrap: wrap; position: relative; z-index: 2;">
                @csrf
                <div class="email-input-box" style="
                    background: white; 
                    padding: 0 1.5rem; 
                    border-radius: 10px; 
                    display: flex; 
                    align-items: center; 
                    gap: 10px; 
                    flex: 1; 
                    max-width: 400px;
                    border: 1px solid #eee;
                    height: 55px;
                ">
                    <i class="far fa-envelope" style="color: #999;"></i>
                    <input type="email" name="email" placeholder="Your email" required style="border: none; outline: none; width: 100%; color: #333;">
                </div>
                <!-- Orange Gradient Button matching reference -->
                <button type="submit" style="
                    background: linear-gradient(180deg, #ff946d 0%, #ff7d68 100%); 
                    color: white; 
                    border: none; 
                    padding: 0 2.5rem; 
                    border-radius: 10px; 
                    font-weight: 500; 
                    cursor: pointer; 
                    height: 55px;
                    box-shadow: 0 4px 15px rgba(255, 125, 104, 0.3);
                    transition: transform 0.2s;
                    font-size: 1rem;
                " onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">
                    Subscribe
                </button>
            </form>

            <!-- Floating Paper Plane Icon Decor (Top Right of Card) -->
            <div style="
                position: absolute; 
                top: -20px; 
                right: -20px; 
                width: 70px; 
                height: 70px; 
                background: linear-gradient(201.65deg, #747DEF 10.27%, #5E3BE1 100%);
                border-radius: 50%; 
                display: flex; 
                align-items: center; 
                justify-content: center; 
                color: white; 
                font-size: 1.8rem;
                box-shadow: 0 4px 15px rgba(94, 59, 225, 0.4);
                z-index: 3;
            ">
                <i class="fas fa-paper-plane" style="transform: translate(-2px, 2px);"></i>
            </div>
        </div>

        <!-- Plus Pattern Decor (Outside Bottom Right) -->
        <div style="position: absolute; bottom: 0; right: 8%; opacity: 0.3;">
            <div style="color: #dfd7f9; font-size: 1.5rem; line-height: 1;">
                + + +<br>
                + + +<br>
                + + +
            </div>
        </div>
    </section>
@endsection
