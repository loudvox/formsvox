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

  document.querySelectorAll('.formsvox-form').forEach(function (form) {
    evaluateConditionalLogic(form);
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
        .then(function (res) {
          return res.json();
        })
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

  // Dynamic Repeater Rows
  document.addEventListener('click', function (e) {
    if (e.target.classList.contains('formsvox-btn-add-row')) {
      var container = e.target.previousElementSibling;
      if (container && container.firstElementChild) {
        var clone = container.firstElementChild.cloneNode(true);
        clone.querySelectorAll('input').forEach(function (inp) {
          inp.value = '';
        });
        container.appendChild(clone);
      }
    }
    if (e.target.classList.contains('formsvox-btn-remove-row')) {
      var row = e.target.closest('.formsvox-repeater-row');
      if (row && row.parentNode.children.length > 1) {
        row.remove();
      }
    }
  });
});
