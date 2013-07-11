# PHP Crocodoc Demo
This code was created out of parts of the code used to build [DocumentLeads.com](http://documentleads.com)

## Setup
* Signup for an account on [Crocodoc.com](http://crocodoc.com) and grab your Key ID.
* Add that Key ID to two pages: `functions.php`
* Ensure that `documents` and `cache` are CHMOD'd to be writable.
* Most PHP files need to have a MySQL connection. So include your connection file, or connect to MySQL at the top of `functions.php` to make things easy for you.
* Register the path of your `webhook_crocodoc.php` in the Crocodoc control panel.
* Upload a new document via the `upload.php` file.
* Go to the `view.php` file in your browser. You should see the file and thumbnail once Crocodoc processes them.

Crocodoc PHP library is available [here](https://github.com/crocodoc/crocodoc-php).
I cannot remember where I found `DocumentManager.php`, but it was not written by me. 

P.S. Sorry for all the different files, when they could probably be consolidated to less. I kept things separate because in the real word situation, this is more than likely going to be how it's done (upload, view, webhook, etc. being separate).