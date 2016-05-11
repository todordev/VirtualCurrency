Virtual Currency for Joomla! 
==========================
( Version 2.1 )
- - -

Virtual Currency is a Joomla! extension that provides functionality, API and resources that can be used to manage virtual currency and virtual goods on Joomla! websites.

##Documentation
You can find documentation on following pages.

[Documentation and FAQ] (http://itprism.com/help/111-virtual-currency-documentation)

[API documentation] (http://cdn.itprism.com/api/virtualcurrency/index.html)

##Download
You can [download Virtual Currency package] (http://itprism.com/free-joomla-extensions/ecommerce-gamification/virtual-currency-accounts-manager) from the website of ITPrism.

[Distribution repository](https://github.com/ITPrism/VirtualCurrencyDistribution)

##License
Virtual Currency is under [GPLv3 license] (http://www.gnu.org/licenses/gpl-3.0.en.html).

## About the code in this repository
This repository contains code that you should use to create a package. You will be able to install that package via [Joomla extension manager] (https://docs.joomla.org/Help25:Extensions_Extension_Manager_Install).

##How to create a package?
* You should install [ANT] (http://ant.apache.org/) on your PC.
* Download or clone [VirtualCurrency distribution] (https://github.com/ITPrism/VirtualCurrencyDistribution).
* Download or clone the code from this repository.
* Rename the file __build/example.txt__ to __build/antconfig.txt__.
* Edit the file __build/antconfig.txt__. Enter name and version of your package. Enter the folder where the source code is Virtual Currency distribution. Enter the folder where the source code of the package will be stored (the folder where you have saved this repository).
* Save the file __build/antconfig.txt__.
* Open a console and go in folder __build__.
* Type "__ant__" and click enter. The system will copy all files from distribution to the folder where you are going to build the installable package.

`
ant
`

##Contribute
If you would like to contribute to the project you should use [Virtual Currency distribution](https://github.com/ITPrism/VirtualCurrencyDistribution). That repository provides Joomla CMS + Virtual Currency.
You can clone it on your PC and install it on your local host. You should use it as development environment. You should use it to create branches, to add new features, to fix issues and to send pull request.