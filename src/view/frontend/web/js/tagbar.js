define(['jquery', 'underscore', 'jquery-ui-modules/widget', 'flowbox'], function ($, _) {
    'use strict';

    $.widget('flowbox.tagbar', {
        _create: function () {
            this._super();
        }
    });

    return $.flowbox.tagbar;
});
