![image editor logo](https://user-images.githubusercontent.com/38328740/64390638-0733ae80-d01c-11e9-840d-2c7eede5d811.png)
[![License](https://img.shields.io/badge/license-MIT-blue.svg)](/LICENSE)
***

# Image Editor

ImageEditor is a php library for easily creating / editing images. It uses the GD library for file generation.

# Dependencies

* PHP 5.3 +
* [GD lib](https://www.php.net/manual/pt_BR/book.image.php)

# Usage

Using the library is very simple. Just include the autoload and you're done!

```php
require "ImageEditor/autoload.php";

use ImageEditor\EditImage as EditImage;
```
To edit and create images you need to use the `ImageEditor` class starting with the static method `from` and passing the image that will be used as a model.
```php
EditImage::from('images/zacarias.jpg') 
```
then use the functions that will be applied to the image and save it
```php
EditImage::from('images/zacarias.jpg')
->negate() // function
->save(); // save
```
[See the wiki for more informations](../../wiki/Using-functions)


# Example

Imagine you have a picture like this:

![zacarias](https://user-images.githubusercontent.com/38328740/64267804-62c34680-cf0d-11e9-970d-24e6d07d5319.jpg)

So you need to edit it and create 2 more photos following the rules:

* There will be 3 images in total (big, mid, small)
* All of them need to be clipped.
* Big will have a width of 300px
* Mid needs to be clearer and width of 200px
* The small will be in grayscale and width of 100px

How will I do this using this library?

```php
EditImage::
    from('images/zacarias.jpg', 'big') // create first image with alias 'big' using image in 'images' folder
    ->copy(['mid', 'small'])           // and more 2 images with alias is 'mid' and 'small'
    ->crop('center center 300x*')      // crop the image in the center using 300px width
    ->use('resize',[         // use function resize in ...
        'mid' => '200x*',    // mid => 200px in width
        'small' => '100x*'   // small => 100px in width
    ])
    ->brightness(100, 'mid') // increase brightness to 100 in mid
    ->grayscale('small')     // tranform small in grayscale
    ->save();                // save imagess
```

The result is this ...

![zacarias_big](https://user-images.githubusercontent.com/38328740/64267805-62c34680-cf0d-11e9-8146-1dd1642e43aa.jpg)
![zacarias_mid](https://user-images.githubusercontent.com/38328740/64267806-62c34680-cf0d-11e9-9373-64cf1ee3cf28.jpg)
![zacarias_small](https://user-images.githubusercontent.com/38328740/64267807-62c34680-cf0d-11e9-9d44-988fff909650.jpg)

# Be happy!

![zacarias_happy](https://user-images.githubusercontent.com/38328740/64267938-a28a2e00-cf0d-11e9-8624-2ae68163a3b2.gif)


