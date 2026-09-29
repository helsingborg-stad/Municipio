var __defProp = Object.defineProperty;
var __name = (target, value) => __defProp(target, "name", { value, configurable: true });
const _Chat = class _Chat {
  constructor(container, input, messageFactory, messageStore, clear, messageRenderer, pendingMessageManager) {
    this.container = container;
    this.input = input;
    this.messageFactory = messageFactory;
    this.messageStore = messageStore;
    this.clear = clear;
    this.messageRenderer = messageRenderer;
    this.pendingMessageManager = pendingMessageManager;
  }
  container;
  input;
  messageFactory;
  messageStore;
  clear;
  messageRenderer;
  pendingMessageManager;
  init() {
    this.input.subscribeToSend(() => {
      const messageContent = this.input.get();
      if (messageContent) {
        this.addMessage(messageContent, false);
        this.input.clear();
      }
    });
  }
  disableSend() {
    this.input.disableSend();
  }
  getElement() {
    return this.container;
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
    const existingPendingMessage = this.pendingMessageManager.get();
    if (existingPendingMessage) {
      this.messageRenderer.moveToBottom(existingPendingMessage);
      return existingPendingMessage;
    }
    const message = this.pendingMessageManager.getOrCreate();
    this.messageRenderer.moveToBottom(message);
    this.messageStore.restore(message);
    this.getElement().dispatchEvent(new CustomEvent("chat:pending-message-added", { detail: message }));
    return message;
  }
  addMessage(messageContent, isReply = false, shouldPersist = true, id, data = {}) {
    return this.createMessage(messageContent, isReply, shouldPersist, id, data);
  }
  deleteMessage(message) {
    this.pendingMessageManager.resolve(message);
    message.delete();
    this.messageStore.delete(message);
    this.getElement().dispatchEvent(new CustomEvent("chat:message-deleted", { detail: message }));
  }
  clearMessages() {
    this.clear.clear();
    this.pendingMessageManager.clear();
    this.getElement().dispatchEvent(new CustomEvent("chat:messages-cleared"));
  }
  editMessage(newContent, message) {
    message.edit(newContent);
    this.pendingMessageManager.resolve(message);
    this.messageStore.save(message);
    this.getElement().dispatchEvent(new CustomEvent("chat:message-edited", { detail: message }));
  }
  updateMessage(message) {
    this.messageStore.save(message);
  }
  getPendingMessage() {
    return this.pendingMessageManager.get();
  }
  getMessages() {
    return this.messageStore.getAll();
  }
  createMessage(messageContent, isReply, shouldPersist, id, data = {}) {
    const message = this.messageFactory.create(messageContent, isReply, id, data);
    this.messageRenderer.render(message, this.pendingMessageManager.get());
    this.getElement().dispatchEvent(new CustomEvent("chat:message-added", { detail: message }));
    if (shouldPersist) {
      this.messageStore.save(message);
    } else {
      this.messageStore.restore(message);
    }
    return message;
  }
};
__name(_Chat, "Chat");
let Chat = _Chat;
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
const _Clear = class _Clear {
  constructor(storage) {
    this.storage = storage;
  }
  storage;
  clear() {
    const messages = this.storage.getAll();
    messages.forEach((message) => {
      message.delete();
    });
    this.storage.deleteAll();
  }
};
__name(_Clear, "Clear");
let Clear = _Clear;
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
const _LocalStorage = class _LocalStorage {
  constructor(id, basicStorage) {
    this.id = id;
    this.basicStorage = basicStorage;
    this.storageKey = `styleguide:chat:${this.id}:messages`;
  }
  id;
  basicStorage;
  storageKey;
  getSavedMessages() {
    const localStorageMessages = this.getMessagesFromLocalStorage();
    if (localStorageMessages.length > 0) {
      return localStorageMessages;
    }
    return this.basicStorage.getSavedMessages();
  }
  getAll() {
    return this.basicStorage.getAll();
  }
  delete(message) {
    this.basicStorage.delete(message);
    this.syncLocalStorage();
  }
  restore(message) {
    this.basicStorage.restore(message);
  }
  deleteAll() {
    this.basicStorage.deleteAll();
    try {
      window.localStorage.removeItem(this.storageKey);
    } catch {
      console.warn("Failed to clear messages from localStorage. Messages may persist across page reloads.");
    }
  }
  save(message) {
    this.basicStorage.save(message);
    this.syncLocalStorage();
  }
  syncLocalStorage() {
    try {
      const savedMessages = this.basicStorage.getSavedMessages();
      if (savedMessages.length === 0) {
        window.localStorage.removeItem(this.storageKey);
        return;
      }
      window.localStorage.setItem(this.storageKey, JSON.stringify(savedMessages));
    } catch {
      console.warn("Failed to save messages to localStorage. Messages will not persist across page reloads.");
    }
  }
  getMessagesFromLocalStorage() {
    try {
      const storedMessages = window.localStorage.getItem(this.storageKey);
      if (!storedMessages) {
        return [];
      }
      const parsedMessages = JSON.parse(storedMessages);
      if (!Array.isArray(parsedMessages)) {
        return [];
      }
      return parsedMessages.filter(this.isPersistentMessage);
    } catch {
      return [];
    }
  }
  isPersistentMessage(message) {
    return typeof message === "object" && message !== null && typeof message.id === "string" && typeof message.content === "string" && typeof message.isReply === "boolean" && typeof message.data === "object" && message.data !== null && !Array.isArray(message.data);
  }
};
__name(_LocalStorage, "LocalStorage");
let LocalStorage = _LocalStorage;
const _BasicStorage = class _BasicStorage {
  messages = /* @__PURE__ */ new Map();
  persistentMessageIds = [];
  getAll() {
    return Array.from(this.messages.values());
  }
  delete(message) {
    this.messages.delete(message.getId());
    const idx = this.persistentMessageIds.indexOf(message.getId());
    if (idx !== -1) {
      this.persistentMessageIds.splice(idx, 1);
    }
  }
  deleteAll() {
    this.messages.clear();
    this.persistentMessageIds = [];
  }
  restore(message) {
    this.messages.set(message.getId(), message);
    const idx = this.persistentMessageIds.indexOf(message.getId());
    if (idx !== -1) {
      this.persistentMessageIds.splice(idx, 1);
    }
  }
  save(message) {
    const id = message.getId();
    this.messages.set(id, message);
    const idx = this.persistentMessageIds.indexOf(id);
    if (idx === -1) {
      this.persistentMessageIds.push(id);
    }
  }
  getSavedMessages() {
    return this.persistentMessageIds.map((id) => this.messages.get(id)).filter((message) => !!message).map((message) => ({
      id: message.getId(),
      content: message.getContent(),
      isReply: message.getIsReply(),
      data: message.getData()
    }));
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
    const persistedMessages = this.storage.getSavedMessages();
    persistedMessages.forEach((message) => {
      this.chat.addMessage(message.content, message.isReply, true, message.id, message.data);
    });
    this.scrollToBottom();
  }
  scrollToBottom() {
    this.messagesScrollContainer.scrollTop = this.messagesScrollContainer.scrollHeight;
  }
};
__name(_Load, "Load");
let Load = _Load;
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
function init() {
  document.addEventListener("DOMContentLoaded", () => {
    document.querySelectorAll("[data-js-chat]").forEach((chatContainer) => {
      const id = chatContainer.getAttribute("data-js-chat");
      const messagesContainer = chatContainer.querySelector("[data-js-messages-container]");
      const messagesScrollContainer = chatContainer.querySelector("[data-js-message-area]");
      const replyMessageTemplate = chatContainer.querySelector("[data-js-reply-message-template]");
      const userMessageTemplate = chatContainer.querySelector("[data-js-user-message-template]");
      const chatInputContainer = chatContainer.querySelector("[data-js-chat-input]");
      const chatInput = chatContainer.querySelector("[data-js-chat-editable]");
      const sendButton = chatContainer.querySelector("[data-js-chat-send]");
      if (!id || !messagesContainer || !messagesScrollContainer || !replyMessageTemplate || !userMessageTemplate || !chatInput || !sendButton || !chatInputContainer) {
        console.error("Chat component initialization failed: Missing required attributes or elements.");
        return;
      }
      const input = new ChatInput(chatInputContainer, chatInput, sendButton);
      const messageFactory = new MessageFactory(replyMessageTemplate, userMessageTemplate);
      const store = new StorageFactory().create(id, chatContainer.hasAttribute("data-js-chat-persistent"));
      const clear = new Clear(store);
      const messageRenderer = new MessageRenderer(messagesContainer, messagesScrollContainer);
      const pendingMessageManager = new PendingMessageManager(messageFactory);
      const chat = new Chat(
        chatContainer,
        input,
        messageFactory,
        store,
        clear,
        messageRenderer,
        pendingMessageManager
      );
      new Load(chat, store, messagesScrollContainer).load();
      chat.init();
      document.dispatchEvent(new CustomEvent("chat:initialized", { detail: chat }));
    });
  }, { once: true });
}
__name(init, "init");
init();
//# sourceMappingURL=chat.js.map
