# Developer Guide

## Magento Marketplace
To package the module for Magento Marketplace, execute `make module` in the repository root. To clean up, run `make clean`. To repackage, run `make clean && make module`.

A Magento Marketplace compatible zip file of the latest tag that contains origin/master will be generated under a `pkg` directory in the repository root.

Because the latest tag containing origin/master is used, make sure the changes you need in your package are present in the origin/master branch.

Copyright
-------------
(c) 2020 Itonomy BV
