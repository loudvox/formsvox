document.addEventListener('DOMContentLoaded', function () {
  // Evaluates conditional logic rules on front-end input change
  function evaluateConditionalLogic(form) {
    var fieldWrappers = form.querySelectorAll('[data-conditional-logic]');
    var formValues = {};

    // Gather all current form input values
    form.querySelectorAll('[name^="formsvox_fields"]').forEach(function (input) {
      var match = input.name.match(/formsvox_fields\[([^\]]+)\]/);
      if (!match) return;
      var fieldId = match[1];

      if (input.type === 'checkbox') {
        if (!formValues[fieldId]) formValues[fieldId] = [];
        if (input.checked) formValues[fieldId].push(input.value);
      } else if (input.type === 'radio') {
        if (input.checked) formValues[fieldId] = input.value;
      } else {
        formValues[fieldId] = input.value;
      }
    });

    fieldWrappers.forEach(function (wrapper) {
      var rawLogic = wrapper.getAttribute('data-conditional-logic');
      if (!rawLogic) return;

      try {
        var logic = JSON.parse(rawLogic);
        if (!logic.enabled || !logic.rules || !logic.rules.length) return;

        var matchAll = logic.match === 'all';
        var action = logic.action || 'show';
        var results = [];

        logic.rules.forEach(function (rule) {
          var targetVal = rule.value;
          var actualVal = formValues[rule.field_id];
          var isMatch = false;

          if (Array.isArray(actualVal)) {
            if (rule.operator === 'equals' || rule.operator === 'contains') {
              isMatch = actualVal.includes(targetVal);
            } else if (rule.operator === 'not_equals') {
              isMatch = !actualVal.includes(targetVal);
            }
          } else {
            var actualStr = actualVal !== undefined && actualVal !== null ? String(actualVal) : '';
            switch (rule.operator) {
              case 'equals':
                isMatch = actualStr === String(targetVal);
                break;
              case 'not_equals':
                isMatch = actualStr !== String(targetVal);
                break;
              case 'contains':
                isMatch = actualStr.includes(String(targetVal));
                break;
              case 'greater_than':
                isMatch = !isNaN(actualStr) && Number(actualStr) > Number(targetVal);
                break;
              case 'less_than':
                isMatch = !isNaN(actualStr) && Number(actualStr) < Number(targetVal);
                break;
              case 'empty':
                isMatch = actualStr === '';
                break;
              case 'not_empty':
                isMatch = actualStr !== '';
                break;
            }
          }
          results.push(isMatch);
        });

        var passed = matchAll ? !results.includes(false) : results.includes(true);
        var visible = action === 'show' ? passed : !passed;

        wrapper.style.display = visible ? '' : 'none';
      } catch (err) {
        console.error('FormsVox conditional logic error:', err);
      }
    });
  }

  // VoiceCore Conversational AI Chat Widget Initialization
  function initConversationalWidget(form) {
    if (!form.getAttribute('data-ai-mode')) return;

    var formId = form.getAttribute('data-form-id');
    var wrapper = form.closest('.formsvox-form-wrapper');
    if (!wrapper) return;

    // Create Chat Widget Container
    var chatBox = document.createElement('div');
    chatBox.className = 'formsvox-ai-chat-box';
    chatBox.innerHTML =
      '<div class="formsvox-ai-header"><span>FormsVox Assistant — Powered by VoiceCore AI</span></div>' +
      '<div class="formsvox-ai-messages" role="log" aria-live="polite"></div>' +
      '<div class="formsvox-ai-input-wrap">' +
      '<input type="text" class="formsvox-ai-input" placeholder="Type your response..." aria-label="Type message" />' +
      '<button type="button" class="formsvox-ai-send-btn">Send</button>' +
      '</div>' +
      '<div class="formsvox-ai-disclosure">AI Assistant — VoiceCore Data Privacy Disclosed</div>';

    wrapper.appendChild(chatBox);

    var messagesDiv = chatBox.querySelector('.formsvox-ai-messages');
    var inputField = chatBox.querySelector('.formsvox-ai-input');
    var sendBtn = chatBox.querySelector('.formsvox-ai-send-btn');
    var conversation = [];

    function addMessage(role, text) {
      var msgDiv = document.createElement('div');
      msgDiv.className = 'formsvox-ai-msg ' + role;
      msgDiv.textContent = text;
      messagesDiv.appendChild(msgDiv);
      messagesDiv.scrollTop = messagesDiv.scrollHeight;
      conversation.push({ role: role, content: text });
    }

    addMessage('assistant', 'Hello! I can help you complete this form conversationally. What is your name?');

    function handleSend() {
      var text = inputField.value.trim();
      if (!text) return;
      inputField.value = '';
      addMessage('user', text);

      var relayUrl = (window.formsvoxFrontend ? window.formsvoxFrontend.restUrl : '/wp-json/formsvox/v1') + '/ai/chat';
      fetch(relayUrl, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          form_id: Number(formId),
          messages: conversation,
        }),
      })
        .then(function (res) { return res.json(); })
        .then(function (data) {
          if (data && data.text) {
            addMessage('assistant', data.text);
          } else {
            addMessage('assistant', 'Thank you! I have saved your response.');
          }
        })
        .catch(function () {
          addMessage('assistant', 'I encountered a temporary connection issue. Please use the form fields below.');
        });
    }

    sendBtn.addEventListener('click', handleSend);
    inputField.addEventListener('keypress', function (e) {
      if (e.key === 'Enter') handleSend();
    });
  }

  document.querySelectorAll('.formsvox-form').forEach(function (form) {
    evaluateConditionalLogic(form);
    initConversationalWidget(form);

    form.addEventListener('input', function () {
      evaluateConditionalLogic(form);
    });
    form.addEventListener('change', function () {
      evaluateConditionalLogic(form);
    });

    // AJAX Form Handler
    form.addEventListener('submit', function (e) {
      if (!form.classList.contains('formsvox-ajax-form')) return;
      e.preventDefault();

      var responseMsg = form.querySelector('.formsvox-response-message');
      var submitBtn = form.querySelector('.formsvox-submit-btn');
      var formData = new FormData(form);

      if (submitBtn) submitBtn.disabled = true;
      if (responseMsg) {
        responseMsg.style.display = 'block';
        responseMsg.innerHTML = 'Submitting...';
        responseMsg.className = 'formsvox-response-message info';
      }

      fetch(form.action, {
        method: 'POST',
        body: formData,
      })
        .then(function (res) { return res.json(); })
        .then(function (data) {
          if (submitBtn) submitBtn.disabled = false;
          if (data.success) {
            form.reset();
            evaluateConditionalLogic(form);
            var msg = (data.confirmations && data.confirmations[0] && data.confirmations[0].message) || 'Thank you! Form submitted successfully.';
            responseMsg.className = 'formsvox-response-message success';
            responseMsg.innerHTML = msg;

            if (data.confirmations && data.confirmations[0] && data.confirmations[0].type === 'redirect' && data.confirmations[0].redirect_url) {
              window.location.href = data.confirmations[0].redirect_url;
            }
          } else {
            responseMsg.className = 'formsvox-response-message error';
            responseMsg.innerHTML = data.message || (data.errors ? Object.values(data.errors).join('<br>') : 'An error occurred. Please try again.');
          }
        })
        .catch(function () {
          if (submitBtn) submitBtn.disabled = false;
          responseMsg.className = 'formsvox-response-message error';
          responseMsg.innerHTML = 'Submission failed. Please check your network.';
        });
    });
  });
});
