import Fab from './fab';
import Comments from './comments';
import ArchiveFilter from './archiveFilter';
import './drawer';

const fab = new Fab();
const archiveFilter = new ArchiveFilter();

fab.showOnScroll();

new Comments();
  