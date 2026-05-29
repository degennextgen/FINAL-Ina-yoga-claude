/**
 * Kooperationen: Sektion (mit Logo) aus api/partners.php
 */
(function () {
  'use strict';

  var ERROR_MSG = 'Kooperationen konnten nicht geladen werden. Bitte später erneut versuchen.';

  function renderSection(partners, root) {
    if (!root) return;
    root.innerHTML = '';
    if (!partners.length) {
      return;
    }
    partners.forEach(function (p) {
      var li = document.createElement('li');
      li.className = 'partners__item';

      var prefix = document.createElement('span');
      prefix.className = 'partners__prefix';
      prefix.textContent = (p.prefix || '').trim();
      li.appendChild(prefix);

      var url = (p.url || '').trim();
      var nameEl;
      if (url) {
        nameEl = document.createElement('a');
        nameEl.className = 'partners__name';
        nameEl.href = url;
        nameEl.target = '_blank';
        nameEl.rel = 'noopener noreferrer';
        nameEl.textContent = p.name || '';
      } else {
        nameEl = document.createElement('span');
        nameEl.className = 'partners__name';
        nameEl.textContent = p.name || '';
      }
      li.appendChild(nameEl);

      var logo = (p.logo || '').trim();
      if (logo) {
        var img = document.createElement('img');
        img.className = 'partners__logo';
        img.src = logo;
        img.alt = p.name || '';
        img.loading = 'lazy';
        img.decoding = 'async';
        li.appendChild(img);
      }

      root.appendChild(li);
    });
  }

  function showError(sectionRoot) {
    if (sectionRoot) {
      sectionRoot.innerHTML = '';
      var err = document.createElement('p');
      err.className = 'partners__error';
      err.textContent = ERROR_MSG;
      sectionRoot.appendChild(err);
    }
  }

  var sectionRoot = document.getElementById('partners-section-root');
  if (!sectionRoot) {
    return;
  }

  fetch('api/partners.php', { credentials: 'same-origin' })
    .then(function (res) {
      if (!res.ok) {
        throw new Error('HTTP ' + res.status);
      }
      return res.json();
    })
    .then(function (data) {
      if (data.error) {
        throw new Error(data.error);
      }
      var partners = data.partners || [];
      renderSection(partners, sectionRoot);
    })
    .catch(function () {
      showError(sectionRoot);
    });
})();
