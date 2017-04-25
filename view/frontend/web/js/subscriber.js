/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'uiComponent',
    'Magento_Customer/js/customer-data',
    'jquery',
    "prototype"
], function (Component, customerData, jQuery, prototype) {
    'use strict';

    document.observe('dom:loaded', function() {

        var subscriberData = customerData.get('subscriber');
        var email = subscriberData().subscriber_email;

        if (email) {
            __s2tQ.push(['storeData', {'Email': email}]);
            console.log(email + ' sent to Sub2Tech');
            customerData.set('subscriber', []);

        }
    });

});