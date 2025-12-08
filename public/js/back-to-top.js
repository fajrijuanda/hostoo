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
        // 1. Calculate Adaptive Duration - SLOWER
        // Min 4s, then adds 1.5s for every 1500px. Max cap 8s.
        const scrollHeight = window.scrollY;
        let calculatedDuration = 3000 + (scrollHeight / 1500) * 1500;
        calculatedDuration = Math.min(Math.max(calculatedDuration, 4000), 8000); // Clamp between 4s and 8s

        // Pass duration to CSS
        backToTopBtn.style.setProperty('--fly-duration', `${calculatedDuration}ms`);

        // 2. Trigger Animation Class
        backToTopBtn.classList.add('flying');

        // 3. Start Smoke Trail Loop
        const trailInterval = setInterval(() => {
            spawnTrailSmoke();
        }, 60);

        // 4. Smooth Scroll to Top (Synced with Duration)
        const startPosition = window.scrollY;
        const startTime = performance.now();

        function slowScroll(currentTime) {
            const timeElapsed = currentTime - startTime;
            const progress = Math.min(timeElapsed / calculatedDuration, 1);

            // Matches CSS cubic-bezier(0.45, 0, 0.55, 1) approx (Ease-In-Out)
            const ease = progress < 0.5
                ? 2 * progress * progress
                : 1 - Math.pow(-2 * progress + 2, 2) / 2;

            window.scrollTo(0, startPosition * (1 - ease));

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

        // Spawn puff at center/tail
        const puff = document.createElement('div');
        const size = Math.random() * 20 + 20; // 20-40px puffs

        Object.assign(puff.style, {
            position: 'fixed',
            left: `${rect.left + rect.width / 2}px`,
            top: `${rect.top + rect.height / 2}px`,
            width: `${size}px`,
            height: `${size}px`,
            background: 'radial-gradient(circle, rgba(220,220,220,0.8) 0%, rgba(255,255,255,0) 70%)',
            borderRadius: '50%',
            filter: 'blur(6px)',
            pointerEvents: 'none',
            zIndex: '9998',
            transform: 'translate(-50%, -50%) scale(0.2)',
            opacity: '0.7',
            transition: 'transform 1.5s ease-out, opacity 1.5s ease-in'
        });

        document.body.appendChild(puff);

        requestAnimationFrame(() => {
            puff.style.transform = `translate(-50%, -50%) scale(2.0)`;
            puff.style.opacity = '0';
        });

        setTimeout(() => {
            puff.remove();
        }, 1500);
    }
});
