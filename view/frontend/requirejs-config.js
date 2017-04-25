var config = {
    paths: {
        'subscriber': 'Space48_SubTech/js/subscriber'
    },

    shim:{
        'subscriber':{
            'deps':['Magento_Customer/js/customer-data', 'jquery']
        }
    }
};