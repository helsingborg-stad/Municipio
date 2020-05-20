import Fab from './fab';
import Comments from './comments';
import ArchiveFilter from './archiveFilter';


const fab = new Fab();
const archiveFilter = new ArchiveFilter();

fab.showOnScroll();

new Comments();