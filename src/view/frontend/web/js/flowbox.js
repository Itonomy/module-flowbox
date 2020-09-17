/**
 * Copyright © Itonomy BV. All rights reserved.
 * See COPYING.txt for license details.
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
        'container',
        'key',
        'locale',
        'tags',
        'tagsOperator',
        'productId',
        'allowCookies',
        'lazyload'
    ];

    var tagData = [];

    var activeTags = ko.observableArray([]);
    activeTags.extend({rateLimit: 100});

    return Component.extend({

        defaults: {
            flowbox: {
                allowCookies: false,
                lazyload: true,
                showTagBar: false
            },
            template: {
                name: "Itonomy_Flowbox/flowbox",
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

            var userAllowedSaveCookie = $.cookie('user_allowed_save_cookie')
            if (!(_.isNull(userAllowedSaveCookie) || _.isUndefined(userAllowedSaveCookie))) {
                this.flowbox.allowCookies = JSON.parse(userAllowedSaveCookie)["1"] === 1;
            }

            if (config.flowbox.showTagBar) {
                _.each(this.flowbox.tags, function(tag) {
                    tagData.push({label: tag, checked: true});
                    activeTags.push(tag);
                });
                activeTags.subscribe(this.updateFlow, this, 'arrayChange');
            }

            this._debug('Flowbox: component init', this);

            return this;
        },

        /**
         * Initialize flow
         * @param elem
         */
        initFlow: function (elem) {
            var flowElement = elem.querySelector('.flowbox .flow');
            flowElement.id = _.uniqueId(`flowbox-${this.flowbox.flow}-container-`);;
            this.flowbox.container = `#${flowElement.id}`;

            var flowConfig = _.pick(this.flowbox, flowKeys);

            var interval = setInterval(function() {
                if (_.isFunction(window.flowbox)) {
                    clearInterval(interval);
                    this._debug('Flowbox: flow init', flowConfig);
                    window.flowbox('init', flowConfig);
                }
            }.bind(this), 0.1);
        },

        /**
         * Update flow
         * @param options
         */
        updateFlow: function () {
            var flowConfig = _.pick(this.flowbox, flowKeys);
            flowConfig.tags = activeTags();
            this._debug('Flowbox: flow update', flowConfig);
            window.flowbox('update', flowConfig);
        },

        /**
         * Add click handler for tag bar checkbox element
         */
        addTagBarCheckboxHandler: function (elem, tag) {
            elem.addEventListener('click', function(event) {
                for (var key in tagData) {
                    if (tagData.hasOwnProperty(key) && tagData[key].label === tag) {
                        tagData[key].checked = event.currentTarget.checked;
                        activeTags.removeAll()
                        _.each(_.filter(tagData,function(tag) {
                            return tag.checked;
                        }), function (tag) {
                            activeTags.push(tag.label);
                        });
                        break;
                    }
                }
            });
        },
    });
});

