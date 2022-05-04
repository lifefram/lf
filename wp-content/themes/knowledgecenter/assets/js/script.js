'use strict';

document.addEventListener('DOMContentLoaded', () => {
  // Toggles
  const $burgers = document.querySelectorAll('.burger');

  if ($burgers.length > 0) {
    $burgers.forEach(function($el) {
      $el.addEventListener('click', function() {
        let target = $el.dataset.target;
        let $target = document.getElementById(target);
        $el.classList.toggle('is-active');
        if ($el.querySelector('.navbar-burger')) {
          $el.querySelector('.navbar-burger').classList.toggle('is-active');
        }
        $target.classList.toggle('is-active');
        if ($el.classList.contains('is-active')) {
          $el.setAttribute('aria-expanded', 'true');
        } else {
          $el.setAttribute('aria-expanded', 'false');
        }
      });
    });
  }
});
