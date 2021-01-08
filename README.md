Itonomy Flowbox Extension
=====================
Flowbox integration for Magento2

Facts
-----
- version: 1.2.4
- extension key: Itonomy_Flowbox

Description
-----------
This extension integrates [Flowbox](https://getflowbox.com) default, dynamic-tag and dynamic-product flows into your Magento2 store as CMS widgets. These widgets have flexible configuration and can easily applied to the required parts of your store.

Requirements
-------------
- PHP >= 7.3.0
- Magento 2 CE/EE >= 2.4

Installing
-------------
### Using composer:
1. Navigate to the root directory of your Magento2 installation;
2. Execute `composer require itonomy/module-flowbox`;
3. Execute `php bin/magento setup:upgrade`;

### Using a zip archive:
1. Navigate to the root directory of your Magento2 installation;
2. Extract the archive's contents to `app/code/Itonomy/Flowbox`;
3. Execute `php bin/magento setup:upgrade`

Uninstalling
-------------
### Using composer:
1. Navigate to the root directory of your Magento2 installation;
2. Execute `composer remove itonomy/module-flowbox`;
3. Execute `php bin/magento setup:upgrade`;

### Using a zip file:
1. Navigate to the root directory of your Magento2 installation;
2. Remove the directory `app/code/Itonomy/Flowbox` and all of its contents;
3. Execute `php bin/magento setup:upgrade`

## Configuration
The module is configured in two ways.

First, basic settings related to the general functioning of the module such as the API key needed for the checkout script can be configured in the backend configuration page.

To access this page, first click on `Stores` > `Configuration` (under the heading `Settings`) in the main menu to navigate to the system configuration page. From there, open the `Catalog` tab. You should now see a tab named `Flowbox`, which you can click on to open up the Flowbox configuration page.

Further per-widget configuration is done when creating or editing a widget of the `Flowbox Flow` type.

Support
-------------
For inquiries about this module and requests for support please send us an [e-mail](mailto://support@itonomy.nl) 

Developer
-------------
[Daniel R. Azulay](mailto://daniel.azulay@itonomy.nl) for [Itonomy](http://www.itonomy.nl)

see DEVELOPING.md

License
-------------
[MIT](http://opensource.org/licenses/mit)

see LICENSE.md

Copyright
-------------
(c) 2020 Itonomy BV
