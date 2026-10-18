/* The logo cardinal holds a pose, then snaps its head to the next one as the
   reader scrolls, the way birds look around. Scroll position picks the pose;
   style.css springs the head into it. Nothing moves under reduced motion. */
(function () {
	var motion = window.matchMedia('(prefers-reduced-motion: no-preference)');

	// Pivots, as offsets in drawing units from the head's transform-origin at the
	// back of the neck. Looking up turns on the throat, so the black bib stays on
	// the chest; looking down turns on the nape, so the crest doesn't hunch.
	var NAPE = [0, 0];
	var THROAT = [18, 48];

	// Poses by the bird's place in the viewport: 1 is the bottom edge, 0 the top.
	// turn is in degrees; shift nudges the head forward, in drawing units.
	var POSES = {
		perch: [
			{ from: 0.7, turn: -10, pivot: THROAT, shift: [0, 0] }, // comes into view looking up at the reader
			{ from: 0.48, turn: 1, pivot: NAPE, shift: [0, 0] },
			{ from: 0.3, turn: -4, pivot: THROAT, shift: [0, 0] }, // a glance back up
			{ from: -Infinity, turn: 11, pivot: NAPE, shift: [4, -2] } // looks down at the research below
		],
		end: [
			{ from: 0.8, turn: 16, pivot: NAPE, shift: [5, -2] }, // head down until the end of the text is in view
			{ from: -Infinity, turn: 0, pivot: NAPE, shift: [0, 0] }
		]
	};
	// How far past a boundary the bird must be before it turns, so it never jitters.
	var MARGIN = 0.03;

	var birds = Array.prototype.map.call(document.querySelectorAll('.division__bird, .end-mark'), function (holder) {
		return {
			head: holder.querySelector('.cardinal__head'),
			tail: holder.querySelector('.cardinal__tail'),
			holder: holder,
			poses: POSES[holder.classList.contains('end-mark') ? 'end' : 'perch'],
			pose: -1
		};
	}).filter(function (bird) { return bird.head; });
	if (!birds.length) {
		return;
	}

	function poseAt(poses, position) {
		for (var i = 0; i < poses.length; i++) {
			if (position > poses[i].from) {
				return i;
			}
		}
		return poses.length - 1;
	}

	function settle(bird, position) {
		var next = poseAt(bird.poses, position);
		if (next === bird.pose) {
			return;
		}
		if (bird.pose >= 0 && Math.abs(position - bird.poses[Math.min(next, bird.pose)].from) < MARGIN) {
			return;
		}
		var pose = bird.poses[next];
		var dx = pose.pivot[0];
		var dy = pose.pivot[1];
		// Every pose uses the same three functions, so the spring blends them smoothly.
		bird.head.style.transform = 'translate(' + (pose.shift[0] + dx) + 'px, ' + (pose.shift[1] + dy) + 'px) ' +
			'rotate(' + pose.turn + 'deg) translate(' + -dx + 'px, ' + -dy + 'px)';
		if (bird.pose >= 0 && bird.tail && bird.tail.animate) {
			bird.tail.animate(
				[{ transform: 'rotate(0deg)' }, { transform: 'rotate(-7deg)', offset: 0.35 }, { transform: 'rotate(0deg)' }],
				{ duration: 420, easing: 'ease-out' }
			);
		}
		bird.pose = next;
	}

	function update() {
		pending = false;
		birds.forEach(function (bird) {
			if (!motion.matches) {
				bird.head.style.transform = '';
				bird.pose = -1;
				return;
			}
			var box = bird.holder.getBoundingClientRect();
			var position = (box.top + box.height / 2) / window.innerHeight;
			if (position > -0.1 && position < 1.1) {
				settle(bird, position);
			}
		});
	}

	var pending = false;
	function request() {
		if (!pending) {
			pending = true;
			window.requestAnimationFrame(update);
		}
	}
	window.addEventListener('scroll', request, { passive: true });
	window.addEventListener('resize', request);
	if (motion.addEventListener) {
		motion.addEventListener('change', request);
	}
	request();
})();
