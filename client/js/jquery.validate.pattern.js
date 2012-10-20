jQuery.validator.addMethod("pattern", function(value, element, param) {
    if (typeof param === 'string') {
        parts = param.split("/");
        modifiers = parts[parts.length - 1];
        if (!/^[img]+$/.test(modifiers)) {
            modifiers = '';
        }
        param = param.replace(/(.*\/)[img]+$/, "$1");
        param = param.replace(/^\//, '');
        param = param.replace(/\/$/, '');
        param = new RegExp(param, modifiers);
    }
    return this.optional(element) || param.test(value);
}, "Please enter a value with a proper pattern.");

