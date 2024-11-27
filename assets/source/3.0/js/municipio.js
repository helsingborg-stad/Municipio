import cssVars from 'css-vars-ponyfill';
import Fab from './fab';
import Comments from './comments';
import './nav';
import './hide';
import { initializeLanguageMenu } from './languageMenu';
import { initializeCollapsibleSearch } from './collapsibleSearch';

//Ponyfill for supporting css variables in IE
cssVars({
    //Options
    silent: false, //Set to true to display errors in console
    watch: true, //Adds an observer to run when new styling is added

    //Callbacks
    onBeforeSend: (xhr, elm, url) => {},
    onError: (message, elm, xhr, url) =>{},
    onWarning: (message) => {},
    onSuccess: (cssText, elm, url) => {},
    onComplete: (cssText, styleElms, cssVariables, benchmark) => {},
    onFinally: (hasChanged, hasNativeSupport, benchmark) => {}
});

const fab = new Fab();

fab.showOnScroll();

initializeLanguageMenu();

document.addEventListener('DOMContentLoaded', () => {
    initializeCollapsibleSearch();
});

new Comments();
   