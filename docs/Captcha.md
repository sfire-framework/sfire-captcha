# sFire Captcha

- [Introduction](#introduction)
- [Requirements](#requirements)
- [Installation](#installation)
- [Setup](#setup)
    - [Namespace](#namespace)
    - [Instance](#instance)
    - [Configuration](#configuration)
- [Usage](#usage)
    - [Setting a background image](setting-a-background-image)
    - [Setting a font file](setting-a-font-file)
    - [Setting a font color](setting-a-font-color)
    - [Setting the font size](setting-the-font-size)
    - [Setting text characters angle](setting-text-characters-angle)
    - [Setting custom noise level](setting-custom-noise-level)
    - [Setting custom captcha text](setting-custom-captcha-text)
    - [Retrieving the captcha text](retrieving-the-captcha-text)
    - [Generating the captcha image](#generating-the-captcha-image)
- [Examples](#examples)
- [Notes](#notes)
    - [Chaining](#chaining)
    - [Generated text similar character exclusion](#generated-text-similar-character-exclusion)



## Introduction

CAPTCHA stands for Completely Automated Public Turing test to tell Computers and Humans Apart. In other words, CAPTCHA determines whether the user is real or a spam robot. CAPTCHAs stretch or manipulate letters and numbers, and rely on human ability to determine which symbols they are. sFire Captcha is an easy-to-use interface for creating those captcha images.



## Requirements

- [GD Library](https://www.php.net/manual/en/book.image.php)
- Freetype support (--with-freetype-dir=DIR ) for [imagettfbbox](https://www.php.net/imagettfbbox)



## Installation

Install this package using [Composer](https://getcomposer.org/):
```shell script
composer require sfire-framework/sfire-captcha
```



## Setup

### Namespace
```php
use sFire\Captcha\Captcha;
```



### Instance

```php
$captcha = new Captcha();
```



### Configuration

There are no configuration settings needed for this package.



## Usage

#### Setting a background image
A background image is required for generating a captcha image. This can be done with the `setBackgroundImage()` method. The image is usually a distorted background image, making it harder for robots to guess the generated text, and should be a locally saved file of the type "jpg", "jpeg" or "png".

##### Syntax
```php
$captcha -> setBackgroundImage(string $image): self;
```

##### Example: Setting a background image
```php
$captcha -> setBackgroundImage('/var/www/data/background.jpg');
```

To retrieve the background image you can use the `getBackgroundImage()` method.



#### Setting a font file
A custom font is required for generating a captcha image. This can be done by using the `setFont()` method. This file should be a [TrueType](https://en.wikipedia.org/wiki/TrueType) font file.

##### Syntax
```php
$captcha -> setFont(string $image): self;
```

##### Example: Setting a font
```php
$captcha -> setFont('/var/www/data/font.ttf');
```

To retrieve the font file you can use the `getFont()` method.



#### Setting a font color
Once you set a font, you can give the generated text a color by using the `setFontColor()` method. This method takes an RGB or Hexadecimal color.

##### Syntax
```php
$captcha -> setFontColor($r, int $g = null, int $b = null): self;
```

##### Example 1: Setting a font color using RGB values
```php
$captcha -> setFontColor(255, 243, 52);
```

##### Example 2: Setting a font color using a hexadecimal value
```php
$captcha -> setFontColor('#005278');
$captcha -> setFontColor('005278');
```

To retrieve the font color you can use the `getFontColor()` method.



#### Setting the font size
Once you set a font, you can set font size for the generated text by using the `setFontSize()` method. You can pass a minimum and maximum font size. All characters will have a font size between these sizes.

##### Syntax
```php
$captcha -> setFontSize(int $min, int $max = null): self;
```

##### Example 1: Setting a font size range
```php
$captcha -> setFontSize(10, 15);
```

##### Example 2: Setting a fixed font size
```php
$captcha -> setFontSize(10);
```

To retrieve the font size you can use the `getFontSize()` method.



#### Setting text characters angle
Once you set a font, you can set a minimum and maximum angle for the text by using the `setFontAngle()` method. This will rotate each character in an angle (degree) between the given minimum and maximum.

##### Syntax
```php
$captcha -> setFontAngle(int $min, int $max = null): self;
```

##### Example 1: Setting a font angle range
```php
$captcha -> setFontAngle(-20, 20);
```

##### Example 2: Setting a fixed font angle
```php
$captcha -> setFontAngle(10);
```

To retrieve the font angle you can use the `getFontAngle()` method.



#### Setting custom noise level
You can use the `setNoise()` method to custom add more noise to the captcha image. The higher the given number, the more noise the captcha image will have.

##### Syntax
```php
$captcha -> setNoise(int $level): self;
```

##### Example: Setting noise to the captcha image
```php
$captcha -> setNoise(50);
```

To retrieve the noise level you can use the `getNoise()` method.



#### Setting custom captcha text

By default sFire Captcha will generate a custom text, but you can manually overwrite this by using the `setText()` method.

##### Syntax
```php
$captcha -> setText(string $text): self;
```

##### Example: Setting a custom captcha text
```php
$captcha -> setText('8Jd52aX');
```

You may also use the built-in `generateText()` method for easy text generation. Using this method will automatically set the generated text as the used captcha text. You may specify the length of the text and an array with symbols that will be randomly used.
##### Syntax
```php
$captcha -> generateText(int $length = 5, array $characters = []): string;
```

##### Example: Generating captcha text
```php
$captcha -> generateText(); //Output similar to "8Fd53"
$captcha -> generateText(10); //Output similar to "8Fd53aXnR7"
$captcha -> generateText(5, ['a', 'b', 'c', 1, 2, 3]); //Output similar to "2b33a"
```



#### Retrieving the captcha text

To validate the captcha, you need to know what the used text is. This can be done with the `getText()` method, which will provide the generated or [custom text](#setting-custom-captcha-text).

##### Syntax
```php
$captcha -> getText(): ?string;
```

##### Example: Setting a custom captcha text
```php
$captcha -> setText('8Jd52aX');
```



#### Generating the captcha image

You can generate the captcha image by calling the `generate()` method. You can choose to save the file locally or output the image directly to the client/browser.

##### Syntax
```php
$captcha -> generate(string $file = null): void;
```

##### Example 1: Generate captcha image and output it to the client/browser
```php
$captcha -> generate();
```

##### Example 2: Generate captcha image and save it locally
```php
$captcha -> generate('/var/www/data/captcha.jpg'); //Save the captcha image as JPG
$captcha -> generate('/var/www/data/captcha.png'); //Save the captcha image as PNG
```



## Examples

### Generate and saving the captcha image
```php
$captcha = new Captcha();
$captcha -> setBackgroundImage('/var/www/data/captcha-bg.jpg');
$captcha -> setFont('/var/www/data/captcha.ttf');
$captcha -> generate('/var/www/data/captcha.jpg'); //Generate and save it locally

$text = $captcha -> getText();
```



### Generating and output captcha image directly to the client/browser

```php
$captcha = new Captcha();
$captcha -> generateText(8); //Generate a text with the length of 8
$captcha -> setBackgroundImage('/var/www/data/captcha-bg.jpg');
$captcha -> setFont('/var/www/data/captcha.ttf');
$captcha -> setAngle(-20, 20); //Setting the angle in degrees for individual characters 
$captcha -> setFontColor('#652d2b'); //Set the font color
$captcha -> setFontSize(15, 25); //Set a minimum and maximum font size
$captcha -> setNoise(50); //Add noise to the captcha image
$captcha -> generate(); //Generate and output it directly to the client/browser

$text = $captcha -> getText();
```



## Notes

### Chaining
Some of the provided methods may be chained together:
```php
$captcha -> setBackgroundImage('/var/www/data/captcha-bg.jpg') -> setFont('/var/www/data/captcha.ttf') -> generate();
```



### Generated text similar character exclusion

Generated text by default don't include similar characters like the letters "o" and "O" and the number "0".