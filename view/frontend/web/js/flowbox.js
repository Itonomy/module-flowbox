/**
 * Copyright © Itonomy BV. All rights reserved.
 * See LICENSE.md for license details.
 */

define([
    'uiComponent',
    'underscore',
    'ko',
    'jquery',
    'lib-flowbox',
    '!domReady'
], function(Component, _, ko, $, fb) {
    'use strict';

    var flowKeys = [
        'allowCookies',
        'container',
        'key',
        'locale',
        'tags',
        'tagsOperator',
        'productId',
        'lazyload'
    ];

    return Component.extend({
        defaults: {
            flowbox: {
                allowCookies: true,
                lazyload: true,
                showTagBar: false,
                tags: [],
                debug: false,
            },
            initialized: false,
            template: {
                name: "Itonomy_Flowbox/flowbox",
            }
        },

        tagData: ko.observableArray([]),
        activeTags: ko.observableArray([]),

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
            var self = this;
            this.activeTags.extend({ rateLimit: 100 });
            if (_.isArray(config.errors)) {
                console.error('Flowbox: server error(s):');
                _.each(config.errors, function (error) {
                    console.error(error);
                });
                return false;
            }

            this._super(config);

            if (config.flowbox.showTagBar) {
                _.each(this.flowbox.tags, function(tag) {
                    self.tagData.push({ label: tag });
                });
                this.activeTags.subscribe(this._updateFlow, this, 'arrayChange');
            }

            this._debug('Flowbox: component init', this);

            return this;
        },

        toggleCheckboxActiveTag: function (label) {
            if (_.isFunction(window.flowbox)) {
                if (this.activeTags().length > 1 || !_.contains(this.activeTags(), label)) {
                    this.activeTags.removeAll()
                    this.activeTags.push(label)
                } else {
                    this.activeTags.removeAll()
                    _.each(this.flowbox.tags, function (label) {
                        this.activeTags.push(label)
                    }, this)
                }
            }
        },

        /**
         * Initialize flow
         * @param elem
         */
        initFlow: function (elem) {
            var flowElement = elem.querySelector('.flowbox .flow');
            flowElement.id = _.uniqueId(`flowbox-${this.flowbox.flow}-container-`);
            this.flowbox.container = `#${flowElement.id}`;

            var flowConfig = _.pick(this.flowbox, flowKeys);
            flowConfig.tags = this.flowbox.tags

            var interval = setInterval(function() {
                if (_.isFunction(window.flowbox)) {
                    clearInterval(interval);
                    this._debug('Flowbox: flow init', flowConfig);
                    window.flowbox('init', flowConfig);
                    this.activeTags.removeAll()
                    _.each(this.flowbox.tags, function (label) {
                        this.activeTags.push(label)
                    }, this)
                }
            }.bind(this), 0.1);
        },

        /**
         * Update flow
         * @param options
         */
        _updateFlow: function () {
            if (_.isFunction(window.flowbox)) {
                var flowConfig = _.pick(this.flowbox, flowKeys);
                flowConfig.tags = this.activeTags();
                this._debug('Flowbox: flow update', flowConfig);
                window.flowbox('update', flowConfig);
            }
        }
    });
});
