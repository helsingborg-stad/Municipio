let lModularity = null;
$ = jQuery;

export default function Helpers(Modularity) {
    lModularity = Modularity;
    $(function(){
    }.bind(this));
}

Helpers.prototype.uuid = function (separator) {
    return Math.random().toString(36).substr(2, 9);
};