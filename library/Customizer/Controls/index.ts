import { BackgroundControlElement } from "./Background/BackgroundControl";
import { ColorChoiceControlElement } from "./ColorChoice/ColorChoiceControl";
import { ControlOrchestrator } from "./ControlOrchestrator";
import "./HeaderLogoScrollAspectRatioSync";
import { MultiCheckControlElement } from "./MultiCheck/MultiCheckControl";
import { MultiColorControlElement } from "./MultiColor/MultiColorControl";
import { MultiSelectControlElement } from "./MultiSelect/MultiSelectControl";
import { RepeaterControlElement } from "./Repeater/RepeaterControl";
import { SortableControlElement } from "./Sortable/SortableControl";
import { TabBoxControlElement } from "./TabBox/TabBoxControl";

new ControlOrchestrator([
	{
		tagName: "municipio-background-control",
		element: BackgroundControlElement,
	},
	{
		tagName: "municipio-color-choice-control",
		element: ColorChoiceControlElement,
	},
	{
		tagName: "municipio-multicheck-control",
		element: MultiCheckControlElement,
	},
	{
		tagName: "municipio-multicolor-control",
		element: MultiColorControlElement,
	},
	{
		tagName: "municipio-multiselect-control",
		element: MultiSelectControlElement,
	},
	{ tagName: "municipio-repeater-control", element: RepeaterControlElement },
	{ tagName: "municipio-sortable-control", element: SortableControlElement },
	{ tagName: "municipio-tab-box-control", element: TabBoxControlElement },
]).register();
