
export default class DynamicAcf{

    constructor() {
        this.init();
    }

    init() {
        const self = this;
        jQuery(document).ready(function($){
            if (typeof acf == 'undefined') {
                return;
            }
            $(document).on('change', '[data-key="field_571dfc40f8114"] .acf-input select', function(e) {
                self.updateDateSourceOnPostTypeChange(e, $);
            });
            $('[data-key="field_571dfc40f8114"] .acf-input select').trigger('ready');
        });
    }

    updateDateSourceOnPostTypeChange(e, $) {
        if (this.request) {
            // if a recent request has been made abort it
            this.request.abort();
        }
        
        var dateSourceSelect = $('[data-key="field_62387e4b55b75"] select');
        dateSourceSelect.empty();
        
        var target = $(e.target);
        var state = target.val();
        
        if (!state) {
            return;
        }
        
        var data = {
            action: 'mod_posts_get_date_source',
            state: state
        }
        
        data = acf.prepareForAjax(data);
        
        this.request = $.ajax({
            url: acf.get('ajaxurl'), // acf stored value
            data: data,
            type: 'post',
            dataType: 'json',
            success: function(json) {
                if (!json) {
                    return;
                }
                // add the new options to the city field
                Object.entries(json).forEach(([value, label]) => {
                    var dateSourceOption = '<option value="'+value+'">'+label+'</option>';
                    dateSourceSelect.append(dateSourceOption);
                });
            }
        });
    }
}

new DynamicAcf();