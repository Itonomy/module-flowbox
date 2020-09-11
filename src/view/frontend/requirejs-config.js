var config = {
    map: {
        '*': {
            'lib-flowbox': 'https://connect.getflowbox.com/flowbox.js',
            'flowbox': 'Itonomy_Flowbox/js/flowbox',
        }
    },
    shim: {
        'flowbox': {
            deps: ['lib-flowbox'],
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
