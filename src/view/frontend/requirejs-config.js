/**
 * Copyright © Itonomy BV. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    paths: {
        'lib-flowbox': 'https://connect.getflowbox.com/flowbox',
        'lib-flowbox-checkout': 'https://connect.getflowbox.com/bzfy-checkout',
    },
    shim: {
        'flowbox': {
            deps: ['lib-flowbox'],
        },
        'flowbox-checkout': {
            deps: ['lib-flowbox-checkout'],
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
