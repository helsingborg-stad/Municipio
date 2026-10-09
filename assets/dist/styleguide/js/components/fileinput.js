var __defProp = Object.defineProperty;
var __defNormalProp = (obj, key, value) => key in obj ? __defProp(obj, key, { enumerable: true, configurable: true, writable: true, value }) : obj[key] = value;
var __name = (target, value) => __defProp(target, "name", { value, configurable: true });
var __publicField = (obj, key, value) => __defNormalProp(obj, typeof key !== "symbol" ? key + "" : key, value);
const _FileInputController = class _FileInputController {
  input;
  files = [];
  fileAddedCallbacks = [];
  fileRemovedCallbacks = [];
  constructor(input) {
    this.input = input;
    this.bindEvents();
  }
  getFiles() {
    return this.files;
  }
  getInputElement() {
    return this.input;
  }
  bindEvents = /* @__PURE__ */ __name(() => {
    this.input.addEventListener("change", () => {
      const dropzone = this.input.closest('[data-js-file="dropzone"]');
      if (!dropzone) return;
      const getMaxFiles = /* @__PURE__ */ __name(() => parseInt(dropzone.getAttribute("data-js-file-max") || "", 10) || Infinity, "getMaxFiles");
      const isMulti = dropzone.getAttribute("data-js-file-is-multi") === "1";
      const maxFiles = getMaxFiles();
      const existingFiles = this.getFiles();
      const newFiles = Array.from(this.input.files || []);
      const getRemainingSlots = /* @__PURE__ */ __name(() => isMulti ? maxFiles - existingFiles.length : existingFiles.length === 0 ? 1 : 0, "getRemainingSlots");
      const remainingSlots = getRemainingSlots();
      if (remainingSlots <= 0) {
        return;
      }
      const acceptedFiles = newFiles.slice(0, remainingSlots);
      const dataTransfer = new DataTransfer();
      acceptedFiles.forEach((file) => dataTransfer.items.add(file));
      this.input.files = dataTransfer.files;
      this.addFiles(acceptedFiles);
    });
  }, "bindEvents");
  addFiles(files) {
    files.forEach((file) => {
      if (!this.files.some((f) => this.isSameFile(f, file))) {
        this.files.push(file);
        this.triggerFileAdded(file);
      }
    });
    const dataTransfer = new DataTransfer();
    this.files.forEach((f) => dataTransfer.items.add(f));
    this.input.files = dataTransfer.files;
  }
  removeFile(file) {
    this.files = this.files.filter((f) => !this.isSameFile(f, file));
    const dataTransfer = new DataTransfer();
    this.files.forEach((f) => dataTransfer.items.add(f));
    this.input.files = dataTransfer.files;
    this.triggerFileRemoved(file);
  }
  isSameFile(a, b) {
    return a.name === b.name && a.size === b.size && a.type === b.type && a.lastModified === b.lastModified;
  }
  triggerFileAdded(file) {
    this.fileAddedCallbacks.forEach((callback) => callback(file));
  }
  triggerFileRemoved(file) {
    this.input.dispatchEvent(new Event("change"));
    this.input.dispatchEvent(new Event("input"));
    this.fileRemovedCallbacks.forEach((callback) => callback(file));
  }
  onFileAdded(callback) {
    this.fileAddedCallbacks.push(callback);
  }
  onFileRemoved(callback) {
    this.fileRemovedCallbacks.push(callback);
  }
  removeFileFromList(file) {
    this.removeFile(file);
  }
};
__name(_FileInputController, "FileInputController");
let FileInputController = _FileInputController;
function HasMaxFiles(controller, dropzone) {
  const buttons = dropzone.querySelectorAll('[data-js-file="button"], [data-js-file="drop"]');
  const maxAttr = dropzone.getAttribute("data-js-file-max");
  const maxFiles = maxAttr ? parseInt(maxAttr, 10) : Infinity;
  const isMulti = dropzone.getAttribute("data-js-file-is-multi") === "1";
  const updateLimitState = /* @__PURE__ */ __name(() => {
    const fileCount = controller.getFiles().length;
    const isAtLimit = fileCount >= maxFiles || !isMulti && fileCount > 0;
    if (isAtLimit) {
      dropzone.setAttribute("data-js-file-disabled", "true");
    } else {
      dropzone.removeAttribute("data-js-file-disabled");
    }
    dropzone.classList.toggle("is-full", isAtLimit);
    buttons.forEach((btn) => {
      btn.disabled = isAtLimit;
    });
  }, "updateLimitState");
  controller.onFileAdded(updateLimitState);
  controller.onFileRemoved(updateLimitState);
  updateLimitState();
}
__name(HasMaxFiles, "HasMaxFiles");
function FileCounter(controller, dropzone) {
  const counter = dropzone.querySelector('[data-js-file="counter"]');
  if (!counter) return;
  const maxAttr = dropzone.getAttribute("data-js-file-max");
  const maxFiles = maxAttr ? parseInt(maxAttr, 10) : Infinity;
  counter.setAttribute("data-counter-max", maxFiles.toString());
  const updateCounter = /* @__PURE__ */ __name(() => {
    const fileCount = controller.getFiles().length.toString();
    const fileCountCurrentValue = counter.getAttribute("data-counter-current");
    if (fileCountCurrentValue !== fileCount) {
      counter.classList.remove("do-animate");
      void counter.offsetWidth;
      counter.classList.add("do-animate");
    }
    counter.setAttribute("data-counter-current", fileCount);
  }, "updateCounter");
  controller.onFileAdded(updateCounter);
  controller.onFileRemoved(updateCounter);
  updateCounter();
}
__name(FileCounter, "FileCounter");
function FileInputisEmpty(controller, dropzone) {
  const updateClass = /* @__PURE__ */ __name(() => {
    const hasFiles = controller.getFiles().length > 0;
    dropzone.classList.toggle("is-empty", !hasFiles);
  }, "updateClass");
  controller.onFileAdded(updateClass);
  controller.onFileRemoved(updateClass);
  updateClass();
}
__name(FileInputisEmpty, "FileInputisEmpty");
const _Notice = class _Notice {
  constructor(field) {
    this.field = field;
    this.noticeTemplate = this.field.querySelector('[data-js-file="notice-template"]');
    this.errorMessage = this.noticeTemplate?.dataset.jsUploadErrorMessage ?? "Following files could not be uploaded";
  }
  field;
  noticeTemplate;
  notice = null;
  noticeMessageElement = null;
  noticeMessageSelector = "[data-js-notice-message]";
  errorMessage = "";
  /**
   * Show a notice with a message
   * @param message The message to display in the notice
   */
  showNotice(invalidFiles) {
    if (!this.noticeTemplate) {
      console.error("Notice template not found");
      return;
    }
    const notice = this.cloneNotice();
    const errorMessageElement = notice.querySelector(this.noticeMessageSelector);
    if (!errorMessageElement) {
      console.error(`Notice message element not found.`);
      return;
    }
    errorMessageElement.textContent = this.createErrorMessage(invalidFiles);
    this.field.prepend(notice);
    this.notice = notice;
    this.noticeMessageElement = errorMessageElement;
  }
  /**
   * Hide the notice element
   */
  hideNotice() {
    this.notice?.remove();
    this.noticeMessageElement = null;
    this.notice = null;
  }
  /**
   * Create an error message from the invalid files
   * @param invalidFiles The list of invalid files
   * @returns A formatted error message
   */
  createErrorMessage(invalidFiles) {
    const fileNames = invalidFiles.map((file) => file.name).join(", ");
    return `${this.errorMessage}: ${fileNames}`;
  }
  /**
   * Clones the notice element from the template.
   * @returns A cloned notice element from the template
   */
  cloneNotice() {
    const notice = this.noticeTemplate.content.cloneNode(true);
    return notice.firstElementChild;
  }
};
__name(_Notice, "Notice");
let Notice = _Notice;
function MaxFileSize(controller, dropzone, noticeHandler) {
  const maxFileSize = dropzone.getAttribute("data-js-file-max-size");
  if (!maxFileSize) return;
  const maxFileSizeBytes = parseFloat(maxFileSize) * 1024 * 1024;
  controller.onFileAdded((file) => {
    dropzone.classList.remove("file-size-error");
    if (file.size > maxFileSizeBytes) {
      controller.removeFileFromList(file);
      noticeHandler.showNotice([file]);
      dropzone.classList.add("file-size-error");
    }
  });
}
__name(MaxFileSize, "MaxFileSize");
const _Dropzone = class _Dropzone {
  constructor(dropzone, noticeHandler, input) {
    this.dropzone = dropzone;
    this.noticeHandler = noticeHandler;
    this.input = input;
    this.registerEvents();
  }
  dropzone;
  noticeHandler;
  input;
  dragCounter = 0;
  invalidFilesClass = "invalid-files";
  /**
   * Register drag and drop events on the dropzone
   */
  registerEvents() {
    ["dragenter", "dragover", "dragleave", "drop"].forEach((event) => {
      this.dropzone.addEventListener(event, (e) => e.preventDefault());
    });
    this.dropzone.addEventListener("dragenter", () => {
      this.dragCounter++;
      this.setDragging(true);
    });
    this.dropzone.addEventListener("dragleave", () => {
      this.dragCounter--;
      if (this.dragCounter <= 0) {
        this.setDragging(false);
      }
    });
    this.dropzone.addEventListener("drop", (e) => {
      this.dragCounter = 0;
      this.setDragging(false);
      this.handleDrop(e);
    });
  }
  /**
   * Set the dragging state of the dropzone
   * @param isDragging 
   */
  setDragging(isDragging) {
    this.dropzone.classList.toggle("is-dragging", isDragging);
  }
  /**
   * Handle the drop event
   * @param event 
   */
  handleDrop(event) {
    if (!event.dataTransfer?.files.length) {
      return;
    }
    const fileCount = this.input.files ? this.input.files.length : 0;
    const maxFiles = this.input.hasAttribute("multiple") ? Infinity : 1;
    if (fileCount >= maxFiles) {
      event.preventDefault();
      return;
    }
    const droppedFiles = Array.from(event.dataTransfer.files);
    const [validFiles, invalidFiles] = this.filterAcceptedFiles(droppedFiles);
    this.noticeHandler.hideNotice();
    if (invalidFiles.length) {
      this.dropzone.classList.add(this.invalidFilesClass);
      this.handleInvalidFiles(invalidFiles);
    } else {
      this.dropzone.classList.remove(this.invalidFilesClass);
    }
    if (!validFiles.length) return;
    const finalFiles = this.limitFilesByMultiple(validFiles);
    const dataTransfer = new DataTransfer();
    finalFiles.forEach((file) => dataTransfer.items.add(file));
    this.input.files = dataTransfer.files;
    const eventChange = new Event("change", { bubbles: true });
    this.input.dispatchEvent(eventChange);
  }
  handleInvalidFiles(files) {
    this.noticeHandler.showNotice(files);
  }
  filterAcceptedFiles(files) {
    const acceptAttr = this.input.accept;
    if (!acceptAttr) return [files, []];
    const acceptedTypes = acceptAttr.split(",").map((type) => type.trim().toLowerCase());
    const validFiles = [];
    const invalidFiles = [];
    files.forEach((file) => {
      const fileType = file.type.toLowerCase();
      const fileExt = "." + file.name.split(".").pop()?.toLowerCase();
      const isValid = acceptedTypes.some((accept) => {
        if (accept.startsWith(".")) return fileExt === accept;
        if (accept.endsWith("/*")) return fileType.startsWith(accept.replace("/*", ""));
        return fileType === accept;
      });
      if (isValid) {
        validFiles.push(file);
      } else {
        invalidFiles.push(file);
      }
    });
    return [validFiles, invalidFiles];
  }
  /**
   * Limit the number of files to be uploaded
   * @param files 
   * @returns 
   */
  limitFilesByMultiple(files) {
    return this.input.hasAttribute("multiple") ? files : files.slice(0, 1);
  }
};
__name(_Dropzone, "Dropzone");
let Dropzone = _Dropzone;
const _FileList = class _FileList {
  constructor(controller, filePreviewRenderer) {
    this.controller = controller;
    this.filePreviewRenderer = filePreviewRenderer;
  }
  controller;
  filePreviewRenderer;
  /**
   * Setup the file list UI, synchronizing with the controller.
   * This includes adding and removing files from the list.
   * 
   * @returns {void}
   */
  init() {
    this.controller.onFileAdded((file) => {
      this.filePreviewRenderer.add(file);
    });
    this.controller.onFileRemoved((file) => {
      this.filePreviewRenderer.remove(file);
    });
  }
};
__name(_FileList, "FileList");
let FileList = _FileList;
const _FileIdCreator = class _FileIdCreator {
  /**
   * Create a unique ID for the file based on its properties.
   * This is used to identify files in the list.
   * 
   * @param file  File
   * @returns string
   */
  create(file) {
    const fileSignature = `${file.name}-${file.size}-${file.type}-${file.lastModified}`;
    return this.simpleHash(fileSignature);
  }
  /**
   * Generate a simple hash from the input string.
   * 
   * @param input string
   * @returns string
   */
  simpleHash(input) {
    let hash = 0;
    for (let i = 0; i < input.length; i++) {
      const char = input.charCodeAt(i);
      hash = (hash << 5) - hash + char;
      hash |= 0;
    }
    return hash.toString(16);
  }
};
__name(_FileIdCreator, "FileIdCreator");
let FileIdCreator = _FileIdCreator;
const _FileNameFormatter = class _FileNameFormatter {
  /**
   * Format file name to a more readable format.
   * This includes replacing underscores and dashes with spaces,
   * and capitalizing the first letter of each word.
   * @param file string
   * @returns 
   */
  format(file) {
    const lastDot = file.lastIndexOf(".");
    const namePart = lastDot !== -1 ? file.substring(0, lastDot) : file;
    const extension = lastDot !== -1 ? file.substring(lastDot) : "";
    const cleanName = namePart.replace(/[_-]/g, " ");
    const titleCased = cleanName.normalize("NFC").replace(
      new RegExp("\\p{L}+", "gu"),
      (word) => word.charAt(0).toLocaleUpperCase() + word.slice(1)
    );
    return titleCased + extension;
  }
};
__name(_FileNameFormatter, "FileNameFormatter");
let FileNameFormatter = _FileNameFormatter;
const _FileSizeFormatter = class _FileSizeFormatter {
  /**
   * Format file size to a human-readable format.
   * This includes converting bytes to KB, MB, or GB as appropriate.
   * @param size number
   * @returns string
   */
  format(size) {
    const kb = 1024;
    const mb = kb * 1024;
    const gb = mb * 1024;
    if (size >= gb) {
      return `${(size / gb).toFixed(2)} GB`;
    }
    if (size >= mb) {
      return `${(size / mb).toFixed(2)} MB`;
    }
    if (size >= kb) {
      return `${(size / kb).toFixed(2)} KB`;
    }
    return `${size} B`;
  }
};
__name(_FileSizeFormatter, "FileSizeFormatter");
let FileSizeFormatter = _FileSizeFormatter;
const _FilePreviewCardRenderer = class _FilePreviewCardRenderer {
  constructor(list, listitemTemplate, controller, fileNameFormatter, fileSizeFormatter, fileIdCreator, previewCreator) {
    this.list = list;
    this.listitemTemplate = listitemTemplate;
    this.controller = controller;
    this.fileNameFormatter = fileNameFormatter;
    this.fileSizeFormatter = fileSizeFormatter;
    this.fileIdCreator = fileIdCreator;
    this.previewCreator = previewCreator;
  }
  list;
  listitemTemplate;
  controller;
  fileNameFormatter;
  fileSizeFormatter;
  fileIdCreator;
  previewCreator;
  fileNameTarget = '[data-js-file="filename"]';
  fileSizeTarget = '[data-js-file="filesize"]';
  removeButtonTarget = '[data-js-file="remove"]';
  listItemTarget = '[data-js-file="listitem"]';
  filePreviewTarget = '[data-js-file="preview"]';
  fileIdAttribute = "data-js-file-id";
  /**
   * Adds a file to the preview list.
   * @param {File} file - The file to be added.
   */
  add(file) {
    const [listItem, fileName, fileSize, removeButton, filePreview] = this.getCloneTemplateElements();
    if (!(listItem && fileName && fileSize && removeButton && filePreview)) {
      console.error("Failed to clone template elements for file preview list.");
      return;
    }
    listItem.setAttribute(this.fileIdAttribute, this.fileIdCreator.create(file));
    this.setFileInfo(file, fileName, fileSize);
    this.setupRemoveButton(file, removeButton);
    const previewItem = this.previewCreator.createPreview(file);
    if (!previewItem) {
      filePreview.classList.add("is-unsupported");
    } else {
      filePreview.appendChild(previewItem);
    }
    this.list.appendChild(listItem);
  }
  /**
   * Removes a file from the preview list.
   * @param {File} file - The file to be removed.
   */
  remove(file) {
    const items = this.list.querySelectorAll(this.listItemTarget);
    items.forEach((item) => {
      if (item.getAttribute(this.fileIdAttribute) === this.fileIdCreator.create(file)) {
        item.remove();
      }
    });
  }
  /**
   * Sets up the remove button for the file preview.
   * @param {File} file - The file associated with the remove button.
   * @param {HTMLButtonElement} removeButton - The button element to set up.
   */
  setupRemoveButton(file, removeButton) {
    removeButton.addEventListener("click", () => {
      this.controller.removeFileFromList(file);
    });
  }
  /**
   * Sets the file name and size in the preview.
   * @param {HTMLElement} fileName - The element to display the file name.
   * @param {HTMLElement} fileSize - The element to display the file size.
   */
  setFileInfo(file, fileName, fileSize) {
    fileName.textContent = this.fileNameFormatter.format(file.name);
    fileSize.textContent = this.fileSizeFormatter.format(file.size || file.fakeSize || 0);
  }
  /**
   * Retrieves the cloned template elements for file preview.
   * @returns {HTMLElement[]} An array containing the cloned list item, file name, file size, and remove button.
   */
  getCloneTemplateElements() {
    const fragment = this.listitemTemplate.content.cloneNode(true);
    const listItem = fragment.firstElementChild;
    const fileName = listItem?.querySelector(this.fileNameTarget);
    const fileSize = listItem?.querySelector(this.fileSizeTarget);
    const removeButton = listItem?.querySelector(this.removeButtonTarget);
    const filePreview = listItem?.querySelector(this.filePreviewTarget);
    return [listItem, fileName, fileSize, removeButton, filePreview];
  }
};
__name(_FilePreviewCardRenderer, "FilePreviewCardRenderer");
let FilePreviewCardRenderer = _FilePreviewCardRenderer;
const _FilePreviewListRenderer = class _FilePreviewListRenderer {
  constructor(list, listitemTemplate, controller, fileNameFormatter, fileSizeFormatter, fileIdCreator) {
    this.list = list;
    this.listitemTemplate = listitemTemplate;
    this.controller = controller;
    this.fileNameFormatter = fileNameFormatter;
    this.fileSizeFormatter = fileSizeFormatter;
    this.fileIdCreator = fileIdCreator;
  }
  list;
  listitemTemplate;
  controller;
  fileNameFormatter;
  fileSizeFormatter;
  fileIdCreator;
  fileNameTarget = '[data-js-file="filename"]';
  fileSizeTarget = '[data-js-file="filesize"]';
  removeButtonTarget = '[data-js-file="remove"]';
  listItemTarget = '[data-js-file="listitem"]';
  fileIdAttribute = "data-js-file-id";
  /**
   * Adds a file to the preview list.
   * @param {File} file - The file to be added.
   */
  add(file) {
    const [listItem, fileName, fileSize, removeButton] = this.getCloneTemplateElements();
    if (!(listItem && fileName && fileSize && removeButton)) {
      console.error("Failed to clone template elements for file preview list.");
      return;
    }
    listItem.setAttribute(this.fileIdAttribute, this.fileIdCreator.create(file));
    this.setFileInfo(file, fileName, fileSize);
    this.setupRemoveButton(file, removeButton);
    this.list.appendChild(listItem);
  }
  /**
   * Sets up the remove button for the file preview.
   * @param {File} file - The file associated with the remove button.
   * @param {HTMLButtonElement} removeButton - The button element to set up.
   */
  setupRemoveButton(file, removeButton) {
    removeButton.addEventListener("click", () => {
      this.controller.removeFileFromList(file);
    });
  }
  /**
   * Sets the file name and size in the preview.
   * @param {HTMLElement} fileName - The element to display the file name.
   * @param {HTMLElement} fileSize - The element to display the file size.
   */
  setFileInfo(file, fileName, fileSize) {
    fileName.textContent = this.fileNameFormatter.format(file.name);
    fileSize.textContent = this.fileSizeFormatter.format(file.size);
  }
  /**
   * Removes a file from the preview list.
   * @param {File} file - The file to be removed.
   */
  remove(file) {
    const items = this.list.querySelectorAll(this.listItemTarget);
    items.forEach((item) => {
      if (item.getAttribute(this.fileIdAttribute) === this.fileIdCreator.create(file)) {
        item.remove();
      }
    });
  }
  /**
   * Retrieves the cloned template elements for file preview.
   * @returns {HTMLElement[]} An array containing the cloned list item, file name, file size, and remove button.
   */
  getCloneTemplateElements() {
    const fragment = this.listitemTemplate.content.cloneNode(true);
    const listItem = fragment.firstElementChild;
    const fileName = listItem?.querySelector(this.fileNameTarget);
    const fileSize = listItem?.querySelector(this.fileSizeTarget);
    const removeButton = listItem?.querySelector(this.removeButtonTarget);
    return [listItem, fileName, fileSize, removeButton];
  }
};
__name(_FilePreviewListRenderer, "FilePreviewListRenderer");
let FilePreviewListRenderer = _FilePreviewListRenderer;
const _PreviewCreator = class _PreviewCreator {
  createPreview(file) {
    const fileType = file.type || "unknown";
    const url = file.isPlaceholder ? file.url : URL.createObjectURL(file);
    if (fileType.startsWith("image/")) {
      return this.createImagePreview(url);
    } else if (fileType.startsWith("video/")) {
      return this.createVideoPreview(url);
    } else if (fileType.startsWith("audio/")) {
      return this.createAudioPreview(url);
    } else if (fileType === "application/pdf") {
      return this.createPdfPreview(url);
    }
    return null;
  }
  createImagePreview(url) {
    const image = document.createElement("img");
    image.src = url;
    return image;
  }
  createVideoPreview(url) {
    const video = document.createElement("video");
    video.src = url;
    video.controls = true;
    return video;
  }
  createAudioPreview(url) {
    const audio = document.createElement("audio");
    audio.src = url;
    audio.controls = true;
    return audio;
  }
  createPdfPreview(url) {
    const pdfFrame = document.createElement("iframe");
    pdfFrame.src = url;
    pdfFrame.style.width = "100%";
    pdfFrame.style.height = "100%";
    pdfFrame.style.border = "none";
    return pdfFrame;
  }
};
__name(_PreviewCreator, "PreviewCreator");
let PreviewCreator = _PreviewCreator;
const _FilePreviewFactory = class _FilePreviewFactory {
  static createFilePreviewRenderer(controller, fileInput, listArea, previewTemplate) {
    const params = [
      listArea,
      previewTemplate,
      controller,
      new FileNameFormatter(),
      new FileSizeFormatter(),
      new FileIdCreator()
    ];
    if (fileInput.dataset.jsFilePreview) {
      return new FilePreviewCardRenderer(...params, new PreviewCreator());
    }
    return new FilePreviewListRenderer(...params);
  }
};
__name(_FilePreviewFactory, "FilePreviewFactory");
let FilePreviewFactory = _FilePreviewFactory;
const _FileInputButtonHandler = class _FileInputButtonHandler {
  constructor(input, button) {
    this.input = input;
    this.button = button;
  }
  input;
  button;
  /**
   * Setup the button to trigger the file input click event.
   */
  init() {
    let isOpeningFilePicker = false;
    this.button.addEventListener("focusout", () => {
      if (isOpeningFilePicker) {
        isOpeningFilePicker = false;
        return;
      }
      this.input.dispatchEvent(new Event("blur"));
    });
    this.button.addEventListener("click", () => {
      isOpeningFilePicker = true;
      this.button.focus();
      this.input.click();
    });
    this.input.addEventListener("focusin", (e) => {
      this.button.focus();
    });
  }
};
__name(_FileInputButtonHandler, "FileInputButtonHandler");
let FileInputButtonHandler = _FileInputButtonHandler;
const _FilePlaceholderCreator = class _FilePlaceholderCreator {
  registerController(controller, input) {
    const key = this.getInputKey(input);
    if (!key) {
      console.error("FilePreviewStore: Input element must have an id or name attribute to register controller.");
      return;
    }
    _FilePlaceholderCreator.controllers[key] = controller;
  }
  getInputKey(input) {
    return input.id || input.name || null;
  }
  getController(input) {
    const key = this.getInputKey(input);
    if (key && _FilePlaceholderCreator.controllers[key]) {
      return _FilePlaceholderCreator.controllers[key];
    }
    return null;
  }
  /**
   * Adds the global function to window object to add fake files.
   * 
   * @returns 
   */
  async addWindowFunction() {
    if (_FilePlaceholderCreator.windowFunctionInitialized) {
      return;
    }
    _FilePlaceholderCreator.windowFunctionInitialized = true;
    window.addFakeFileToInput = async (fileData, input) => {
      const controller = this.getController(input);
      if (!controller) {
        console.error("FilePlaceholderCreator: No controller registered for the provided input element.");
        return;
      }
      const fakeFile = new File([""], fileData.name, { type: fileData.type });
      fakeFile.isPlaceholder = true;
      fakeFile.id = fileData.id;
      fakeFile.url = fileData.url;
      fakeFile.fakeSize = fileData.size;
      controller.addFiles([fakeFile]);
    };
  }
};
__name(_FilePlaceholderCreator, "FilePlaceholderCreator");
__publicField(_FilePlaceholderCreator, "controllers", {});
__publicField(_FilePlaceholderCreator, "windowFunctionInitialized", false);
let FilePlaceholderCreator = _FilePlaceholderCreator;
function HasMinFiles(controller, dropzone) {
  const minAttr = dropzone.getAttribute("data-js-file-min");
  const minFiles = minAttr ? parseInt(minAttr, 10) : 0;
  const input = controller.getInputElement();
  const validationMessage = dropzone.querySelector("[data-js-upload-error-message-min-files]")?.getAttribute("data-js-upload-error-message-min-files") || "Please upload the minimum required number of files.";
  const updateLimitState = /* @__PURE__ */ __name(() => {
    const fileCount = controller.getFiles().length;
    const hasPassedRequirement = fileCount >= minFiles || minFiles === 0;
    if (!hasPassedRequirement) {
      dropzone.classList.add("has-min-files-not-met");
      input.setCustomValidity(validationMessage);
    } else {
      dropzone.classList.remove("has-min-files-not-met");
      input.setCustomValidity("");
    }
    input.dispatchEvent(new Event("blur", { bubbles: true }));
  }, "updateLimitState");
  controller.onFileAdded(updateLimitState);
  controller.onFileRemoved(updateLimitState);
}
__name(HasMinFiles, "HasMinFiles");
const _FileInput = class _FileInput {
  constructor() {
    this.initFileInputs();
  }
  initFileInputs() {
    document.querySelectorAll('[data-js-file="dropzone"]').forEach((dropzone) => {
      const [
        input,
        button,
        listArea,
        template
      ] = this.getElementsFromDropzone(dropzone);
      if (!(input && button && listArea && template)) {
        console.error("FileInput: Missing required elements in dropzone.");
        return;
      }
      const controller = new FileInputController(input);
      const globalPlaceholderCreator = new FilePlaceholderCreator();
      globalPlaceholderCreator.addWindowFunction();
      globalPlaceholderCreator.registerController(controller, input);
      if (dropzone) {
        const noticeHandler = new Notice(dropzone);
        new FileList(controller, FilePreviewFactory.createFilePreviewRenderer(
          controller,
          dropzone,
          listArea,
          template
        )).init();
        new FileInputButtonHandler(input, button).init();
        new Dropzone(dropzone, noticeHandler, input);
        MaxFileSize(controller, dropzone, noticeHandler);
        HasMaxFiles(controller, dropzone);
        HasMinFiles(controller, dropzone);
        FileCounter(controller, dropzone);
        FileInputisEmpty(controller, dropzone);
      }
    });
  }
  getElementsFromDropzone(dropzone) {
    const input = dropzone.querySelector('[data-js-file="input"]');
    const button = dropzone.querySelector('[data-js-file="button"]');
    const listArea = dropzone.querySelector('[data-js-file="list"]');
    const template = dropzone.querySelector('[data-js-file="listitem-template"]');
    return [input, button, listArea, template];
  }
};
__name(_FileInput, "FileInput");
let FileInput = _FileInput;
const init = /* @__PURE__ */ __name(() => new FileInput(), "init");
if (document.readyState === "loading") document.addEventListener("DOMContentLoaded", init);
else init();
//# sourceMappingURL=fileinput.js.map
