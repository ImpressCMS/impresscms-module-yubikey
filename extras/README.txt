Important! Select the right checklogin.php
==========================================

For Yubikey to operate you must overwrite the checklogin.php file of your ImpressCMS installation as described in the manual. There are different versions of this file (currently 2) for different versions of ImpressCMS. They are labeled:

* 1.3.1_&_1.3.2_checklogin.php (for ImpressCMS V1.3.1 and 1.3.2).
* 1.3.3_to_1.3.6_checklogin.php (for ImpressCMS V1.3.3 to 1.3.6.1).

Select the appropriate version for your site and then RENAME IT to checklogin.php before overwriting /include/checklogin.php on your site.

If you use the wrong version, the module probably won't work.