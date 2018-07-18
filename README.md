# Loft Library for Drupal 8

**DO NOT ENABLE THIS, IT IS NOT A MODULE.**

The Drupal 8 version of this is not really a module, but an entry in your root composer.json

Add the following:

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
