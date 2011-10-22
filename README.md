DatumDroid API
==============

DatumDroid API is basically an API written to support the [DatumDroid](http://datumdroid.com/) application.
It returns back JSON encoded results for various services like Feedzilla, Google Images, Google Stocks, Google Weather, Guardian, Twitter, Youtube for a search query with per page and current page parameters passed through GET or POST.

You could then parse to display them.

An online version of this API exists on http://api.datumdroid.com/

Installation
------------

Just put the files in a directory on an Apache server with PHP5.2+ (requires JSON).

Usage
-----

Call the website like api.datumdroid.com/?q=gautam+gupta&per_page=10&page=1&all=1
And JSON encoded results would be returned.

### Parameters ###

 * `q` - Search query
 * `per_page` - Items per page for each service. Max 50. Default 10.
 * `page` - Page number to be requested. Default 1.

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
