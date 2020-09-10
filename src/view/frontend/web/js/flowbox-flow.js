define(['MutationObserver', 'jquery', 'underscore', 'jquery-ui-modules/widget', 'domReady!'], function (mutationObserver, $, _) {
    'use strict';

    $.widget('flowbox.flow', {
        /**
         * Inject flowbox into container element when it has been loaded
         */
        _create: function () {
            this._super();
        },

        initFlow: function() {
            window.flowbox('init', this.options.config)
        },

        /**
         * Updates the widget's tags if its type is dynamic-tag-flow
         * @param tags
         */
        updateFlow: function (tags) {
            if (this.options.type === 'dynamic-tag-flow') {
                window.flowbox('update', _.extend(this.options.flowbox.config, {'tags': tags}));
            }
        }
    });

    return $.flowbox.flow;
});
