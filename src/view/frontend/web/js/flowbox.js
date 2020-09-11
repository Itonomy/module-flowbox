define(['jquery', 'underscore', 'jquery-ui-modules/widget', 'lib-flowbox'], function ($, _) {
    'use strict';

    $.widget('flowbox.flow', {
        _create: function () {
            this._super();
            this.initFlow(this.options.flowbox);
        },

        /**
         * Initialize flow
         * @param options
         */
        initFlow: function(options) {
            var interval = setInterval(function() {
                if (typeof window.flowbox == 'function') {
                    clearInterval(interval);
                    window.flowbox('init', options.config);
                }
            }, 0.1);
        },

        /**
         * Update flow
         * @param tags
         */
        updateFlow: function (options) {
            window.flowbox(
                'update',
                _.clone(this.options.flowbox.config).extendOwn(options)
            );
        }
    });

    return $.flowbox.flow;
});
