[![License](https://img.shields.io/github/license/IsengardBiz/yubikey.svg?maxAge=2592000)](License.txt) 
	[![GitHub release](https://img.shields.io/github/release/IsengardBiz/yubikey.svg?maxAge=2592000)](https://github.com/IsengardBiz/yubikey/releases) 
		![This is ImpressCMS module](https://img.shields.io/badge/ImpressCMS-module-F3AC03.svg?maxAge=2592000)

# Yubikey

Enables 2-factor authentication using Yubikey hardware tokens

## How to install

Yubikey is installed as a regular ImpressCMS module, which means you should copy the complete `/yubikey` folder into the `/modules` directory of your website. Then, log in to your site as administrator, go to `System Admin > Modules`, look for the Yubikey icon in the list of uninstalled modules and click in the install icon. Follow the directions in the screen and you'll be ready to go.

## Requirements

1. This version of the Yubikey module is specific to ImpressCMS 1.3.x series (a version for the 1.2.x is also available). Do not install it on V2.0.
2. You need a Yubikey hardware token, they cost $25. You can buy these from [Yubico](http://yubico.com/). Do not buy them from a reseller. Sometimes you can find promotional codes online.
3. Your server must have cURL installed.
4. You must get a Client ID and API key from the Yubikey website (see below). The module will not work until you do (see set up, below).
5. A MANUAL is available in the `/extras` folder, or via a link in the control panel. DO NOT USE THIS MODULE UNTIL YOU HAVE READ IT.

## Set up

To operate the module you need to obtain a Yubikey client ID and secret API key from https://upgrade.yubico.com/getapikey/. This information must be entered in the module preferences. Only then will you be able to authenticate keys against the Yubico validation server.
