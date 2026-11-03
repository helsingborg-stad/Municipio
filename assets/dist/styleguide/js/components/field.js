var __defProp = Object.defineProperty;
var __name = (target, value) => __defProp(target, "name", { value, configurable: true });
const _Checkbox = class _Checkbox {
  constructor(checkboxGroups) {
    checkboxGroups && this.setListener(checkboxGroups);
  }
  setListener(checkboxGroups) {
    checkboxGroups.forEach((checkboxGroup) => {
      const checkboxes = checkboxGroup.querySelectorAll(".c-option__checkbox--hidden-box");
      const validationElement = checkboxGroup.querySelector(".js-checkbox-valid");
      checkboxes.forEach((checkbox) => {
        checkbox.addEventListener("change", () => {
          const validator = checkboxGroup.querySelectorAll('.c-option [type="checkbox"]:checked');
          if (validator.length > 0) {
            validationElement.setAttribute("checked", true);
            checkboxGroup.querySelector(".c-field__label").classList.remove("u-color__text--danger");
          } else {
            validationElement.removeAttribute("checked");
          }
        });
      });
    });
  }
  validateCheckboxes(checkboxGroups) {
    const hasChecked = [];
    checkboxGroups.forEach((group) => {
      const input = group.querySelector("[data-js-required]");
      let validation = input.getAttribute("checked") ? true : false;
      if (input.hasAttribute("js-no-validation")) {
        validation = true;
      }
      hasChecked.push(validation);
      if (!validation) {
        group.querySelector(".c-field__label").classList.add("u-color__text--danger");
      } else {
        group.querySelector(".c-field__label").classList.remove("u-color__text--danger");
      }
    });
    return hasChecked.includes(false) ? false : true;
  }
};
__name(_Checkbox, "Checkbox");
let Checkbox = _Checkbox;
const _Collapse = class _Collapse {
  constructor(form) {
    if (!form) return;
    this.collapseSections = form.querySelectorAll(".mod-form-collapse");
    this.setListener();
  }
  setListener() {
    [...this.collapseSections].forEach((collapseButton) => {
      this.collapse(collapseButton);
      collapseButton.addEventListener("click", () => {
        this.collapse(collapseButton);
      });
    });
  }
  collapse(collapseButton = false) {
    let element = collapseButton.nextElementSibling;
    do {
      element.classList.toggle("u-display--none");
      element = element.nextElementSibling ? element.nextElementSibling : false;
    } while (element ? element.classList.contains("mod-form-field") : false);
  }
};
__name(_Collapse, "Collapse");
let Collapse = _Collapse;
const _Conditions = class _Conditions {
  constructor(form) {
    form && this.init(form);
  }
  init(form) {
    const groups = form.querySelectorAll("[conditional-target]");
    const conditionalElements = form.querySelectorAll("[conditional]") && Array.from(form.querySelectorAll("[conditional]")).map((element) => element).filter((element) => element.getAttribute("conditional"));
    const condtionalTargets = [];
    Array.from(groups).forEach((group) => {
      condtionalTargets.push({ element: group, json: JSON.parse(group.getAttribute("conditional-target")) });
      this.handleRequired(group.querySelectorAll("[data-js-required]"), true);
    });
    conditionalElements.forEach((element) => {
      element.addEventListener("change", (e) => {
        let conditions = element.getAttribute("conditional");
        conditions = JSON.parse(conditions.replace(/'/g, '"'));
        this.show(conditions, condtionalTargets);
      });
    });
  }
  show(conditions, condtionalTargets) {
    condtionalTargets.forEach((arr) => {
      if (conditions.label === arr.json.label) {
        if (conditions.value === arr.json.value) {
          arr.element.style.display = "block";
          this.handleRequired(arr.element.querySelectorAll("[js-no-validation]", false));
        } else {
          arr.element.style.display = "none";
          this.handleRequired(arr.element.querySelectorAll("[data-js-required]"), true);
        }
      }
    });
  }
  handleRequired(inputs, isHidden) {
    inputs && inputs.forEach((input) => {
      if (isHidden) {
        input.setAttribute("js-no-validation", "");
        input.removeAttribute("required");
      } else {
        input.removeAttribute("js-no-validation");
        if (input.hasAttribute("data-js-required")) {
          input.setAttribute("required", "");
        }
      }
    });
  }
};
__name(_Conditions, "Conditions");
let Conditions = _Conditions;
const _Policy = class _Policy {
  constructor(form) {
    this.parentElement = null;
    form && this.setListener(form);
  }
  setListener(form) {
    this.parentElement = form.querySelector(".js-policy-acceptance");
    if (this.parentElement) {
      this.parentElement.querySelector(".c-option__checkbox--hidden-box").addEventListener("change", () => {
        this.parentElement.querySelector('.c-option [type="checkbox"]:checked') ? this.parentElement.querySelector(".c-option__checkbox--label-text").classList.remove("u-color__text--danger") : "";
      });
    }
  }
  validatePolicy() {
    if (this.parentElement) {
      const checked = this.parentElement.querySelector('.c-option [type="checkbox"]:checked') ? true : false;
      if (!checked) {
        this.parentElement.querySelector(".c-option__checkbox--label-text").classList.add("u-color__text--danger");
      }
      return this.parentElement.querySelector('.c-option [type="checkbox"]:checked') ? true : false;
    }
  }
};
__name(_Policy, "Policy");
let Policy = _Policy;
const _Fields = class _Fields {
  constructor(form) {
    if (!form) return;
    this.form = form;
    this.inputs = form.querySelectorAll("input, textarea, select");
    this.checkboxGroups = form.querySelectorAll(".checkbox-group-required");
    this.setupFormValidate(form);
  }
  setupFormValidate() {
    const params = this.initialize();
    const formEmpty = new CustomEvent("formEmpty", {});
    this.checkEmpty();
    this.form.addEventListener("change", (e) => {
      this.form.dispatchEvent(formEmpty);
    });
    this.inputs.forEach((input) => {
      if (input.hasAttribute("data-validation-message")) {
        this.getFieldWrapper(input).querySelector(".c-field__error").setAttribute("aria-label", input.getAttribute("data-validation-message"));
        this.getFieldWrapper(input).querySelector(".c-field__error-message").innerHTML = input.getAttribute("data-validation-message");
      } else {
        if (this.getFieldWrapper(input).querySelector(".c-field__error")) {
          this.getFieldWrapper(input).querySelector(".c-field__error").remove();
        }
      }
      if (input.closest(".c-select--multiselect")) {
        const multiSelect = input.closest(".c-select--multiselect");
        multiSelect.querySelector(".c-select__options").addEventListener("click", (e) => {
          this.form.dispatchEvent(formEmpty);
        });
      }
    });
    this.setValidationListeners(params);
  }
  checkFormRequirements(form) {
    if (form.querySelector('[type="submit"]') === null) {
      console.error("Form must have a submit button.", form);
      return false;
    }
    return true;
  }
  initialize() {
    const checkboxHandler = new Checkbox(this.checkboxGroups);
    new Collapse(this.form);
    const policyHandler = new Policy(this.form);
    new Conditions(this.form);
    return { checkboxHandler, policyHandler };
  }
  setValidationListeners(params) {
    this.keyup();
    this.focusout();
    this.click(params);
    this.submit(params);
    this.form.addEventListener("formEmpty", () => {
      this.checkEmpty();
    });
  }
  /* Handle validation */
  validateInput(input, submitCheck = false) {
    let valueLength = input.value ? input.value.length : 0;
    if (input.hasAttribute("js-no-validation") || input.type === "checkbox" || input.type === "radio") {
      return;
    }
    if (["date", "week", "month", "time"].indexOf(input.type) != -1) {
      valueLength = 1;
    }
    if (valueLength > 0 || submitCheck) {
      if (input.hasAttribute("required")) {
        if (input.checkValidity()) {
          this.handleValid(input);
          return true;
        } else {
          this.handleInvalid(input);
          return false;
        }
      }
    } else {
      this.handleNotFilled(input);
      return false;
    }
  }
  handleValid(input) {
    this.classToggle(this.getFieldWrapper(input), "is-valid", "is-invalid");
    this.getFieldWrapper(input).querySelector(".c-field__error") ? this.getFieldWrapper(input).querySelector(".c-field__error").setAttribute("aria-hidden", true) : "";
  }
  handleInvalid(input) {
    this.classToggle(this.getFieldWrapper(input), "is-invalid", "is-valid");
    this.getFieldWrapper(input).querySelector(".c-field__error") ? this.getFieldWrapper(input).querySelector(".c-field__error").setAttribute("aria-hidden", false) : "";
  }
  handleNotFilled(input) {
    this.getFieldWrapper(input).classList.remove("is-valid", "is-invalid");
    this.getFieldWrapper(input).querySelector(".c-field__error") ? this.getFieldWrapper(input).querySelector(".c-field__error").setAttribute("aria-hidden", true) : "";
  }
  classToggle(element, addClass, removeClass) {
    !element.classList.contains(addClass) ? element.classList.add(addClass) : "";
    element.classList.remove(removeClass);
  }
  getFieldWrapper(input) {
    var fieldWrapper = input;
    do {
      if (fieldWrapper.parentNode !== document.body) {
        fieldWrapper = fieldWrapper.parentNode;
      } else {
        return input;
      }
    } while (!fieldWrapper.matches(".c-field, .c-option, .c-select"));
    return fieldWrapper;
  }
  /*  Listeners  */
  keyup() {
    this.inputs.forEach((input) => {
      input.addEventListener("keyup", () => {
        if (this.getFieldWrapper(input).classList.contains("is-invalid") || this.getFieldWrapper(input).classList.contains("is-valid")) {
          this.validateInput(input);
        }
        this.checkEmpty();
      });
    });
  }
  focusout() {
    ["focusout", "change"].forEach((e) => {
      [...this.inputs].forEach((input) => {
        input.addEventListener(e, () => {
          this.validateInput(input);
          this.checkEmpty();
        });
      });
    });
  }
  click({ checkboxHandler, policyHandler }) {
    const submitButton = this.form.querySelector('[type="submit"]');
    if (submitButton) {
      submitButton.addEventListener("click", (e) => {
        const containsInvalid = [];
        this.inputs.forEach((input) => {
          containsInvalid.push(this.validateInput(input, true));
        });
        containsInvalid.push(policyHandler.validatePolicy());
        containsInvalid.push(checkboxHandler.validateCheckboxes(this.checkboxGroups));
        if (containsInvalid.includes(false)) {
          this.classToggle(this.form, "is-invalid", "is-valid");
          checkboxHandler.validateCheckboxes(this.checkboxGroups);
          [...this.form.querySelectorAll(".c-form__notice-failed")].forEach((element) => {
            element.setAttribute("aria-hidden", false);
          });
          [...this.form.querySelectorAll(".c-form__notice-success")].forEach((element) => {
            element.setAttribute("aria-hidden", true);
          });
        }
      });
    }
  }
  checkEmpty() {
    let emptyForm = false;
    let attatchedFiles = false;
    const checkInputs = [];
    const submitButton = this.form.querySelector('[type="submit"]');
    this.form.querySelectorAll("input[js-field-fileinput]") ? this.form.querySelectorAll("input[js-field-fileinput]").length > 0 ? attatchedFiles = true : false : attatchedFiles = false;
    this.inputs.forEach((input) => {
      if (emptyForm) return;
      if (input?.type && (input?.type === "radio" || input?.type === "checkbox")) {
        checkInputs.push(input);
        return;
      }
      if (!input.classList.contains("js-no-validation")) {
        if (input.getAttribute("type") !== "hidden") {
          input.value.length > 0 || attatchedFiles ? emptyForm = true : "";
        }
      }
      this.validateInput(input);
    });
    if (!emptyForm && checkInputs.length > 0) {
      checkInputs.forEach((input) => {
        if (input.checked && !emptyForm) {
          emptyForm = true;
        }
      });
    }
    if (submitButton) {
      !emptyForm ? submitButton.disabled = true : submitButton.disabled = false;
    }
    return emptyForm;
  }
  submit({ checkboxHandler, policyHandler }) {
    const submitButton = this.form.querySelector('[type="submit"]');
    this.form.addEventListener("submit", (e) => {
      if (!checkboxHandler.validateCheckboxes(this.checkboxGroups)) {
        e.preventDefault();
        this.classToggle(this.form, "is-invalid", "is-valid");
      } else {
        this.classToggle(this.form, "is-valid", "is-invalid");
        if (typeof formbuilder !== "undefined") {
          submitButton ? submitButton.innerHTML = formbuilder.sending : "";
        }
      }
      [...this.form.querySelectorAll(".c-form__notice-failed")].forEach((element) => {
        element.setAttribute("aria-hidden", true);
      });
      [...this.form.querySelectorAll(".c-form__notice-success")].forEach((element) => {
        element.setAttribute("aria-hidden", false);
      });
    });
  }
};
__name(_Fields, "Fields");
let Fields = _Fields;
function initializeForms() {
  const forms = document.querySelectorAll(".js-form-validation");
  [...forms].forEach((form) => {
    new Fields(form);
  });
}
__name(initializeForms, "initializeForms");
const TEMPORAL_INPUT_TYPES = ["date", "time", "datetime-local", "month", "week"];
function initializePickerIcons() {
  document.addEventListener("click", (e) => {
    const icon = e.target.closest(".c-field__icon");
    if (!icon) return;
    const inner = icon.closest(".c-field__inner");
    if (!inner) return;
    const input = inner.querySelector("input");
    if (!input || !TEMPORAL_INPUT_TYPES.includes(input.type)) return;
    if (typeof input.showPicker === "function") {
      input.showPicker();
    } else {
      input.focus();
    }
  });
}
__name(initializePickerIcons, "initializePickerIcons");
initializePickerIcons();
if (document.readyState === "loading") document.addEventListener("DOMContentLoaded", initializeForms);
else initializeForms();
//# sourceMappingURL=field.js.map
