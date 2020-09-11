define(['jquery', 'underscore', 'jquery-ui-modules/widget', 'lib-flowbox'], function ($, _) {
    'use strict';

    $.widget('flowbox.flow', {
        _isConfigValid: false,
        _create: function () {
            this._super();
            if (this._validateConfig()) {
                console.debug(`[Flowbox]: configuration for flow ${this.options.flow} is valid`);
                this.initFlow(this.options.config);
            }
        },

        _validateConfig: function() {
            if (!String(this.options.flow).length || !_.isObject(this.options.config)) {
                console.error('[Flowbox]: invalid data, cannot configure widget');
                return false;
            }
            var requiredKeys = ['key', 'container'];
            if (this.options.flow === 'dynamic-tag') {
                requiredKeys.push(['tags'])
            }
            if (this.options.flow === 'dynamic-product') {
                requiredKeys.push(['product_id'])
            }
            var valid = true;
            _.each(requiredKeys, function(requiredKey) {
                if (!this.config.hasOwnProperty(requiredKey)
                    || null === this.config[requiredKey]
                    || !this.config[requiredKey].length
                ) {
                    console.error(`Flowbox: required property ${requiredKey} missing for flow ${this.flow}`);
                    valid = false;
                }
            }, this.options)
            return valid;
        },

        /**
         * Initialize flow
         * @param options
         */
        initFlow: function(options) {
            console.debug('[Flowbox]: awaiting window.flowbox() function..');
            var interval = setInterval(function() {
                if (typeof window.flowbox == 'function') {
                    clearInterval(interval);
                    console.debug('[Flowbox]: window.flowbox() function found');
                    console.debug('[Flowbox]: calling init', options);
                    window.flowbox('init', options);
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
                _.clone(this.options.config).extendOwn(options)
            );
        }
    });

    return $.flowbox.flow;
});
