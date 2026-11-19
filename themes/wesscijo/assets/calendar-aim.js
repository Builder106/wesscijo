/* The calendar telescope points at one upcoming day. Tapping or clicking a
   star, or hovering or focusing an event, swings the tube to that day's star
   on a light spring; the cardinal rides the tube and stays upright, and the
   eyepiece shows what the telescope is looking at (its title links to the
   event). Without this script a star is a plain link to its event. Under
   reduced motion the tube jumps. */
(function () {
	var fig = document.querySelector('.calendar__scope');
	var tube = fig && fig.querySelector('.refractor__tube');
	if (!tube) {
		return;
	}
	var bird = fig.querySelector('.refractor__bird');
	var head = bird && bird.querySelector('.cardinal__head');
	var tail = bird && bird.querySelector('.cardinal__tail');
	var eye = fig.querySelector('.eyepiece');
	var stars = Array.prototype.slice.call(fig.querySelectorAll('.refractor__star'));
	var motion = window.matchMedia('(prefers-reduced-motion: no-preference)');
	var aimed = fig.querySelector('.refractor__star.is-aimed');
	var angle = aimed ? parseFloat(aimed.getAttribute('data-angle')) : 22;
	var from = angle;
	var target = angle;
	var start = 0;
	var frame = 0;

	// The bird leans half as far as the tube, so the pipette in its feet stays
	// close to the tube at steep aims without the bird tipping over.
	function draw(a) {
		tube.setAttribute('transform', 'rotate(' + (-a) + ' 215 150)');
		if (bird) {
			bird.setAttribute('transform', 'rotate(' + (a * 0.5 + 2) + ' 300 136)');
		}
	}

	// Tilt the head up toward the aimed star, turning on the throat like
	// cardinal-watch.js does (pivot in drawing units from the head's origin).
	var THROAT = [18, 48];
	function look(a, flick) {
		if (!head || !motion.matches) {
			return;
		}
		// -4 to -10 degrees: past about 10 the body layer shows behind the crest.
		var turn = -4 - (a - 12) * 0.2;
		head.style.transform = 'translate(' + THROAT[0] + 'px, ' + THROAT[1] + 'px) rotate(' + turn + 'deg) translate(' + -THROAT[0] + 'px, ' + -THROAT[1] + 'px)';
		if (flick && tail && tail.animate) {
			tail.animate(
				[{ transform: 'rotate(0deg)' }, { transform: 'rotate(-7deg)', offset: 0.35 }, { transform: 'rotate(0deg)' }],
				{ duration: 420, easing: 'ease-out' }
			);
		}
	}

	// Underdamped spring: about 12% overshoot, settled after 1.2 s.
	function step(now) {
		var t = (now - start) / 1000;
		var k = t >= 1.2 ? 1 : 1 - Math.exp(-6 * t) * Math.cos(9 * t);
		angle = from + (target - from) * k;
		draw(angle);
		frame = t < 1.2 ? window.requestAnimationFrame(step) : 0;
	}

	function aim(day) {
		var star = null;
		stars.forEach(function (s) {
			if (s.getAttribute('data-day') === day) {
				star = s;
			}
		});
		if (!star || star === aimed) {
			return;
		}
		stars.forEach(function (s) {
			s.classList.toggle('is-aimed', s === star);
			s.setAttribute('aria-pressed', s === star ? 'true' : 'false');
		});
		aimed = star;
		if (eye) {
			eye.querySelector('.eyepiece__date').textContent = star.getAttribute('data-date');
			eye.querySelector('.eyepiece__title').textContent = star.getAttribute('data-title');
			eye.querySelector('.eyepiece__title').setAttribute('href', star.getAttribute('href'));
			eye.querySelector('.eyepiece__when').textContent = star.getAttribute('data-when');
		}
		target = parseFloat(star.getAttribute('data-angle'));
		look(target, true);
		if (!motion.matches) {
			angle = target;
			draw(angle);
			return;
		}
		from = angle;
		start = performance.now();
		if (!frame) {
			frame = window.requestAnimationFrame(step);
		}
	}

	// Opening move: the tube starts low and sweeps up to the next event.
	if (motion.matches && aimed) {
		target = angle;
		window.setTimeout(function () { look(target, false); }, 900);
		from = 4;
		angle = 4;
		draw(angle);
		window.setTimeout(function () {
			start = performance.now();
			frame = window.requestAnimationFrame(step);
		}, 350);
	}

	// First press aims at the star; a press on the star already aimed goes to
	// its event in the list and moves focus there.
	function activate(star) {
		if (star !== aimed) {
			aim(star.getAttribute('data-day'));
			return;
		}
		var row = document.getElementById(star.getAttribute('href').slice(1));
		if (!row) {
			return;
		}
		row.scrollIntoView({ behavior: motion.matches ? 'smooth' : 'auto', block: 'start' });
		var link = row.querySelector('.event__title a');
		if (link) {
			link.focus({ preventScroll: true });
		}
	}

	function hook(el) {
		var day = el.getAttribute('data-day');
		el.addEventListener('pointerenter', function () { aim(day); });
		el.addEventListener('focusin', function () { aim(day); });
	}
	Array.prototype.forEach.call(document.querySelectorAll('.event[data-day]'), hook);
	stars.forEach(function (star) {
		hook(star);
		star.setAttribute('role', 'button');
		star.setAttribute('aria-pressed', star === aimed ? 'true' : 'false');
		star.setAttribute('aria-label', 'Aim the telescope at ' + star.getAttribute('aria-label') + '. Press again to go to the event.');
		star.addEventListener('click', function (event) {
			event.preventDefault();
			activate(star);
		});
		star.addEventListener('keydown', function (event) {
			if (event.key === ' ') {
				event.preventDefault();
				activate(star);
			}
		});
	});
})();
