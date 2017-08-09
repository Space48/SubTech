# Sub2Tech
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Space48/SubTech/badges/quality-score.png?b=master&s=6224692d0afa0464aad9672ffe48e8b94c9dd0e0)](https://scrutinizer-ci.com/g/Space48/SubTech/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/Space48/SubTech/badges/build.png?b=master&s=eec15aaa403ebaaa6fa18e189f303ba8933985ec)](https://scrutinizer-ci.com/g/Space48/SubTech/build-status/master)
[![Code Coverage](https://scrutinizer-ci.com/g/Space48/SubTech/badges/coverage.png?b=master&s=f7cfd44ebf434797310200bb30ad18d7794b79dc)](https://scrutinizer-ci.com/g/Space48/SubTech/?branch=master)

This module will send various data to the Sub2Tech system via Javascript calls that are invoked on every page load. This includes the following:


- Email address when subscribing to newsletter
- Customer data when registering for account
- Customer data when logging into account
- Customer data when updating address in account area
- Basket data when adding to cart
- Basket data when updating cart
- Basket data when removing from cart
- Order data when completing an order


## Installation

**Manual**

To install this module copy the code from this repo to `app/code/Space48/SubTech` folder of your Magento 2 instance, then you need to run php `bin/magento setup:upgrade`

**Composer**:

From the terminal execute the following:

`composer config repositories.space48-subtech vcs git@github.com:Space48/SubTech.git`

then

`composer require "space48/subtech:{module-version}"`

## How to use it
Once installed, go to the admin area and go to `Stores -> Configuration -> Space48 -> Sub2Tech` and `enable` the extension. Add the licence key into the Licence Key field (provided by Sub2Tech)
