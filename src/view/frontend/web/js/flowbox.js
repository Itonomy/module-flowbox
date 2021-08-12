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
    'jquery/jquery.cookie',
    '!domReady'
], function(Component, _, ko, $, fb) {
    'use strict';

    var flowKeys = [
        'allowCookies',
        'container',
        'key',
        'lazyload',
        'locale',
        'productId',
        'tags',
        'tagsOperator',
    ];

    return Component.extend({
        defaults: {
            flowbox: {
                allowCookies: false,
                cookieRestrictionEnabled: true,
                debug: false,
                lazyload: true,
                showTagBar: false,
                tagInputType: 'radio',
                tags: [],
            },
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

            if (this.flowbox.allowCookies) {
                var userAllowedSaveCookie = $.cookie('user_allowed_save_cookie')
                if (!(_.isNull(userAllowedSaveCookie) || _.isUndefined(userAllowedSaveCookie))) {
                    this.flowbox.allowCookies = JSON.parse(userAllowedSaveCookie)["1"] === 1;
                }
            }

            if (config.flowbox.showTagBar) {
                _.each(this.flowbox.tags, function(tag) {
                    self.tagData.push({ label: tag });
                });
                this.activeTags.subscribe(this.updateFlow, this, 'arrayChange');
            }

            this._debug('Flowbox: component init', this);


            return this;
        },

        hashAllTags: function (tags) {
            var ts = [];
            for (var i=0; i<tags.length;i++) {
                var tag = tags[i];
                if (tag.charAt(0) === '#') {
                    ts.push(tag);
                    continue;
                }
                ts.push('#' + tag);
            }
            return ts;
        },

        /**
         * Initialize flow
         * @param elem
         */
        initFlow: function (elem) {
            if (this.flowbox.flow === 'dynamic_product_flow' && !this.flowbox.hasOwnProperty('product_id')) {
                // Dynamic product flow but no product identifier, so don't initialize.
                this._debug('No product identifier found, not loading widget.');
                return;
            }
            var flowElement = elem.querySelector('.flowbox .flow');
            flowElement.id = _.uniqueId(`flowbox-${this.flowbox.flow}-container-`);
            this.flowbox.container = `#${flowElement.id}`;

            var flowConfig = _.pick(this.flowbox, flowKeys);
            flowConfig.tags = []; // reset tags array so initial flow displays all images.

            var interval = setInterval(function() {
                if (_.isFunction(window.flowbox)) {
                    clearInterval(interval);
                    this._debug('Flowbox: flow init', flowConfig);
                    window.flowbox('init', flowConfig);
                    this.updateFlow();
                }
            }.bind(this), 0.1);
        },

        /**
         * Update flow
         */
        updateFlow: function () {
            var flowConfig = _.pick(this.flowbox, flowKeys);
            var tags = this.activeTags()
            if (typeof(tags) === 'string') {
                tags = [tags];
            }
            var tags = this.hashAllTags(tags);
            flowConfig.tags = tags;
            this._debug('Flowbox: flow update', flowConfig);
            window.flowbox('update', flowConfig);
        }
    });
});
