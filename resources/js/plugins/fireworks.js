import confetti from "canvas-confetti";

export default {
    data() {
        return {
            DURATION_IN_SECONDS: 2
        }
    },
    methods: {
        // from the documentation https://www.kirilv.com/canvas-confetti/
        fireworks() {
            const duration = this.DURATION_IN_SECONDS * 1000;
            const animationEnd = Date.now() + duration;
            const defaults = { startVelocity: 30, spread: 360, ticks: 60, zIndex: 0 };



            var interval = setInterval(() => {
                const timeLeft = animationEnd - Date.now();

                if (timeLeft <= 0) {
                    return clearInterval(interval);
                }
                const randomInRange = this.randomInRange;

                const particleCount = 50 * (timeLeft / duration);
                // since particles fall down, start a bit higher than random
                confetti(Object.assign({}, defaults, { particleCount, origin: { x: randomInRange(0.1, 0.3), y: Math.random() - 0.2 } }));
                confetti(Object.assign({}, defaults, { particleCount, origin: { x: randomInRange(0.7, 0.9), y: Math.random() - 0.2 } }));
            }, 250);
        },

        celebrate() {
            confetti({
                particleCount: 100,
                spread: 70,
                origin: { y: 0.6 }
            });
        },

        randomInRange(min, max) {
            return Math.random() * (max - min) + min;
        }
    }
}
