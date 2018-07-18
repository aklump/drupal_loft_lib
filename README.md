# Loft Library for Drupal 8

**DO NOT ENABLE THIS, IT IS NOT A MODULE.**

The Drupal 8 version of this is not really a module, but an entry in your root _composer.json_

Add the following to the _repositories_ section of the root _composer.json_

    {
        "repositories": [
            {
                "type": "vcs",
                "url": "/Users/aklump/Code/Packages/php/loft_php_lib"
            }
        ]
    }
    
Then require it like this:

    composer require aklump/loft_php_lib

That's it!
