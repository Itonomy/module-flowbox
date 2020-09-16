var config = {
    map: {
        '*': {
            'flowbox': 'https://connect.getflowbox.com/flowbox.js',
            'flowbox-flow': 'Itonomy_Flowbox/js/flow',
            'flowbox-tagbar': 'Itonomy_Flowbox/js/tagbar',
        }
    },
    shim: {
        'flowbox-flow': {
            deps: ['flowbox'],
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
