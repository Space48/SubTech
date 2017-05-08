# Sub2Tech

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
