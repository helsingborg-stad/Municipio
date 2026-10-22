var __defProp = Object.defineProperty;
var __name = (target, value) => __defProp(target, "name", { value, configurable: true });
const _Chat = class _Chat {
  constructor(container, messagesScrollContainer, input, clear, messageHandler, pendingMessageHandler, infoHandler, defaultShouldPersist = true) {
    this.container = container;
    this.messagesScrollContainer = messagesScrollContainer;
    this.input = input;
    this.clear = clear;
    this.messageHandler = messageHandler;
    this.pendingMessageHandler = pendingMessageHandler;
    this.infoHandler = infoHandler;
    this.defaultShouldPersist = defaultShouldPersist;
  }
  container;
  messagesScrollContainer;
  input;
  clear;
  messageHandler;
  pendingMessageHandler;
  infoHandler;
  defaultShouldPersist;
  init() {
    this.input.subscribeToSend(() => {
      const messageContent = this.input.get();
      if (messageContent) {
        this.addMessage(messageContent, false);
        this.input.clear();
      }
    });
    this.input.subscribeToTyping(() => {
      this.getElement().dispatchEvent(new CustomEvent("chat:typing"));
    });
  }
  disableSend() {
    this.input.disableSend();
  }
  getElement() {
    return this.container;
  }
  getScrollContainer() {
    return this.messagesScrollContainer;
  }
  enableSend() {
    this.input.enableSend();
  }
  disable() {
    this.input.disable();
  }
  enable() {
    this.input.enable();
  }
  addPendingMessage() {
    return this.pendingMessageHandler.add();
  }
  addMessage(messageContent, isReply = false, shouldPersist, id, data = {}) {
    const effectiveShouldPersist = shouldPersist ?? this.defaultShouldPersist;
    return this.messageHandler.add(messageContent, isReply, effectiveShouldPersist, id, data);
  }
  addInfo(content, shouldPersist, id) {
    const effectiveShouldPersist = shouldPersist ?? this.defaultShouldPersist;
    return this.infoHandler.add(content, effectiveShouldPersist, id);
  }
  setDefaultShouldPersist(shouldPersist) {
    this.defaultShouldPersist = shouldPersist;
  }
  deleteMessage(message) {
    this.messageHandler.delete(message);
  }
  clearMessages() {
    this.clear.clear();
    this.pendingMessageHandler.clear();
    this.getElement().dispatchEvent(new CustomEvent("chat:messages-cleared"));
  }
  editMessage(newContent, message) {
    this.messageHandler.edit(newContent, message);
  }
  updateMessage(message) {
    this.messageHandler.update(message);
  }
  getPendingMessage() {
    return this.pendingMessageHandler.get();
  }
  getMessages() {
    return this.messageHandler.getMessages();
  }
};
__name(_Chat, "Chat");
let Chat = _Chat;
const _Clear = class _Clear {
  constructor(storage) {
    this.storage = storage;
  }
  storage;
  clear() {
    const items = this.storage.getAll();
    items.forEach((item) => {
      item.delete();
    });
    this.storage.deleteAll();
  }
};
__name(_Clear, "Clear");
let Clear = _Clear;
const _InfoHandler = class _InfoHandler {
  constructor(chatElement, infoFactory, infoRenderer, messageStore) {
    this.chatElement = chatElement;
    this.infoFactory = infoFactory;
    this.infoRenderer = infoRenderer;
    this.messageStore = messageStore;
  }
  chatElement;
  infoFactory;
  infoRenderer;
  messageStore;
  add(content, shouldPersist = false, id) {
    const info = this.infoFactory.create(content, id);
    this.infoRenderer.render(info);
    this.chatElement.dispatchEvent(new CustomEvent("chat:info-added", { detail: info }));
    if (shouldPersist) {
      this.messageStore.save(info);
    } else {
      this.messageStore.restore(info);
    }
    return info;
  }
};
__name(_InfoHandler, "InfoHandler");
let InfoHandler = _InfoHandler;
const _MessageHandler = class _MessageHandler {
  constructor(chatElement, messageFactory, messageStore, messageRenderer, pendingMessageManager) {
    this.chatElement = chatElement;
    this.messageFactory = messageFactory;
    this.messageStore = messageStore;
    this.messageRenderer = messageRenderer;
    this.pendingMessageManager = pendingMessageManager;
  }
  chatElement;
  messageFactory;
  messageStore;
  messageRenderer;
  pendingMessageManager;
  add(messageContent, isReply = false, shouldPersist = true, id, data = {}) {
    const message = this.messageFactory.create(messageContent, isReply, id, data);
    this.messageRenderer.render(message, this.pendingMessageManager.get());
    this.chatElement.dispatchEvent(new CustomEvent("chat:message-added", { detail: message }));
    if (shouldPersist) {
      this.messageStore.save(message);
    } else {
      this.messageStore.restore(message);
    }
    return message;
  }
  delete(message) {
    this.pendingMessageManager.resolve(message);
    message.delete();
    this.messageStore.delete(message);
    this.chatElement.dispatchEvent(new CustomEvent("chat:message-deleted", { detail: message }));
  }
  edit(newContent, message) {
    message.edit(newContent);
    this.pendingMessageManager.resolve(message);
    this.messageStore.save(message);
    this.chatElement.dispatchEvent(new CustomEvent("chat:message-edited", { detail: message }));
  }
  update(message) {
    this.messageStore.save(message);
  }
  getMessages() {
    return this.messageStore.getAll().filter(this.isMessageItem);
  }
  isMessageItem(item) {
    return item.getType() === "message";
  }
};
__name(_MessageHandler, "MessageHandler");
let MessageHandler = _MessageHandler;
const _PendingMessageHandler = class _PendingMessageHandler {
  constructor(chatElement, pendingMessageManager, messageRenderer, messageStore) {
    this.chatElement = chatElement;
    this.pendingMessageManager = pendingMessageManager;
    this.messageRenderer = messageRenderer;
    this.messageStore = messageStore;
  }
  chatElement;
  pendingMessageManager;
  messageRenderer;
  messageStore;
  add() {
    const existingPendingMessage = this.pendingMessageManager.get();
    if (existingPendingMessage) {
      this.messageRenderer.moveToBottom(existingPendingMessage);
      return existingPendingMessage;
    }
    const message = this.pendingMessageManager.getOrCreate();
    this.messageRenderer.moveToBottom(message);
    this.messageStore.restore(message);
    this.chatElement.dispatchEvent(new CustomEvent("chat:pending-message-added", { detail: message }));
    return message;
  }
  get() {
    return this.pendingMessageManager.get();
  }
  clear() {
    this.pendingMessageManager.clear();
  }
};
__name(_PendingMessageHandler, "PendingMessageHandler");
let PendingMessageHandler = _PendingMessageHandler;
const _Info = class _Info {
  constructor(infoElement, infoId, content) {
    this.infoElement = infoElement;
    this.infoId = infoId;
    this.content = content;
  }
  infoElement;
  infoId;
  content;
  getInfo() {
    return this.infoElement;
  }
  getType() {
    return "info";
  }
  delete() {
    this.getInfo().remove();
  }
  edit(content) {
    this.content = content;
    this.getInfo().textContent = content;
  }
  getContent() {
    return this.content;
  }
  getId() {
    return this.infoId;
  }
};
__name(_Info, "Info");
let Info = _Info;
const _InfoFactory = class _InfoFactory {
  constructor(infoTemplate) {
    this.infoTemplate = infoTemplate;
  }
  infoTemplate;
  create(content, id) {
    const infoElement = this.infoTemplate.content.firstElementChild?.cloneNode(true);
    const infoContentArea = infoElement.querySelector("[data-js-chat-info]");
    const infoId = id || `info-${Date.now()}-${Math.random().toString(36).substr(2, 9)}`;
    infoElement.setAttribute("data-js-info-id", infoId);
    if (infoContentArea) {
      infoContentArea.textContent = content;
    }
    return new Info(infoElement, infoId, content);
  }
};
__name(_InfoFactory, "InfoFactory");
let InfoFactory = _InfoFactory;
const _InfoRenderer = class _InfoRenderer {
  constructor(messageArea) {
    this.messageArea = messageArea;
  }
  messageArea;
  render(info) {
    this.messageArea.appendChild(info.getInfo());
  }
};
__name(_InfoRenderer, "InfoRenderer");
let InfoRenderer = _InfoRenderer;
const ALLOWED_CLASS_NAMES = /* @__PURE__ */ new Set(["c-chat__message--pending"]);
function sanitizeMarkup(html) {
  const temporaryContainer = document.createElement("div");
  temporaryContainer.innerHTML = html;
  sanitizeNode(temporaryContainer);
  cleanupWhitespaceNodes(temporaryContainer);
  return temporaryContainer.innerHTML;
}
__name(sanitizeMarkup, "sanitizeMarkup");
function sanitizeNode(node) {
  const forbiddenTags = /* @__PURE__ */ new Set(["script", "style", "iframe", "object", "embed", "form", "link", "meta", "base", "button", "img"]);
  const forbiddenAttributes = /* @__PURE__ */ new Set(["style"]);
  const unsafeUrlAttributes = /* @__PURE__ */ new Set(["href", "src", "action", "formaction"]);
  const unsafeUrlPattern = /^(javascript|data|vbscript):/i;
  Array.from(node.children).forEach((child) => {
    if (forbiddenTags.has(child.tagName.toLowerCase())) {
      child.remove();
      return;
    }
    Array.from(child.attributes).forEach((attribute) => {
      if (attribute.name.toLowerCase() === "class") {
        sanitizeClassAttribute(child, attribute.value);
        return;
      }
      if (forbiddenAttributes.has(attribute.name.toLowerCase()) || attribute.name.startsWith("on") || unsafeUrlAttributes.has(attribute.name.toLowerCase()) && unsafeUrlPattern.test(attribute.value.trim())) {
        child.removeAttribute(attribute.name);
      }
    });
    sanitizeNode(child);
    if (shouldReplaceWrapperWithLineBreak(child)) {
      replaceWrapperWithLineBreaks(child);
      return;
    }
    if (shouldRemoveEmptyElement(child)) {
      child.remove();
    }
  });
}
__name(sanitizeNode, "sanitizeNode");
function replaceWrapperWithLineBreaks(element) {
  const lineBreakCount = Math.max(1, element.querySelectorAll("br").length);
  const fragment = document.createDocumentFragment();
  Array.from({ length: lineBreakCount }).forEach(() => {
    fragment.appendChild(document.createElement("br"));
  });
  element.replaceWith(fragment);
}
__name(replaceWrapperWithLineBreaks, "replaceWrapperWithLineBreaks");
function shouldReplaceWrapperWithLineBreak(element) {
  if (element.tagName.toLowerCase() === "br") {
    return false;
  }
  const normalizedTextContent = getNormalizedTextContent(element);
  const hasLineBreakDescendant = element.querySelector("br") !== null;
  const isLineBreakWrapper = hasLineBreakDescendant && normalizedTextContent.length === 0;
  return isLineBreakWrapper;
}
__name(shouldReplaceWrapperWithLineBreak, "shouldReplaceWrapperWithLineBreak");
function shouldRemoveEmptyElement(element) {
  if (element.tagName.toLowerCase() === "br") {
    return false;
  }
  if (isPendingElement(element) || isInsidePendingElement(element)) {
    return false;
  }
  const hasElementChildren = element.children.length > 0;
  const normalizedTextContent = getNormalizedTextContent(element);
  return !hasElementChildren && normalizedTextContent.length === 0;
}
__name(shouldRemoveEmptyElement, "shouldRemoveEmptyElement");
function sanitizeClassAttribute(element, className) {
  const allowedClasses = className.split(/\s+/).filter((candidate) => ALLOWED_CLASS_NAMES.has(candidate));
  if (allowedClasses.length === 0) {
    element.removeAttribute("class");
    return;
  }
  element.setAttribute("class", allowedClasses.join(" "));
}
__name(sanitizeClassAttribute, "sanitizeClassAttribute");
function cleanupWhitespaceNodes(node) {
  Array.from(node.childNodes).forEach((childNode) => {
    if (childNode.nodeType === Node.TEXT_NODE) {
      normalizeWhitespaceTextNode(childNode);
      return;
    }
    if (childNode.nodeType === Node.ELEMENT_NODE) {
      cleanupWhitespaceNodes(childNode);
    }
  });
}
__name(cleanupWhitespaceNodes, "cleanupWhitespaceNodes");
function normalizeWhitespaceTextNode(textNode) {
  if (!isWhitespaceOnlyTextNode(textNode)) {
    return;
  }
  const previousSibling = getAdjacentMeaningfulSibling(textNode, "previousSibling");
  const nextSibling = getAdjacentMeaningfulSibling(textNode, "nextSibling");
  if (!previousSibling || !nextSibling || isBoundaryNode(previousSibling) || isBoundaryNode(nextSibling)) {
    textNode.remove();
    return;
  }
  textNode.textContent = " ";
}
__name(normalizeWhitespaceTextNode, "normalizeWhitespaceTextNode");
function getAdjacentMeaningfulSibling(node, direction) {
  let sibling = node[direction];
  while (sibling) {
    if (sibling.nodeType !== Node.TEXT_NODE) {
      return sibling;
    }
    if (!isWhitespaceOnlyTextNode(sibling)) {
      return sibling;
    }
    sibling = sibling[direction];
  }
  return null;
}
__name(getAdjacentMeaningfulSibling, "getAdjacentMeaningfulSibling");
function isBoundaryNode(node) {
  if (node.nodeType !== Node.ELEMENT_NODE) {
    return false;
  }
  const tagName = node.tagName.toLowerCase();
  return (/* @__PURE__ */ new Set(["br", "div", "p", "li"])).has(tagName);
}
__name(isBoundaryNode, "isBoundaryNode");
function isWhitespaceOnlyTextNode(textNode) {
  return textNode.textContent?.replace(/[\u00A0\u200B-\u200D\uFEFF]/g, " ").trim().length === 0;
}
__name(isWhitespaceOnlyTextNode, "isWhitespaceOnlyTextNode");
function getNormalizedTextContent(element) {
  return (element.textContent ?? "").replace(/\u00A0/g, " ").replace(/[\u200B-\u200D\uFEFF]/g, "").trim();
}
__name(getNormalizedTextContent, "getNormalizedTextContent");
function isPendingElement(element) {
  return element.classList.contains("c-chat__message--pending");
}
__name(isPendingElement, "isPendingElement");
function isInsidePendingElement(element) {
  return element.closest(".c-chat__message--pending") !== null;
}
__name(isInsidePendingElement, "isInsidePendingElement");
const _ChatInput = class _ChatInput {
  constructor(container, input, sendButton) {
    this.container = container;
    this.input = input;
    this.sendButton = sendButton;
    this.setupContainerListener();
    this.setupInputListeners();
    this.updateEmptyState();
  }
  container;
  input;
  sendButton;
  ACTION_BAR_ATTRIBUTE = "data-js-chat-actions";
  EMPTY_CLASS = "is-empty";
  TYPING_DEBOUNCE_INTERVAL_MS = 1e3;
  subscribeToSend(callback) {
    this.sendButton.addEventListener("click", (event) => {
      event.preventDefault();
      callback();
    });
    this.input.addEventListener("keydown", (event) => {
      if (event.key === "Enter" && !event.shiftKey) {
        event.preventDefault();
        callback();
      }
    });
  }
  subscribeToTyping(callback) {
    let lastTypingEventTimestamp = 0;
    this.input.addEventListener("input", () => {
      const currentTimestamp = Date.now();
      if (currentTimestamp - lastTypingEventTimestamp < this.TYPING_DEBOUNCE_INTERVAL_MS) {
        return;
      }
      lastTypingEventTimestamp = currentTimestamp;
      callback();
    });
  }
  get() {
    return this.input.innerHTML;
  }
  clear() {
    this.input.innerHTML = "";
    this.updateEmptyState();
  }
  disable() {
    this.input.setAttribute("contenteditable", "false");
    this.sendButton.setAttribute("disabled", "true");
  }
  disableSend() {
    this.sendButton.setAttribute("disabled", "true");
  }
  enableSend() {
    this.sendButton.removeAttribute("disabled");
  }
  enable() {
    this.input.setAttribute("contenteditable", "true");
    this.sendButton.removeAttribute("disabled");
  }
  setupInputListeners() {
    this.input.addEventListener("paste", (event) => {
      this.handlePaste(event);
    });
    this.input.addEventListener("input", () => {
      this.sanitizeEditableContent();
    });
  }
  setupContainerListener() {
    this.container.addEventListener("click", (event) => {
      if (event.target.hasAttribute(this.ACTION_BAR_ATTRIBUTE)) {
        event.preventDefault();
        this.input.focus();
        const range = document.createRange();
        range.selectNodeContents(this.input);
        range.collapse(false);
        const selection = window.getSelection();
        selection?.removeAllRanges();
        selection?.addRange(range);
      }
    });
  }
  handlePaste(event) {
    event.preventDefault();
    const clipboardData = event.clipboardData;
    if (!clipboardData) {
      return;
    }
    const plainTextContent = clipboardData.getData("text/plain");
    const htmlContent = clipboardData.getData("text/html");
    const sanitizedContent = plainTextContent.length > 0 ? this.convertPlainTextToMarkup(plainTextContent) : sanitizeMarkup(htmlContent);
    this.insertAtCaret(sanitizedContent);
    this.sanitizeEditableContent();
  }
  sanitizeEditableContent() {
    const sanitizedMarkup = sanitizeMarkup(this.input.innerHTML);
    if (sanitizedMarkup === this.input.innerHTML) {
      this.updateEmptyState();
      return;
    }
    this.input.innerHTML = sanitizedMarkup;
    this.placeCaretAtEnd();
    this.updateEmptyState();
  }
  updateEmptyState() {
    this.container.classList.toggle(this.EMPTY_CLASS, this.isEmpty());
  }
  isEmpty() {
    const normalizedTextContent = (this.input.textContent ?? "").replace(/\u00A0/g, " ").replace(/[\u200B-\u200D\uFEFF]/g, "").trim();
    return normalizedTextContent.length === 0;
  }
  insertAtCaret(html) {
    const selection = window.getSelection();
    if (!selection || selection.rangeCount === 0 || !this.input.contains(selection.anchorNode)) {
      this.input.innerHTML += html;
      this.placeCaretAtEnd();
      return;
    }
    const range = selection.getRangeAt(0);
    range.deleteContents();
    const fragment = range.createContextualFragment(html);
    const lastInsertedNode = fragment.lastChild;
    range.insertNode(fragment);
    if (lastInsertedNode) {
      range.setStartAfter(lastInsertedNode);
      range.setEndAfter(lastInsertedNode);
      selection.removeAllRanges();
      selection.addRange(range);
    }
  }
  placeCaretAtEnd() {
    const selection = window.getSelection();
    if (!selection) {
      return;
    }
    const range = document.createRange();
    range.selectNodeContents(this.input);
    range.collapse(false);
    selection.removeAllRanges();
    selection.addRange(range);
  }
  escapeHtml(content) {
    const temporaryContainer = document.createElement("div");
    temporaryContainer.textContent = content;
    return temporaryContainer.innerHTML;
  }
  convertPlainTextToMarkup(content) {
    return this.escapeHtml(content).replace(/\r\n|\r|\n/g, "<br>");
  }
};
__name(_ChatInput, "ChatInput");
let ChatInput = _ChatInput;
const _Load = class _Load {
  constructor(chat, storage, messagesScrollContainer) {
    this.chat = chat;
    this.storage = storage;
    this.messagesScrollContainer = messagesScrollContainer;
  }
  chat;
  storage;
  messagesScrollContainer;
  load() {
    const persistedItems = this.storage.getItems();
    persistedItems.forEach((item) => {
      if (item.type === "message") {
        this.chat.addMessage(item.content, item.isReply, true, item.id, item.data);
        return;
      }
      this.chat.addInfo(item.content, true, item.id);
    });
    this.scrollToBottom();
  }
  scrollToBottom() {
    this.messagesScrollContainer.scrollTop = this.messagesScrollContainer.scrollHeight;
  }
};
__name(_Load, "Load");
let Load = _Load;
const _Message = class _Message {
  constructor(isReply, message, messageContainer, id, content, data = {}) {
    this.isReply = isReply;
    this.message = message;
    this.messageContainer = messageContainer;
    this.id = id;
    this.content = content;
    this.data = data;
  }
  isReply;
  message;
  messageContainer;
  id;
  content;
  data;
  getMessage() {
    return this.message;
  }
  getMessageContainer() {
    return this.messageContainer;
  }
  getType() {
    return "message";
  }
  delete() {
    this.getMessageContainer().remove();
  }
  getId() {
    return this.id;
  }
  getContent() {
    return this.content;
  }
  getData() {
    return { ...this.data };
  }
  setData(data) {
    this.data = { ...data };
  }
  /**
   * Updates the message content and rendered markup.
   *
   * @param content The sanitized message content to display.
   */
  edit(content) {
    const sanitizedContent = sanitizeMarkup(content);
    this.content = sanitizedContent;
    this.message.innerHTML = sanitizedContent;
  }
  getIsReply() {
    return this.isReply;
  }
};
__name(_Message, "Message");
let Message = _Message;
const _MessageFactory = class _MessageFactory {
  constructor(replyMessageTemplate, userMessageTemplate) {
    this.replyMessageTemplate = replyMessageTemplate;
    this.userMessageTemplate = userMessageTemplate;
  }
  replyMessageTemplate;
  userMessageTemplate;
  create(message, isReply = false, id, data = {}) {
    const template = isReply ? this.replyMessageTemplate : this.userMessageTemplate;
    const sanitizedMessage = sanitizeMarkup(message);
    const messageElement = template.content.firstElementChild?.cloneNode(true);
    const messageContentArea = messageElement.querySelector("[data-js-chat-message]");
    messageContentArea.innerHTML = sanitizedMessage;
    const messageId = id || `message-${Date.now()}-${Math.random().toString(36).substr(2, 9)}`;
    messageElement.setAttribute("data-js-message-id", messageId);
    return new Message(isReply, messageContentArea, messageElement, messageId, sanitizedMessage, data);
  }
};
__name(_MessageFactory, "MessageFactory");
let MessageFactory = _MessageFactory;
const _MessageRenderer = class _MessageRenderer {
  constructor(messageArea, container) {
    this.messageArea = messageArea;
    this.container = container;
  }
  messageArea;
  container;
  render(message, pendingMessage = null) {
    const shouldAutoScroll = !message.getIsReply();
    const messageContainer = message.getMessageContainer();
    if (pendingMessage && pendingMessage.getId() !== message.getId()) {
      this.messageArea.insertBefore(messageContainer, pendingMessage.getMessageContainer());
      if (shouldAutoScroll) {
        this.scrollToBottom();
      }
      return;
    }
    this.messageArea.appendChild(messageContainer);
    if (shouldAutoScroll) {
      this.scrollToBottom();
    }
  }
  moveToBottom(message) {
    this.messageArea.appendChild(message.getMessageContainer());
  }
  scrollToBottom() {
    requestAnimationFrame(() => {
      this.container.scrollTop = this.container.scrollHeight;
    });
  }
};
__name(_MessageRenderer, "MessageRenderer");
let MessageRenderer = _MessageRenderer;
function getPendingMarkup() {
  return '<span class="c-chat__message--pending"><span></span><span></span><span></span></span>';
}
__name(getPendingMarkup, "getPendingMarkup");
const _PendingMessageManager = class _PendingMessageManager {
  constructor(messageFactory) {
    this.messageFactory = messageFactory;
  }
  messageFactory;
  pendingMessage = null;
  get() {
    return this.pendingMessage;
  }
  getOrCreate() {
    if (this.pendingMessage) {
      return this.pendingMessage;
    }
    this.pendingMessage = this.messageFactory.create(getPendingMarkup(), true);
    return this.pendingMessage;
  }
  resolve(message) {
    if (this.pendingMessage?.getId() === message.getId()) {
      this.pendingMessage = null;
    }
  }
  clear() {
    this.pendingMessage = null;
  }
};
__name(_PendingMessageManager, "PendingMessageManager");
let PendingMessageManager = _PendingMessageManager;
const _LocalStorage = class _LocalStorage {
  constructor(id, basicStorage) {
    this.id = id;
    this.basicStorage = basicStorage;
    this.storageKey = `styleguide:chat:${this.id}:items`;
  }
  id;
  basicStorage;
  storageKey;
  getItems() {
    const localStorageItems = this.getSavedDataFromLocalStorage();
    if (localStorageItems.length > 0) {
      return localStorageItems;
    }
    return this.basicStorage.getItems();
  }
  getAll() {
    return this.basicStorage.getAll();
  }
  delete(item) {
    this.basicStorage.delete(item);
    this.syncLocalStorage();
  }
  restore(item) {
    this.basicStorage.restore(item);
  }
  deleteAll() {
    this.basicStorage.deleteAll();
    try {
      window.localStorage.removeItem(this.storageKey);
    } catch {
      console.warn("Failed to clear chat items from localStorage. Items may persist across page reloads.");
    }
  }
  save(item) {
    this.basicStorage.save(item);
    this.syncLocalStorage();
  }
  syncLocalStorage() {
    try {
      const savedItems = this.basicStorage.getItems();
      if (savedItems.length === 0) {
        window.localStorage.removeItem(this.storageKey);
        return;
      }
      window.localStorage.setItem(this.storageKey, JSON.stringify(savedItems));
    } catch {
      console.warn("Failed to save chat items to localStorage. Items will not persist across page reloads.");
    }
  }
  getSavedDataFromLocalStorage() {
    try {
      const storedItems = window.localStorage.getItem(this.storageKey);
      if (!storedItems) {
        return [];
      }
      const parsedItems = JSON.parse(storedItems);
      if (!Array.isArray(parsedItems)) {
        return [];
      }
      return parsedItems.filter(this.isPersistentChatItem);
    } catch {
      return [];
    }
  }
  isPersistentChatItem(item) {
    if (typeof item !== "object" || item === null) {
      return false;
    }
    const candidate = item;
    if (candidate.type === "info") {
      return typeof candidate.id === "string" && typeof candidate.content === "string";
    }
    if (candidate.type === "message") {
      return typeof candidate.id === "string" && typeof candidate.content === "string" && typeof candidate.isReply === "boolean" && typeof candidate.data === "object" && candidate.data !== null && !Array.isArray(candidate.data);
    }
    return false;
  }
};
__name(_LocalStorage, "LocalStorage");
let LocalStorage = _LocalStorage;
const _BasicStorage = class _BasicStorage {
  items = /* @__PURE__ */ new Map();
  persistentItemIds = [];
  getAll() {
    return Array.from(this.items.values());
  }
  delete(item) {
    this.items.delete(item.getId());
    const idx = this.persistentItemIds.indexOf(item.getId());
    if (idx !== -1) {
      this.persistentItemIds.splice(idx, 1);
    }
  }
  deleteAll() {
    this.items.clear();
    this.persistentItemIds = [];
  }
  restore(item) {
    this.items.set(item.getId(), item);
    const idx = this.persistentItemIds.indexOf(item.getId());
    if (idx !== -1) {
      this.persistentItemIds.splice(idx, 1);
    }
  }
  save(item) {
    const id = item.getId();
    this.items.set(id, item);
    const idx = this.persistentItemIds.indexOf(id);
    if (idx === -1) {
      this.persistentItemIds.push(id);
    }
  }
  getItems() {
    return this.persistentItemIds.map((id) => this.items.get(id)).filter((item) => !!item).map((item) => this.toPersistentItem(item)).filter((item) => item !== null);
  }
  toPersistentItem(item) {
    if (item.getType() === "message") {
      const messageItem = item;
      return {
        type: "message",
        id: messageItem.getId(),
        content: messageItem.getContent(),
        isReply: messageItem.getIsReply(),
        data: messageItem.getData()
      };
    }
    if (item.getType() === "info") {
      const infoItem = item;
      return {
        type: "info",
        id: infoItem.getId(),
        content: infoItem.getContent()
      };
    }
    return null;
  }
};
__name(_BasicStorage, "BasicStorage");
let BasicStorage = _BasicStorage;
const _StorageFactory = class _StorageFactory {
  create(id, isPersistent) {
    if (isPersistent) {
      return new LocalStorage(id, new BasicStorage());
    }
    return new BasicStorage();
  }
};
__name(_StorageFactory, "StorageFactory");
let StorageFactory = _StorageFactory;
function init() {
  document.addEventListener(
    "DOMContentLoaded",
    () => {
      document.querySelectorAll("[data-js-chat]").forEach((chatContainer) => {
        const id = chatContainer.getAttribute("data-js-chat");
        const messagesContainer = chatContainer.querySelector("[data-js-messages-container]");
        const messagesScrollContainer = chatContainer.querySelector("[data-js-message-area]");
        const replyMessageTemplate = chatContainer.querySelector("[data-js-reply-message-template]");
        const userMessageTemplate = chatContainer.querySelector("[data-js-user-message-template]");
        const chatInputContainer = chatContainer.querySelector("[data-js-chat-input]");
        const chatInput = chatContainer.querySelector("[data-js-chat-editable]");
        const sendButton = chatContainer.querySelector("[data-js-chat-send]");
        const chatInfoTemplate = chatContainer.querySelector("[data-js-info-template]");
        if (!id || !messagesContainer || !messagesScrollContainer || !replyMessageTemplate || !userMessageTemplate || !chatInput || !sendButton || !chatInputContainer || !chatInfoTemplate) {
          console.error("Chat component initialization failed: Missing required attributes or elements.");
          return;
        }
        const input = new ChatInput(chatInputContainer, chatInput, sendButton);
        const messageFactory = new MessageFactory(replyMessageTemplate, userMessageTemplate);
        const infoFactory = new InfoFactory(chatInfoTemplate);
        const shouldPersistByDefault = chatContainer.hasAttribute("data-js-chat-persistent");
        const store = new StorageFactory().create(id, shouldPersistByDefault);
        const clear = new Clear(store);
        const messageRenderer = new MessageRenderer(messagesContainer, messagesScrollContainer);
        const infoRenderer = new InfoRenderer(messagesContainer);
        const pendingMessageManager = new PendingMessageManager(messageFactory);
        const messageHandler = new MessageHandler(chatContainer, messageFactory, store, messageRenderer, pendingMessageManager);
        const pendingMessageHandler = new PendingMessageHandler(chatContainer, pendingMessageManager, messageRenderer, store);
        const infoHandler = new InfoHandler(chatContainer, infoFactory, infoRenderer, store);
        const chat = new Chat(chatContainer, messagesScrollContainer, input, clear, messageHandler, pendingMessageHandler, infoHandler, shouldPersistByDefault);
        new Load(chat, store, messagesScrollContainer).load();
        chat.init();
        document.dispatchEvent(new CustomEvent("chat:initialized", { detail: chat }));
      });
    },
    { once: true }
  );
}
__name(init, "init");
init();
//# sourceMappingURL=chat.js.map
