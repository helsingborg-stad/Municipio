import Fab from './fab';
import './hide';
import { initializeComments } from './comments';
import { initializeLanguageMenu } from './languageMenu';
import { initializeCollapsibleSearch } from './collapsibleSearch';
import { initializeSessionManager } from './sessionManager';
import { initializeHashHighlightManager } from './hashHighlightManager';
import { initializeHashUpdateManager } from './hashUpdateManager';

const fab = new Fab();

fab.showOnScroll();

initializeLanguageMenu();
initializeSessionManager();
initializeComments();
initializeCollapsibleSearch();
initializeHashHighlightManager();
initializeHashUpdateManager();