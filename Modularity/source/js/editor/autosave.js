let lModularity = null;
$ = jQuery;

export default function Autosave(Modularity) {
    lModularity = Modularity;
    $(function(){
        //this.handleEvents();
    }.bind(this));
}

Autosave.prototype.save = function (selector) {
    $('#modularity-options #publishing-action .spinner').css('visibility', 'visible');
    var request = $(selector).serializeObject();
    request.id = modularity_post_id;
    request.action = 'save_modules';

    $.post(ajaxurl, request, function (response) {
        $('#modularity-options #publishing-action .spinner').css('visibility', 'hidden');
    });
};

jQuery.fn.serializeObject = function()
{
    var o = {};
    var a = this.serializeArray();
    jQuery.each(a, function() {
        if (o[this.name] !== undefined) {
            if (!o[this.name].push) {
                o[this.name] = [o[this.name]];
            }
            o[this.name].push(this.value || '');
        } else {
            o[this.name] = this.value || '';
        }
    });
    return o;
};