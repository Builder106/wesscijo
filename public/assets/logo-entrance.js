(() => {
  const root = document.documentElement;
  if (!root.classList.contains('logo-entrance')) return;

  const { controller, blob } = window.logoFlight ?? {};
  const stage = document.getElementById('logo-entrance');
  const mark = stage?.querySelector('.logo-entrance__mark');
  const flight = stage?.querySelector('.logo-entrance__flight');
  const backdrop = stage?.querySelector('.logo-entrance__backdrop');
  const skip = stage?.querySelector('.logo-entrance__skip');
  const target = document.querySelector('.hero__logo-slot');
  const page = [...document.querySelectorAll('.hero, .site-main, .colophon')];
  const motion = matchMedia('(prefers-reduced-motion: reduce)');
  let landingTimer;
  let animations = [];
  let settled = false;

  const settle = () => {
    if (settled) return;
    settled = true;
    clearTimeout(landingTimer);
    clearTimeout(window.logoEntranceWatchdog);
    controller?.abort();
    animations.forEach((animation) => animation.cancel());
    root.classList.remove('logo-entrance', 'logo-entrance-revealing');
    motion.removeEventListener('change', onMotionChange);
    document.removeEventListener('keydown', onKeydown);
    if (document.activeElement === skip) document.getElementById('main')?.focus({ preventScroll: true });
  };

  function onMotionChange() {
    if (motion.matches) settle();
  }

  function onKeydown(event) {
    if (event.key === 'Escape') settle();
  }

  if (!stage || !mark || !flight || !backdrop || !skip || !target || !blob || motion.matches || getComputedStyle(stage).position !== 'fixed') {
    settle();
    return;
  }

  stage.style.removeProperty('display');
  // The watchdog only covers this script failing to run; it must not cut a slow flight short.
  clearTimeout(window.logoEntranceWatchdog);

  const moveToMasthead = () => {
    if (!root.classList.contains('logo-entrance') || motion.matches) {
      settle();
      return;
    }

    const start = mark.getBoundingClientRect();
    const end = target.getBoundingClientRect();
    const x = end.left + end.width / 2 - start.left - start.width / 2;
    const y = end.top + end.height / 2 - start.top - start.height / 2;
    const scale = end.width / start.width;
    root.classList.add('logo-entrance-revealing');

    const logoAnimation = mark.animate([
      { transform: 'translate(-50%, -50%)' },
      { transform: `translate(calc(-50% + ${x}px), calc(-50% + ${y}px)) scale(${scale})` },
    ], { duration: 620, easing: 'cubic-bezier(.22, 1, .36, 1)', fill: 'forwards' });
    animations = [
      logoAnimation,
      backdrop.animate([{ opacity: 1 }, { opacity: 0 }], { duration: 540, delay: 80, fill: 'forwards' }),
      ...page.map((element) => element.animate([{ opacity: 0 }, { opacity: 1 }], {
        duration: 400,
        delay: 180,
        easing: 'ease-out',
        fill: 'forwards',
      })),
    ];
    logoAnimation.addEventListener('finish', settle, { once: true });
  };

  motion.addEventListener('change', onMotionChange);
  document.addEventListener('keydown', onKeydown);
  skip.addEventListener('click', settle);
  flight.addEventListener('load', () => {
    if (!root.classList.contains('logo-entrance')) {
      settle();
      return;
    }
    landingTimer = setTimeout(moveToMasthead, 3050);
  }, { once: true });
  flight.addEventListener('error', settle, { once: true });
  blob.then((file) => {
    if (settled) return;
    // A fresh object URL restarts the flight on every load while the file itself stays cached.
    flight.src = URL.createObjectURL(file);
  }, settle);
})();
