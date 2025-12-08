document.addEventListener('DOMContentLoaded', () => {
    // 1. Hide default cursor
    document.body.style.cursor = 'none';

    // 2. Create Plane Element
    const plane = document.createElement('img');
    plane.src = '/images/cursor_plane.png';
    plane.id = 'cursor-plane';
    Object.assign(plane.style, {
        position: 'fixed',
        width: '50px', // Slightly smaller for better usability
        height: 'auto',
        zIndex: '10000',
        pointerEvents: 'none',
        transform: 'translate(-50%, -50%)',
        transition: 'transform 0.1s ease-out'
    });
    document.body.appendChild(plane);

    let mouseX = window.innerWidth / 2;
    let mouseY = window.innerHeight / 2;
    let isMouseMoving = false;
    let lastMouseX = mouseX;
    let facingRight = true;

    // Track mouse
    document.addEventListener('mousemove', (e) => {
        mouseX = e.clientX;
        mouseY = e.clientY;
        isMouseMoving = true;

        // Determine direction
        if (mouseX > lastMouseX + 2) {
            facingRight = true;
        } else if (mouseX < lastMouseX - 2) {
            facingRight = false;
        }
        lastMouseX = mouseX;

        // Move Plane
        plane.style.left = `${mouseX}px`;
        plane.style.top = `${mouseY}px`;

        // Apply Rotation (Flip)
        const scaleX = facingRight ? 1 : -1;
        plane.style.transform = `translate(-50%, -50%) scaleX(${scaleX})`;

        clearTimeout(window.moveTimeout);
        window.moveTimeout = setTimeout(() => {
            isMouseMoving = false;
        }, 100);
    });

    // Cloud Spawning Loop
    setInterval(() => {
        // Spawn rate
        const shouldSpawn = isMouseMoving || Math.random() > 0.6;

        if (shouldSpawn) {
            createSmokePuff(mouseX, mouseY, facingRight);
        }
    }, 80);

    function createSmokePuff(x, y, isFacingRight) {
        // Calculate Tail Position (Behind the plane)
        const tailOffset = isFacingRight ? -25 : 25; // Adjusted for 50px width
        const spawnX = x + tailOffset;
        const spawnY = y + 5;

        const puff = document.createElement('div');

        // Puff Characteristics
        const size = Math.random() * 15 + 8;
        const isOrange = Math.random() > 0.8;

        Object.assign(puff.style, {
            position: 'fixed',
            left: `${spawnX}px`,
            top: `${spawnY}px`,
            width: `${size}px`,
            height: `${size}px`,
            background: isOrange ? 'radial-gradient(circle, rgba(223, 105, 81, 0.8) 10%, rgba(223, 105, 81, 0) 70%)' : 'radial-gradient(circle, rgba(255, 255, 255, 0.9) 10%, rgba(255, 255, 255, 0) 70%)',
            borderRadius: '50%',
            filter: 'blur(3px)',
            pointerEvents: 'none',
            zIndex: '9999',
            transform: 'translate(-50%, -50%) scale(0.5)',
            opacity: '0.8',
            transition: 'transform 1s ease-out, opacity 1s ease-in, left 1s linear, top 1s linear'
        });

        document.body.appendChild(puff);

        requestAnimationFrame(() => {
            // Animate moving opposite to plane direction (smoke trail effect)
            const driftDirection = isFacingRight ? -1 : 1;
            const driftDist = Math.random() * 40 + 10;

            puff.style.transform = `translate(-50%, -50%) scale(${Math.random() * 1 + 1.5})`;
            puff.style.opacity = '0';
            puff.style.left = `${spawnX + (driftDirection * driftDist)}px`;
            puff.style.top = `${spawnY - Math.random() * 15}px`;
        });

        setTimeout(() => {
            puff.remove();
        }, 1000);
    }
});
