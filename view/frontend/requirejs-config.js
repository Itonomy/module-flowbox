/**
 * Copyright © Itonomy BV. All rights reserved.
 * See LICENSE.md for license details.
 */

var config = {
    paths: {
        'lib-flowbox': 'https://connect.getflowbox.com/flowbox',
        'lib-flowbox-checkout': 'https://connect.getflowbox.com/bzfy-checkout',
    },
    shim: {
        'flowbox': {
            deps: ['lib-flowbox'],
            exports: 'default'
        },
        'flowbox-checkout': {
            deps: ['lib-flowbox-checkout'],
            exports: 'default'
        }
    },
    config: {
        text: {
            headers: {
                'X-Requested-With': 'XMLHttpRequest' // CORS
            }
        }
    },
};
