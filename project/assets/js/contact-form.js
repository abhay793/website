/**
 * contact-form.js
 * Client-side convenience validation for the contact form. This is purely
 * a UX enhancement — all authoritative validation happens server-side in
 * contact.php, since client-side checks can always be bypassed.
 */
(function () {
  'use strict';

  document.addEventListener('DOMContentLoaded', function () {
    var form = document.getElementById('contactForm');
    if (!form) return;

    form.addEventListener('submit', function (e) {
      var name = form.querySelector('#name');
      var email = form.querySelector('#email');
      var message = form.querySelector('#message');
      var invalid = [];

      [name, email, message].forEach(function (field) {
        field.classList.remove('is-invalid');
      });

      if (!name.value.trim()) invalid.push(name);
      if (!email.value.trim() || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value.trim())) invalid.push(email);
      if (!message.value.trim()) invalid.push(message);

      if (invalid.length) {
        e.preventDefault();
        invalid.forEach(function (field) { field.classList.add('is-invalid'); });
        invalid[0].focus();
      }
    });
  });
})();
