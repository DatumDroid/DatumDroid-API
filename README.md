DatumDroid API
==============

DatumDroid API is basically an API written to support the [DatumDroid](https://play.google.com/store/apps/details?id=com.datumdroid.app) application.
It returns back JSON encoded results for various services like Feedzilla, Google Images, Guardian, Twitter, Youtube for a search query with per page and current page parameters passed through GET or POST.

You could then parse to display them.

An online version of this API exists on http://datumdroid.gaut.am/

Installation
------------

Just put the files in a directory on an Apache server with PHP5.2+ (requires JSON and SimpleXML).
Check `dd-config-sample.php` and `request.php` for possible configurations.
Must edits: `DD_URI` and `$dd_api_keys`.

Usage
-----

Call the website like http://datumdroid.gaut.am/?q=gautam+gupta&per_page=10&page=1&all=1
And JSON encoded results would be returned.

### Parameters ###

 * `q` -- Search query
 * `per_page` -- Items per page for each service. Max 50. Default 10.
 * `page` -- Page number to be requested. Default 1.
 * `<service>` -- The service to fetch results from, plain names (check filenames in `dd-includes`). Value would be the number of results to return or do not set. If set to 1 and `per_page` is also set, then `per_page` value is used instead.
 * `all` -- Whether to fetch results from all available services. 1 or do not set.
 * `debug` -- Whether to `print_r` results instead of `json_encode` and whether to display errors or not. You may want to install a JSON viewing browser extension instead of using debug for the first reason.
 * `supported_services` - Returns a JSON encoded (or `print_r`-ed array if `debug` is on) associative array of the supported services with keys being the plain names and values being the underscored names of the services.

### Services ###

 1. Feedzilla
 2. Google Images
 3. Google Stocks
 4. Google Weather
 5. Guardian
 6. Twitter
 7. Youtube

Contributing
------------

 1. Fork it.
 2. Create a branch (`git checkout -b my_datumdroid_api`)
 3. Commit your changes (`git commit -am "Added X Service"`)
 4. Push to the branch (`git push origin my_datumdroid_api`)
 5. Create an Issue with a link to your branch
 6. Enjoy a refreshing Diet Coke and wait

### To add a service ###

 1. Add `service.[name].php` in `dd-includes` like `service.twitter.php` having a class extending `DD_Search_Service` (copy from another service class file) and make necessary changes in the file.
 2. Add `[name]` => `[class_name]` in `$dd_services` array in `dd-config.php`
 3. Done, enjoy :)

### Notes ###
 * This project follows [WordPress Coding Standards](http://codex.wordpress.org/WordPress_Coding_Standards).
