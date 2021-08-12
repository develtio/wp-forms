import LiveFormValidation from 'live-form-validation';

window.LiveForm = LiveFormValidation.LiveForm;
window.Nette = LiveFormValidation.Nette;

(function () {
  window.Nette.init();
  window.LiveForm.setOptions({
    messageErrorPrefix: ''
  })
})();