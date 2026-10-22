import Fab from "./fab";
import "./hide";
import { initializeComments } from "./comments";
import { initializeHashHighlightManager } from "./hashHighlightManager";
import { initializeHashUpdateManager } from "./hashUpdateManager";
import { initializeHeaderLogoScrollShrink } from "./headerLogoScrollShrink";
import { initializeHeaderScrollOffset } from "./headerScrollOffset";
import { initializeLanguageMenu } from "./languageMenu";
import { initPostsListAsync } from "./postsList";
import { initializeWpApiSettingsNonceRefresh } from "./restApi/wpApiSettings";
import { initializeSessionManager } from "./sessionManager";

const fab = new Fab();

fab.showOnScroll();

initializeWpApiSettingsNonceRefresh();
initializeLanguageMenu();
initializeSessionManager();
initializeComments();
initializeHashHighlightManager();
initializeHeaderScrollOffset();
initializeHashUpdateManager();
initializeHeaderLogoScrollShrink();
initPostsListAsync();
