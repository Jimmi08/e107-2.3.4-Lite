[![e107 Content Management System](eimages/logoHD.png)](https://e107.org)
=================

This is custom version of e107 CMS. Don't use it. It is public to be able syncing via plugin. 

Some plugins are moved to separate repos:

[e107 Alt Auth Plugin](https://github.com/Jimako-e107-plugins/alt_auth) 

[e107 Forum Plugin](https://github.com/Jimako-e107-plugins/forum)
 
[e107 Featurebox Plugin](https://github.com/Jimako-e107-plugins/featurebox)

[e107 Download Plugin](https://github.com/Jimako-e107-plugins/download)

[e107 RSS Plugin](https://github.com/Jimako-e107-plugins/rss_menu)

[e107 Featurebox Plugin](https://github.com/Jimako-e107-plugins/featurebox)

[e107 Chatbox Plugin](https://github.com/Jimako-e107-plugins/chatbox)

[e107 SP Gallery Plugin](https://github.com/Jimako-e107-plugins/e107-spgallery-plugin)

[e107 FAQs Plugin](https://github.com/Jimako-e107-plugins/faqs)

[e107 NewsFeed Plugin](https://github.com/Jimako-e107-plugins/newsfeed)



**[e107][1]** is a free and open-source content management system (CMS) which allows you to manage and publish your content online with ease. Developers can save time in building websites and powerful online applications. Users can avoid programming completely! Blogs, websites, intranets – e107 does it all. 

## Table of Contents

   * [e107 Content Management System](README.md)
      * [Table of Contents](#table-of-contents)
      * [Requirements](#requirements)
         * [Minimum](#minimum)
         * [Recommended](#recommended)
      * [Installation](#installation)
         * [Standard Installation](#standard-installation)
         * [Git Installation (developer version)](#git-installation-developer-version)
      * [Reporting Bugs](#reporting-bugs)
      * [Contributing to Development](#contributing-to-development)
      * [Donations](#donations)
      * [Support](#support)
      * [License](#license)

## Requirements

   ### Minimum

   * A web server (Apache or Microsoft IIS) running PHP 5.6 or newer
   * MySQL 4.x or newer, or MariaDB
   * FTP access to your web server and an FTP client (such as FileZilla)
   * Username and password to your MySQL database

   ### Recommended

   * Apache 2.4 or newer on Linux
   * PHP 7.4 or newer
   * MySQL 5.6 or newer, or MariaDB 10.3 or newer
   * A registered domain name
   * Access to a server control panel (such as cPanel)


## Installation 

### Standard Installation

1. [Download e107](https://e107.org/download).
2. Unzip/Extract the compressed file onto your desired web root.
   This is often a folder called `public_html`. 
3. Point your browser to the `install.php` script (e.g., `https://example.com/subfolder/install.php`)
4. Follow the installation wizard in your browser.



### Git Installation (developer version)

1. Run the following commands, replacing '~' with your document root (the parent of `public_html`) and xxx:xxx is the intended owner of your e107 files.
   ```
   cd ~
   git clone https://github.com/e107inc/e107.git public_html	
   chown -R xxx:xxx public_html 
   ```    
2. Point your browser to the `install.php` script (e.g., `https://example.com/subfolder/install.php`)
3. Follow the installation wizard in your browser.



## Reporting Bugs

Be sure you are using the most recent version of e107 prior to reporting an issue.
You may report any bugs and make feature requests [e107's GitHub Issues page](https://github.com/e107inc/e107/issues).



## Contributing to Development

* Please submit 1 pull request for each GitHub issue you work on. 
* Make sure that only the lines you have changed actually show up in a file-comparison (diff).
  Some text editors alter every line; this should be avoided. 
* It is recommended to configure `git pull` to rebase on the master branch by default to avoid unnecessary merge commits.  You can set this up in your copy of the repo's `.git/config` file like so:
  ```
  [branch "master"]
    rebase = true
  ``` 
* See the [CONTRIBUTING](.github/CONTRIBUTING.md) document for a tutorial on getting started.

## Donations
If you like e107 and wish to help it to improve, please consider making a small donation.

* PayPal: donate (at) e107.org



## Support
Having trouble getting e107 up and running? Something not working the way you think it should? Unfortunately we don't have time to maintain a full e107 support community ourselves, but there are a few ways to get help.

* If you think you have found a bug, then please see the section below on Reporting Bugs.
* If you need help with how to use e107 or a development question (such as how to create a theme or plugin) - please see our [docs](https://e107.org/developer-manual).
* You can also seek assistance in the [Github Discussions](https://github.com/e107inc/e107/discussions) area where you can get friendly community support from other users.
* For real-time technical chat, and community support, please visit us on [Gitter](https://gitter.im/e107inc/e107). 
* For other comments, please use our official community presences on Facebook, and Twitter as well as unofficial community presences on Google+ and Reddit. 



## License

e107 is released under the terms and conditions of the GNU General Public License (http://www.gnu.org/licenses/gpl.txt)

  [1]: https://e107.org
