import { initializeA11yWarningMarkers } from "./a11yWarningMarkers";
import { initializeHeadingHierarchyIndicator } from "./headingHierarchyIndicator";
import { initializeMissingH1Indicator } from "./missingH1Indicator";
import { initializeVagueControlTextIndicator } from "./vagueControlTextIndicator";

initializeVagueControlTextIndicator();
initializeHeadingHierarchyIndicator();
initializeMissingH1Indicator();
initializeA11yWarningMarkers();
