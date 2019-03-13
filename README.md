# This Magento 1 extension is orphaned, unsupported and no longer maintained.

If you use it, you are effectively adopting the code for your own project.

Api2 Session Auth Adapter
=========================
This Magento extension is only interesting to developers implementing a webapp running on the same host as the Magento instance.  
It enables the use of regular customer sessions for authentication instead of having to use OAuth.  

Facts
-----
- version: check the [config.xml](https://github.com/Vinai/VinaiKopp_Api2SessionAuthAdapter/blob/master/app/code/community/VinaiKopp/Api2SessionAuthAdapter/etc/config.xml)
- stability: dev
- extension key: VinaiKopp_Api2SessionAuthAdapter
- Magento Connect 1.0 extension key: - none -
- Magento Connect 2.0 extension key: - none -
- [extension on GitHub](https://github.com/Vinai/VinaiKopp_Api2SessionAuthAdapter)
- [direct download link](https://github.com/Vinai/VinaiKopp_Api2SessionAuthAdapter/zipball/master)

Description
-----------
This Magento extension is only interesting to developers implementing a webapp running on the same host as the Magento instance.  
It enables the use of regular customer sessions for authentication instead of having to use OAuth. 

Usage
-----
Simply install the extension and provide some mechanism for customers to log in.

Compatibility
-------------
- Magento >= 1.7

Installation Instructions
-------------------------
If you are using the Magento compiler, disable compilation before the installation, and after the module is installed, you need to run the compiler again.

Refresh the configuration cache after installation.  
The extension has no user interface components and offers no configuration options.

Uninstallation
--------------
To uninstall this extension simply remove all files, clear the configuration cache.  
If you are using compilation, recompile after removing the modules' files.

Support
-------
If you have any issues with this extension, open an issue on GitHub (see URL above)

Contribution
------------
Any contributions are highly appreciated. The best way to contribute code is to open a
[pull request on GitHub](https://help.github.com/articles/using-pull-requests).

Developer
---------
Vinai Kopp  
[http://www.netzarbeiter.com](http://www.netzarbeiter.com)  
[@VinaiKopp](https://twitter.com/VinaiKopp)

Licence
-------
[OSL - Open Software Licence 3.0](http://opensource.org/licenses/osl-3.0.php)

Copyright
---------
(c) 2014 Vinai Kopp
