var sortableIn;
let lModularity = null
$ = jQuery;

export default function DragAndDrop(Modularity) {
    lModularity = Modularity;

    $(function(){

        if (typeof pagenow !== 'undefined' && pagenow == 'admin_page_modularity-editor') {
            this.init();
        }

    }.bind(this));
}

/**
 * Initialize
 * @return {void}
 */
DragAndDrop.prototype.init = function () {
    this.setupDraggable();
    this.setupDroppable();
    this.setupSortable();
};

DragAndDrop.prototype.setupSortable = function () {
    $('.modularity-js-sortable').sortable({
        handle: '.modularity-sortable-handle',
        connectWith: '.modularity-js-sortable',
        placeholder: 'ui-sortable-placeholder',
        start:  function(e, ui) {
            try {
                var validTargetAreas = jQuery(ui.item).attr('data-sidebar-incompability');
                    validTargetAreas = JSON.parse(validTargetAreas);
                    if (validTargetAreas && typeof validTargetAreas === "object") {
                        jQuery(".modularity-sidebar-area").each(function(index, sidebar) {
                            if(!validTargetAreas.includes(jQuery(this).attr('data-area-id'))) {
                                jQuery(this).parent().parent().removeClass("modularity-incompatible-area");
                            } else {
                                jQuery(this).parent().parent().addClass("modularity-incompatible-area");
                            }
                        });
                    }
            }
            catch(error) {
                console.log("Incompability information not defined - " + error);
            }
        },
        stop: function (e, ui) {
            var sidebarId = ui.item.parents('ul').data('area-id');
            ui.item.find('input[name^="modularity_modules"]').each(function (index, element) {
                var newName = $(this).attr('name').replace(/\[(.*?)\]/i, '[' + sidebarId + ']');
                $(this).attr('name', newName);
            });
            jQuery("[id^=modularity-mb-]").removeClass("modularity-incompatible-area");
        }
    }).bind(this);
};

/**
 * Setup draggable functionallity
 * @return {void}
 */
DragAndDrop.prototype.setupDraggable = function () {
    $('.modularity-js-draggable').draggable({
        appendTo: 'body',
        containment: 'document',
        scroll: false,
        helper: 'clone',
        revert: 'invalid',
        revertDuration: 200,
        start:  function( event, ui ) {
            try {
                var validTargetAreas = jQuery(this).attr('data-sidebar-incompability');
                    validTargetAreas = JSON.parse(validTargetAreas);

                    if (validTargetAreas && typeof validTargetAreas === "object") {
                        jQuery(".modularity-sidebar-area").each(function(index, sidebar) {
                            if(!validTargetAreas.includes(jQuery(this).attr('data-area-id'))) {
                                jQuery(this).parent().parent().removeClass("modularity-incompatible-area");
                            } else {
                                jQuery(this).parent().parent().addClass("modularity-incompatible-area");
                            }
                        });
                    }
            }
            catch(error) {
                console.log("Incompability information not defined - " + error);
            }
        },
        stop: function( event, ui ) {
            jQuery("[id^=modularity-mb-]").removeClass("modularity-incompatible-area");
        },

    });
};

/**
 * Setup droppable functionallity
 * @return {void}
 */
DragAndDrop.prototype.setupDroppable = function () {
    $('.modularity-js-droppable').droppable({
        accept: '.modularity-js-draggable',
        hoverClass: 'modularity-state-droppable',
        drop: function (e, ui) {
            this.appendModule(e, ui);
        }.bind(this)
    }).bind(this);
};

/**
 * Appends a module to the target when dropped
 * @param  {object} e  Event
 * @param  {object} ui UI
 * @return {void}
 */
DragAndDrop.prototype.appendModule = function (e, ui) {
    var module = ui.draggable;
    var moduleName = module.find('.modularity-module-name').text();
    var moduleId = module.data('module-id');
    var incompability = module.attr('data-sidebar-incompability');

    lModularity.Editor.Module.addModule(e.target, moduleId, moduleName, undefined, undefined, undefined, undefined, undefined, incompability);
};