'use strict';

document.addEventListener('DOMContentLoaded', () => {

  // const navbarBurger = document.getElementById('navigation-burger');
  const navbarBurger = document.querySelectorAll('.navbar-burger');

  if (navbarBurger) {
    navbarBurger.forEach( (menu) => {
      menu.addEventListener('click', () => {
        toggleMobileMenu(menu);
      });
    });

    function toggleMobileMenu(el) {
      const target = el.dataset.target;
      const $target = document.getElementById(target);
      el.classList.toggle('is-active');
      $target.classList.toggle('is-active');
      if (el.classList.contains('is-active')) {
        el.setAttribute('aria-expanded', 'true');
      } else {
        el.setAttribute('aria-expanded', 'false');
      }
    }
  }

  // Panel Tabs
  const $panelTabs = document.querySelectorAll('.panel-tabs a');

  if ($panelTabs.length > 0) {
    $panelTabs.forEach((tab) => {
      tab.addEventListener('click', (e) => {
        const selected = tab.getAttribute('data-tab');
        const parent = tab.closest('.panel');
        const panels = parent.querySelectorAll('.panel-tabs a');
        const tabContents = parent.querySelectorAll('.tabs-content');
        panels.forEach(panel => panel.classList.remove('is-active'));
        tab.classList.add('is-active');
        tabContents.forEach(tabcontent => {
          tabcontent.classList.add('is-hidden');
          const data = tabcontent.getAttribute('data-content');
          if (data === selected) {
            tabcontent.classList.remove('is-hidden');
          }
        });
      });
    });
  }

  const commentNumber = document.querySelector('.comment-number');

  if (commentNumber) {
    const cancelCommentReply = document.querySelector('#cancel-comment-reply-link');
    cancelCommentReply.addEventListener('click', () => {
      commentNumber.classList.remove('is-hidden');
    });

    const commentReply = document.querySelectorAll('.comment-reply-link');
    commentReply.forEach(btn => {
      btn.addEventListener('click', () => {
        commentNumber.classList.add('is-hidden');
      });
    });
  }

});

