import { LayerGroupFilterInterface } from "./layerGroupFilterInterface";

interface FilterHelperInterface {
    findChildren(id: string): LayerGroupFilterInterface[];
    findParent(parentId: string): LayerGroupFilterInterface|null;
    hideChildrenFilter(id: string): void;
    showChildrenFilter(id: string): void;
}