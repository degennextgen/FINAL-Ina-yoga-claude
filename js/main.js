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
 * Testimonials-Slider (Schritt 19)
 * Auto-wechsel alle 4 Sekunden + Pfeil-Navigation
 */
document.addEventListener('DOMContentLoaded', function () {
  var track = document.querySelector('.testimonials__track');
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

  goTo(0);
  start();
});
