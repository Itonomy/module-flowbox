/**
 * Copyright © Itonomy BV. All rights reserved.
 * See LICENSE.md for license details.
 */

define([
    'uiComponent',
    'underscore',
    'ko',
    'jquery',
    'lib-flowbox-checkout',
    '!domReady'
], function(Component, _, ko, $, fb) {
    'use strict';

    var flowKeys = ['apiKey', 'products', 'orderId'];

    return Component.extend({
        defaults: {
            flowbox: {
            }
        },

        /**
         * Logs a debug message if javascript debugging is enabled
         * @param data
         */
        _debug: function(...data) {
            if (this.flowbox.debug) {
                console.debug(...data);
            }
        },

        /**
         * Initialize component
         * @param config
         * @returns {boolean|*}
         */
        initialize: function(config) {
            if (_.isArray(config.errors)) {
                console.error('Flowbox: server error(s):');
                _.each(config.errors, function (error) {
                    console.error(error);
                });
                return false;
            }

            this._super(config);

            this.flowbox.products = _.values(config.flowbox.products)

            var interval = setInterval(function() {
                if (_.isObject(window.flowboxCheckout) && _.isFunction(window.flowboxCheckout.checkout)) {
                    clearInterval(interval);
                    var flowConfig = _.pick(this.flowbox, flowKeys);
                    this._debug('Flowbox: checkout', flowConfig);
                    window.flowboxCheckout.checkout(flowConfig);
                }
            }.bind(this), 0.1);

            return this;
        },
    });
});

