define(['jquery', 'underscore', 'ko', 'jquery-ui-modules/widget', 'flowbox'], function ($, _, ko) {
    'use strict';

    $.widget('flowbox.flow', {
        template: 'Itonomy_Flowbox/flowbox',
        _create: function () {
            this._super();

            if (!this._validateConfig()) {
                console.error('[Flowbox]: invalid configuration');
            }

            this.flowElement = this.element.find('.flowbox.flow');

            this.initContainer(this._getContainerId());

            this.initFlow(this.options.config);
        },

        /**
         * Initialize flow
         * @param options
         */
        initFlow: function(options) {
            if (!options.enable) {
                 console.warn('[Flowbox]: widget not enabled in configuration');
            }
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
        },

        initContainer: function(containerId) {
            console.debug(`[Flowbox]: initializing container with id: ${containerId}`);
            $(this.flowElement).attr('id', containerId);
            this.options.config.container = `#${containerId}`;
        },

        /**
         * Returns a unique id for this flowbox instance
         * @returns {string}
         * @private
         */
        _getContainerId: function() {
            // Underscore's uniqueId is perfect for DOM elements as it starts at 1 and counts.
            return _.uniqueId(`flowbox-${this.options.flow}-container-`);
        },

        /**
         * Validates flowbox configuration
         * @returns {boolean}
         * @private
         */
        _validateConfig: function() {
            if (!String(this.options.flow).length || !_.isObject(this.options.config) || _.has(this.options, 'enable')) {
                console.error('[Flowbox]: invalid data, cannot configure widget');
                return false;
            }
            var requiredKeys = ['key'];
            if (this.options.flow === 'dynamic-tag') {
                requiredKeys.push(['tags'])
            }
            if (this.options.flow === 'dynamic-product') {
                requiredKeys.push(['productId'])
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
    });

    return $.flowbox.flow;
});
