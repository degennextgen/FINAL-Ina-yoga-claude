/**
 * Startseite: Events + Preise aus einer Quelle (api/site.php).
 */
(function () {
  'use strict';

  var eventsCache = [];

  function escapeHtml(s) {
    if (s == null) return '';
    var d = document.createElement('div');
    d.textContent = String(s);
    return d.innerHTML;
  }

  function pad2(n) {
    return n < 10 ? '0' + n : String(n);
  }

  function formatIsoToDe(iso) {
    if (!iso || typeof iso !== 'string') return '';
    var p = iso.split('-');
    if (p.length !== 3) return iso;
    return pad2(parseInt(p[2], 10)) + '.' + pad2(parseInt(p[1], 10)) + '.' + p[0];
  }

  function formatDateLabel(ev) {
    var ds = (ev.date_start || '').trim();
    if (!ds) return 'Termin auf Anfrage';
    var startDe = formatIsoToDe(ds);
    var de = (ev.date_end || '').trim();
    if (de && de !== ds) {
      return startDe + ' – ' + formatIsoToDe(de);
    }
    return startDe;
  }

  function formatMetaLine(ev) {
    var parts = [];
    parts.push(formatDateLabel(ev));
    var t = (ev.time || '').trim();
    if (t) parts.push(t);
    var loc = (ev.location || '').trim().replace(/\n+/g, ', ');
    if (loc) parts.push(loc);
    return parts.join(' · ');
  }

  function openEventDetailModal(modal) {
    var modals = document.querySelectorAll('.modal');
    var body = document.body;
    var burger = document.querySelector('.header__burger');

    modals.forEach(function (m) {
      m.classList.remove('modal--open');
      m.setAttribute('aria-hidden', 'true');
    });
    modal.classList.add('modal--open');
    modal.setAttribute('aria-hidden', 'false');
    body.classList.add('modal-open');

    if (burger) {
      burger.classList.add('header__burger--open');
      burger.setAttribute('aria-label', 'Menü schließen');
    }
  }

  function renderCards(grid, events) {
    grid.innerHTML = '';

    if (!events.length) {
      var empty = document.createElement('p');
      empty.className = 'events__empty';
      empty.textContent = 'Aktuell sind keine Events eingetragen.';
      grid.appendChild(empty);
      return;
    }

    events.forEach(function (ev) {
      var article = document.createElement('article');
      article.className = 'events__card';

      var wrap = document.createElement('div');
      wrap.className = 'events__card-image-wrap';
      var img = document.createElement('img');
      img.src = ev.image || '';
      img.alt = ev.title || '';
      wrap.appendChild(img);

      var cardBody = document.createElement('div');
      cardBody.className = 'events__card-body';

      var h3 = document.createElement('h3');
      h3.className = 'events__card-title';
      h3.textContent = ev.title || '';

      cardBody.appendChild(h3);

      var dateLine = document.createElement('p');
      dateLine.className = 'events__card-meta';
      dateLine.textContent = formatDateLabel(ev);
      cardBody.appendChild(dateLine);

      var timeStr = (ev.time || '').trim();
      if (timeStr) {
        var timeLine = document.createElement('p');
        timeLine.className = 'events__card-meta';
        timeLine.textContent = timeStr;
        cardBody.appendChild(timeLine);
      }

      var locStr = (ev.location || '').trim();
      if (locStr) {
        locStr.split('\n').forEach(function (line) {
          line = line.trim();
          if (!line) return;
          var p = document.createElement('p');
          p.className = 'events__card-meta';
          p.textContent = line;
          cardBody.appendChild(p);
        });
      }

      var ex = (ev.excerpt || '').trim();
      if (ex) {
        var exP = document.createElement('p');
        exP.className = 'events__card-meta';
        exP.textContent = ex;
        cardBody.appendChild(exP);
      }

      var btn = document.createElement('button');
      btn.type = 'button';
      btn.className = 'events__card-btn btn-img';
      btn.setAttribute('data-open-event', ev.id || '');
      var btnImg = document.createElement('img');
      btnImg.src = 'Buttons/Button_Violet_Images_Ina.png';
      btnImg.alt = '';
      var span = document.createElement('span');
      span.textContent = 'MEHR DAZU';
      btn.appendChild(btnImg);
      btn.appendChild(span);
      cardBody.appendChild(btn);

      article.appendChild(wrap);
      article.appendChild(cardBody);
      grid.appendChild(article);
    });
  }

  function fillModal(modal, ev) {
    var img = modal.querySelector('.modal__image');
    var titleEl = modal.querySelector('#modal-event-detail-title');
    var descEl = modal.querySelector('#modal-event-detail-desc');
    var metaEl = modal.querySelector('#modal-event-detail-meta');

    if (img) {
      img.src = ev.image || '';
      img.alt = ev.title || '';
    }
    if (titleEl) titleEl.textContent = ev.title || '';

    if (descEl) {
      descEl.innerHTML = '';
      var p = document.createElement('p');
      p.className = 'modal__text';
      var bodyText = (ev.body || '').trim();
      p.innerHTML = escapeHtml(bodyText).replace(/\n/g, '<br>');
      descEl.appendChild(p);
    }

    if (metaEl) metaEl.textContent = formatMetaLine(ev);
  }

  /**
   * @param {string} rootId
   * @param {{ rows?: Array<{label: string, value: string}>, footnote?: string }} block
   */
  function renderYogaPricing(rootId, block) {
    var el = document.getElementById(rootId);
    if (!el || !block || !block.rows) return;
    el.innerHTML = '';
    block.rows.forEach(function (row) {
      var p = document.createElement('p');
      p.className = 'yoga-courses__price-item';
      p.innerHTML =
        '<span class="yoga-courses__price-label">' +
        escapeHtml(row.label) +
        '</span> <span class="yoga-courses__price-value">' +
        escapeHtml(row.value) +
        '</span>';
      el.appendChild(p);
    });
    var foot = document.getElementById('pricing-yoga-footnote');
    if (foot) {
      foot.textContent = (block.footnote || '').trim();
    }
  }

  /**
   * @param {string} rootId
   * @param {{ intro?: string, rows?: Array<{label: string, value: string}> }} block
   */
  function renderSliderPricing(rootId, block) {
    var el = document.getElementById(rootId);
    if (!el || !block) return;
    el.innerHTML = '';
    var wrap = document.createElement('div');
    wrap.className = 'offer-slider__text';
    var intro = (block.intro || '').trim();
    if (intro) {
      wrap.appendChild(document.createTextNode(intro + ' '));
    }
    var ul = document.createElement('ul');
    (block.rows || []).forEach(function (row) {
      var li = document.createElement('li');
      var lab = (row.label || '').trim();
      var val = (row.value || '').trim();
      li.textContent = lab + (lab && val ? ': ' : '') + val;
      ul.appendChild(li);
    });
    wrap.appendChild(ul);
    el.appendChild(wrap);
  }

  document.addEventListener('DOMContentLoaded', function () {
    var grid = document.getElementById('events-grid-root');
    var modal = document.getElementById('modal-event-detail');

    fetch('api/site.php', { credentials: 'same-origin' })
      .then(function (r) {
        if (!r.ok) throw new Error('HTTP ' + r.status);
        return r.json();
      })
      .then(function (data) {
        var pricing = data.pricing || {};

        if (grid) {
          eventsCache = data.events || [];
          renderCards(grid, eventsCache);
          grid.addEventListener('click', function (e) {
            if (!modal) return;
            var btn = e.target.closest('[data-open-event]');
            if (!btn) return;
            var id = btn.getAttribute('data-open-event');
            var ev = null;
            for (var i = 0; i < eventsCache.length; i++) {
              if (eventsCache[i].id === id) {
                ev = eventsCache[i];
                break;
              }
            }
            if (!ev) return;
            fillModal(modal, ev);
            openEventDetailModal(modal);
          });
        }

        renderYogaPricing('pricing-yoga-root', pricing.yoga_courses);
        renderSliderPricing('pricing-soft-touch-root', pricing.soft_touch);
        renderSliderPricing('pricing-nuad-slider-root', pricing.nuad_thai_slider);
      })
      .catch(function () {
        if (grid) {
          grid.innerHTML =
            '<p class="events__empty events__empty--error">Inhalte konnten nicht geladen werden. Bitte später erneut versuchen.</p>';
        }
      });
  });
})();
