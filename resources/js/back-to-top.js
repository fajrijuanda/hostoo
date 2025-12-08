document.addEventListener('DOMContentLoaded', () => {
    // Create the button element dynamically if not present
    let backToTopBtn = document.getElementById('back-to-top');
    if (!backToTopBtn) {
        backToTopBtn = document.createElement('div');
        backToTopBtn.id = 'back-to-top';
        // Use Image as requested
        backToTopBtn.innerHTML = '<img src="/images/cursor_plane.png" alt="Back to Top">';
        document.body.appendChild(backToTopBtn);
    }

    // Scroll Visibility Logic
    window.addEventListener('scroll', () => {
        if (window.scrollY > 300) {
            backToTopBtn.classList.add('visible');
        } else {
            backToTopBtn.classList.remove('visible');
        }
    });

    // Click Animation Logic
    backToTopBtn.addEventListener('click', () => {
        // 1. Calculate Adaptive Duration - LONGER for zig-zag
        // Base 6s + more for longer pages. Cap at 12s.
        const scrollHeight = window.scrollY;
        let calculatedDuration = 6000 + (scrollHeight / 1000) * 1000;
        calculatedDuration = Math.min(Math.max(calculatedDuration, 7000), 12000);

        // Pass duration to CSS
        backToTopBtn.style.setProperty('--fly-duration', `${calculatedDuration}ms`);

        // 2. Trigger Animation Class
        backToTopBtn.classList.add('flying');

        // 3. Start Smoke Trail Loop
        const trailInterval = setInterval(() => {
            spawnTrailSmoke();
        }, 80);

        // 4. Smooth Scroll to Top (Synced with Start of Ascent Phase ~70%)
        const startPosition = window.scrollY;
        const startTime = performance.now();

        // We want the scroll to mostly happen during the last 30% of the animation (the ascent)
        // But we can start slowly earlier.

        function slowScroll(currentTime) {
            const timeElapsed = currentTime - startTime;
            const progress = Math.min(timeElapsed / calculatedDuration, 1);

            // Custom Easing: Hold position mostly until 60-70%, then scroll up
            let scrollProgress = 0;
            if (progress < 0.6) {
                // Minimal movement (0-10% scroll) during first 60% of time
                scrollProgress = (progress / 0.6) * 0.1;
            } else {
                // Fast movement (10-100% scroll) during last 40% of time
                const p2 = (progress - 0.6) / 0.4;
                // Ease out cubic
                const ease = 1 - Math.pow(1 - p2, 3);
                scrollProgress = 0.1 + (0.9 * ease);
            }

            window.scrollTo(0, startPosition * (1 - scrollProgress));

            if (timeElapsed < calculatedDuration) {
                requestAnimationFrame(slowScroll);
            }
        }

        requestAnimationFrame(slowScroll);

        // 5. Reset after animation completes
        setTimeout(() => {
            clearInterval(trailInterval); // Stop smoke
            backToTopBtn.classList.remove('flying');
        }, calculatedDuration + 100); // Small buffer
    });

    function spawnTrailSmoke() {
        // Get dynamic position of the moving button
        const rect = backToTopBtn.getBoundingClientRect();

        // Only spawn if on screen
        if (rect.top < -50 || rect.top > window.innerHeight + 50) return;

        // Determine Plane Facing (based on CSS transform or inferred from logic)
        // Simplified: We assume tail is "behind" movement.
        // But extracting current rotation is hard. Let's just spawn at center.

        const puff = document.createElement('div');
        const size = Math.random() * 15 + 10;
        const isOrange = Math.random() > 0.7; // 30% chance of orange

        Object.assign(puff.style, {
            position: 'fixed',
            left: `${rect.left + rect.width / 2}px`,
            top: `${rect.top + rect.height / 2}px`,
            width: `${size}px`,
            height: `${size}px`,
            background: isOrange
                ? 'radial-gradient(circle, rgba(223, 105, 81, 0.8) 10%, rgba(223, 105, 81, 0) 70%)'
                : 'radial-gradient(circle, rgba(255, 255, 255, 0.9) 10%, rgba(255, 255, 255, 0) 70%)',
            borderRadius: '50%',
            filter: 'blur(4px)',
            pointerEvents: 'none',
            zIndex: '9998',
            transform: 'translate(-50%, -50%) scale(0.5)',
            opacity: '0.8',
            transition: 'transform 1s ease-out, opacity 1s ease-in, left 1s, top 1s'
        });

        document.body.appendChild(puff);

        requestAnimationFrame(() => {
            // Apply drift - Randomize slightly
            const driftX = (Math.random() - 0.5) * 50;
            const driftY = Math.random() * 30 + 10; // Drift down slightly (gravity/wake)

            puff.style.transform = `translate(-50%, -50%) scale(${Math.random() * 1 + 1.5})`;
            puff.style.opacity = '0';
            puff.style.left = `${parseFloat(puff.style.left) + driftX}px`;
            puff.style.top = `${parseFloat(puff.style.top) + driftY}px`;
        });

        setTimeout(() => {
            puff.remove();
        }, 1000);
    }
});
