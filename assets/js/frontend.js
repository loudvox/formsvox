document.addEventListener('DOMContentLoaded', function () {
  // AJAX Form Handler
  document.querySelectorAll('.formvox-ajax-form').forEach(function (form) {
    form.addEventListener('submit', function (e) {
      e.preventDefault();

      var responseMsg = form.querySelector('.formvox-response-message');
      var submitBtn = form.querySelector('.formvox-submit-btn');
      var formData = new FormData(form);

      if (submitBtn) submitBtn.disabled = true;
      if (responseMsg) {
        responseMsg.style.display = 'block';
        responseMsg.innerHTML = 'Submitting...';
        responseMsg.className = 'formvox-response-message info';
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
            var msg = (data.confirmations && data.confirmations[0] && data.confirmations[0].message) || 'Thank you! Form submitted successfully.';
            responseMsg.className = 'formvox-response-message success';
            responseMsg.innerHTML = msg;
          } else {
            responseMsg.className = 'formvox-response-message error';
            responseMsg.innerHTML = data.message || 'An error occurred. Please try again.';
          }
        })
        .catch(function () {
          if (submitBtn) submitBtn.disabled = false;
          responseMsg.className = 'formvox-response-message error';
          responseMsg.innerHTML = 'Submission failed. Please check your network.';
        });
    });
  });

  // Dynamic Repeater Rows
  document.addEventListener('click', function (e) {
    if (e.target.classList.contains('formvox-btn-add-row')) {
      var container = e.target.previousElementSibling;
      if (container && container.firstElementChild) {
        var clone = container.firstElementChild.cloneNode(true);
        clone.querySelectorAll('input').forEach(function (inp) {
          inp.value = '';
        });
        container.appendChild(clone);
      }
    }
    if (e.target.classList.contains('formvox-btn-remove-row')) {
      var row = e.target.closest('.formvox-repeater-row');
      if (row && row.parentNode.children.length > 1) {
        row.remove();
      }
    }
  });
});
