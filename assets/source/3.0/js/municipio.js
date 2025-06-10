import Fab from './fab';
import './hide';
import { initializeComments } from './comments';
import { initializeLanguageMenu } from './languageMenu';
import { initializeCollapsibleSearch } from './collapsibleSearch';
import { initializeSessionManager } from './sessionManager';

const fab = new Fab();

fab.showOnScroll();

initializeLanguageMenu();
initializeSessionManager();
initializeComments();
initializeCollapsibleSearch();