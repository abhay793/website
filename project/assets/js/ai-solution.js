/**
 * ai-solution.js
 * Drives the AI Solution chat widget: sending messages, rendering the
 * typing animation, auto-scrolling, timestamps, and suggestion buttons.
 * Talks to client/ai-response.php, which is structured so a real model
 * (e.g. the OpenAI API) can be dropped in later without changing this file.
 */
(function () {
  'use strict';

  var chatWindow = document.getElementById('chatWindow');
  var chatForm = document.getElementById('chatForm');
  var chatInput = document.getElementById('chatInput');
  var sendBtn = document.getElementById('chatSendBtn');
  var suggestions = document.getElementById('chatSuggestions');
  var csrfToken = document.querySelector('meta[name="csrf-token"]');
  csrfToken = csrfToken ? csrfToken.getAttribute('content') : '';

  function formatTime(date) {
    return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
  }

  function stampAllTimestamps() {
    document.querySelectorAll('[data-time]').forEach(function (el) {
      if (!el.textContent) el.textContent = formatTime(new Date());
    });
  }

  function scrollToBottom() {
    chatWindow.scrollTop = chatWindow.scrollHeight;
  }

  function appendMessage(role, text) {
    var wrapper = document.createElement('div');
    wrapper.className = 'chat-msg ' + role;

    var avatar = document.createElement('div');
    avatar.className = 'avatar';
    avatar.textContent = role === 'user' ? 'You' : 'AI';

    var body = document.createElement('div');

    var bubble = document.createElement('div');
    bubble.className = 'chat-bubble';
    bubble.textContent = text;

    var timestamp = document.createElement('span');
    timestamp.className = 'chat-timestamp';
    timestamp.textContent = formatTime(new Date());

    body.appendChild(bubble);
    body.appendChild(timestamp);
    wrapper.appendChild(avatar);
    wrapper.appendChild(body);
    chatWindow.appendChild(wrapper);
    scrollToBottom();
    return wrapper;
  }

  function appendTypingIndicator() {
    var wrapper = document.createElement('div');
    wrapper.className = 'chat-msg bot';
    wrapper.id = 'typingIndicatorMsg';

    var avatar = document.createElement('div');
    avatar.className = 'avatar';
    avatar.textContent = 'AI';

    var bubble = document.createElement('div');
    bubble.className = 'chat-bubble';
    bubble.innerHTML = '<div class="typing-indicator"><span></span><span></span><span></span></div>';

    wrapper.appendChild(avatar);
    wrapper.appendChild(bubble);
    chatWindow.appendChild(wrapper);
    scrollToBottom();
  }

  function removeTypingIndicator() {
    var el = document.getElementById('typingIndicatorMsg');
    if (el) el.remove();
  }

  function sendMessage(text) {
    if (!text.trim()) return;

    appendMessage('user', text.trim());
    chatInput.value = '';
    sendBtn.disabled = true;
    appendTypingIndicator();

    fetch('/client/ai-response.php', {
      method: 'POST',
      credentials: 'same-origin',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-Token': csrfToken,
      },
      body: JSON.stringify({ message: text.trim() }),
    })
      .then(function (res) {
        if (res.status === 401) {
          window.location.href = '/client/login.php?reason=timeout';
          throw new Error('Session expired');
        }
        return res.json();
      })
      .then(function (data) {
        removeTypingIndicator();
        if (data.error) {
          appendMessage('bot', 'Sorry — ' + data.error);
        } else {
          appendMessage('bot', data.reply);
        }
      })
      .catch(function () {
        removeTypingIndicator();
        appendMessage('bot', 'Something went wrong reaching the assistant. Please try again.');
      })
      .finally(function () {
        sendBtn.disabled = false;
        chatInput.focus();
      });
  }

  document.addEventListener('DOMContentLoaded', function () {
    stampAllTimestamps();
    scrollToBottom();

    chatForm.addEventListener('submit', function (e) {
      e.preventDefault();
      sendMessage(chatInput.value);
    });

    suggestions.querySelectorAll('.chat-suggestion-btn').forEach(function (btn) {
      btn.addEventListener('click', function () {
        sendMessage(btn.textContent);
      });
    });
  });
})();
