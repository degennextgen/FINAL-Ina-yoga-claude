/**
 * Lock hero height to initial viewport height.
 * Prevents resize when mobile browser chrome (address bar) shows/hides on scroll.
 */
(function () {
  document.documentElement.style.setProperty('--hero-height', window.innerHeight + 'px');
})();

/**
 * Scroll-to-top – sichtbar nach Scroll, smooth wie Anker-Navigation
 */
document.addEventListener('DOMContentLoaded', function () {
  var btn = document.querySelector('.scroll-top');
  if (!btn) return;

  var threshold = 300;

  function updateScrollTop() {
    var show = window.scrollY > threshold;
    btn.classList.toggle('scroll-top--visible', show);
    btn.setAttribute('aria-hidden', show ? 'false' : 'true');
    btn.tabIndex = show ? 0 : -1;
  }

  window.addEventListener('scroll', updateScrollTop, { passive: true });
  updateScrollTop();
});

/**
 * Burger-Menü Toggle (Schritt 5)
 */
document.addEventListener('DOMContentLoaded', function () {
  const burger = document.querySelector('.header__burger');
  const overlay = document.getElementById('nav-overlay');
  const body = document.body;

  if (!burger || !overlay) return;

  function closeMenu() {
    overlay.setAttribute('aria-hidden', 'true');
    burger.setAttribute('aria-expanded', 'false');
    burger.setAttribute('aria-label', 'Menü öffnen');
    burger.classList.remove('header__burger--open');
    overlay.classList.remove('nav-overlay--open');
    body.classList.remove('nav-open');
  }

  function openMenu() {
    overlay.setAttribute('aria-hidden', 'false');
    burger.setAttribute('aria-expanded', 'true');
    burger.setAttribute('aria-label', 'Menü schließen');
    burger.classList.add('header__burger--open');
    overlay.classList.add('nav-overlay--open');
    body.classList.add('nav-open');
  }

  burger.addEventListener('click', function () {
    if (body.classList.contains('modal-open')) {
      document.querySelectorAll('.modal--open').forEach(function (m) {
        m.classList.remove('modal--open');
        m.setAttribute('aria-hidden', 'true');
      });
      body.classList.remove('modal-open');
      burger.classList.remove('header__burger--open');
      burger.setAttribute('aria-label', 'Menü öffnen');
      return;
    }
    const isOpen = overlay.getAttribute('aria-hidden') === 'false';
    if (isOpen) {
      closeMenu();
    } else {
      openMenu();
    }
  });

  // Overlay schließen bei Klick auf Link
  overlay.querySelectorAll('a').forEach(function (link) {
    link.addEventListener('click', closeMenu);
  });
});

/**
 * Modal – Fullscreen Popup (Schritt 10)
 */
document.addEventListener('DOMContentLoaded', function () {
  const modalTriggers = document.querySelectorAll('[data-modal]');
  const modals = document.querySelectorAll('.modal');
  const body = document.body;
  const burger = document.querySelector('.header__burger');

  function setBurgerModalState(active) {
    if (!burger) return;
    if (active) {
      burger.classList.add('header__burger--open');
      burger.setAttribute('aria-label', 'Menü schließen');
    } else {
      // only remove open class if nav overlay is also not open
      const overlay = document.getElementById('nav-overlay');
      const navOpen = overlay && overlay.getAttribute('aria-hidden') === 'false';
      if (!navOpen) {
        burger.classList.remove('header__burger--open');
        burger.setAttribute('aria-label', 'Menü öffnen');
      }
    }
  }

  function openModal(id) {
    const modal = document.getElementById('modal-' + id);
    if (!modal) return;
    // close any currently open modal first
    modals.forEach(function (m) {
      m.classList.remove('modal--open');
      m.setAttribute('aria-hidden', 'true');
    });
    modal.classList.add('modal--open');
    modal.setAttribute('aria-hidden', 'false');
    body.classList.add('modal-open');
    setBurgerModalState(true);
  }

  function closeModal(modal) {
    if (!modal) return;
    modal.classList.remove('modal--open');
    modal.setAttribute('aria-hidden', 'true');
    body.classList.remove('modal-open');
    setBurgerModalState(false);
  }

  function closeAllModals() {
    modals.forEach(function (modal) {
      closeModal(modal);
    });
  }

  modalTriggers.forEach(function (trigger) {
    trigger.addEventListener('click', function (e) {
      if (trigger.tagName === 'A') e.preventDefault();
      const id = trigger.getAttribute('data-modal');
      if (id) openModal(id);
    });
  });

  document.querySelectorAll('[data-modal-close]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      const modal = btn.closest('.modal');
      if (modal) closeModal(modal);
    });
  });

  document.querySelectorAll('.modal__backdrop').forEach(function (backdrop) {
    backdrop.addEventListener('click', function () {
      closeModal(backdrop.closest('.modal'));
    });
  });

  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') closeAllModals();
  });

  modals.forEach(function (modal) {
    modal.querySelectorAll('a[href^="#"]').forEach(function (link) {
      link.addEventListener('click', function () {
        closeModal(modal);
      });
    });
  });
});


/**
 * Swipe-Hilfsfunktion – erkennt horizontales Wischen auf Touch-Geräten
 * @param {Element} el       – das Element, auf dem Swipes erkannt werden
 * @param {Function} onLeft  – Callback bei Wisch nach links  (→ weiter)
 * @param {Function} onRight – Callback bei Wisch nach rechts (→ zurück)
 */
function addSwipe(el, onLeft, onRight) {
  if (!el) return;
  var startX = 0;
  var startY = 0;
  el.addEventListener('touchstart', function (e) {
    startX = e.touches[0].clientX;
    startY = e.touches[0].clientY;
  }, { passive: true });
  el.addEventListener('touchend', function (e) {
    var dx = e.changedTouches[0].clientX - startX;
    var dy = e.changedTouches[0].clientY - startY;
    // Nur auslösen wenn horizontale Bewegung dominiert und Mindestweite 50px
    if (Math.abs(dx) < 50 || Math.abs(dx) < Math.abs(dy)) return;
    if (dx < 0) onLeft();
    else onRight();
  }, { passive: true });
}

/**
 * Testimonials-Slider (Schritt 19)
 * Auto-wechsel alle 4 Sekunden + Pfeil-Navigation + Swipe
 */
document.addEventListener('DOMContentLoaded', function () {
  var track = document.querySelector('.testimonials__track');
  var trackWrap = document.querySelector('.testimonials__track-wrap');
  var items = document.querySelectorAll('.testimonials__item');
  var dots = document.querySelectorAll('.testimonials__dot');
  var prevBtn = document.querySelector('.testimonials__nav--prev');
  var nextBtn = document.querySelector('.testimonials__nav--next');

  if (!track || !items.length) return;

  var current = 0;
  var total = items.length;
  var intervalId = null;

  function goTo(index) {
    current = ((index % total) + total) % total;
    track.style.transform = 'translateX(-' + (current * 25) + '%)';
    dots.forEach(function (dot, i) {
      dot.classList.toggle('testimonials__dot--active', i === current);
    });
  }

  function start() {
    intervalId = setInterval(function () {
      goTo(current + 1);
    }, 4000);
  }

  function restart() {
    clearInterval(intervalId);
    start();
  }

  if (prevBtn) {
    prevBtn.addEventListener('click', function () {
      goTo(current - 1);
      restart();
    });
  }

  if (nextBtn) {
    nextBtn.addEventListener('click', function () {
      goTo(current + 1);
      restart();
    });
  }

  dots.forEach(function (dot, i) {
    dot.addEventListener('click', function () {
      goTo(i);
      restart();
    });
  });

  // Swipe-Unterstützung
  addSwipe(trackWrap,
    function () { goTo(current + 1); restart(); },
    function () { goTo(current - 1); restart(); }
  );

  goTo(0);
  start();
});

/**
 * Generic Offer Slider factory (Sound Bath + Nuad Thai)
 */
function initOfferSlider(trackId, nextId, prevId, sectionId, reversed) {
  document.addEventListener('DOMContentLoaded', function () {
    var track   = document.getElementById(trackId);
    var slide2  = document.querySelector('#' + sectionId + ' .offer-slider__slide--2');
    var nextBtn = document.getElementById(nextId);
    var prevBtn = document.getElementById(prevId);
    if (!track || !nextBtn || !prevBtn) return;
    // reversed: Slide 2 ist links im DOM → start bei -100%, WAS DICH ERWARTET → 0%
    if (reversed) track.style.transform = 'translateX(-100%)';
    function show(open) {
      track.style.transform = reversed
        ? 'translateX(' + (open ? 0 : -100) + '%)'
        : 'translateX(-' + (open ? 100 : 0) + '%)';
      slide2.setAttribute('aria-hidden', open ? 'false' : 'true');
    }
    nextBtn.addEventListener('click', function () { show(true); });
    prevBtn.addEventListener('click', function () { show(false); });

    // Swipe-Unterstützung: Links = weiter, Rechts = zurück
    var section = document.getElementById(sectionId);
    addSwipe(section,
      function () { show(true); },
      function () { show(false); }
    );
  });
}
initOfferSlider('sb-track', 'sb-next', 'sb-prev', 'sound-bath', true);
initOfferSlider('nt-track', 'nt-next', 'nt-prev', 'nuad-thai', false);

/**
 * Yoga Intro Slider
 */
document.addEventListener('DOMContentLoaded', function () {
  var track = document.querySelector('.yoga-intro__track');
  var slide2 = document.querySelector('.yoga-intro__slide--2');
  var nextBtn = document.getElementById('yoga-intro-next');
  var prevBtn = document.getElementById('yoga-intro-prev');

  if (!track || !nextBtn || !prevBtn) return;

  function showSlide(index) {
    track.style.transform = 'translateX(-' + (index * 100) + '%)';
    slide2.setAttribute('aria-hidden', index === 0 ? 'true' : 'false');
  }

  nextBtn.addEventListener('click', function () {
    if (window.matchMedia('(min-width: 1024px)').matches) {
      // Desktop: Modal öffnen statt zu Slide 2 zu wechseln
      var yogaModal = document.getElementById('modal-yoga-was-dich-erwartet');
      if (yogaModal) {
        document.querySelectorAll('.modal').forEach(function (m) {
          m.classList.remove('modal--open');
          m.setAttribute('aria-hidden', 'true');
        });
        yogaModal.classList.add('modal--open');
        yogaModal.setAttribute('aria-hidden', 'false');
        document.body.classList.add('modal-open');
        var burgerEl = document.querySelector('.header__burger');
        if (burgerEl) {
          burgerEl.classList.add('header__burger--open');
          burgerEl.setAttribute('aria-label', 'Menü schließen');
        }
      }
    } else {
      showSlide(1);
    }
  });

  prevBtn.addEventListener('click', function () {
    showSlide(0);
  });

  // Swipe-Unterstützung: Links = weiter (Slide 2 / Modal), Rechts = zurück
  var yogaSection = document.querySelector('.yoga-intro');
  addSwipe(yogaSection,
    function () { nextBtn.click(); },
    function () { showSlide(0); }
  );
});
