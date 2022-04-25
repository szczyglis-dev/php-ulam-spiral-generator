# [PHP] Ulam Spiral Generator
PHP **7.2.5+**, current release: **1.2.1** build 2022-04-25

Mathematic Ulam spiral generator and renderer with programmable callbacks written in PHP.

## Live Demo: https://szczyglis.dev/ulam-spiral-generator

## What is Ulam spiral?
from https://en.wikipedia.org/wiki/Ulam_spiral:
> The Ulam spiral or prime spiral is a graphical depiction of the set of prime numbers, devised by mathematician Stanisław Ulam in 1963 and popularized in Martin Gardner's Mathematical Games column in Scientific American a short time later. It is constructed by writing the positive integers in a square spiral and specially marking the prime numbers. [...]

![300px-Ulam_1](https://user-images.githubusercontent.com/61396542/75210085-6d55ee80-5780-11ea-86fd-ab6bcabdab88.png)


## How to install:
```
composer require szczyglis/php-ulam-spiral-generator
``` 

## Features:

- Ulam spiral matrix builder working with any dataset
- Built in on-screen spiral renderer (as HTML table or raw data)
- For standalone and external usage (it is only one PHP class)
- Easy to use
- Programmable callbacks for in rows and columns number highlighting and counting 
- Javascript-based real-time rows/columns/crosses highlighter


## Usage example:
```php
<?php
// app.php

require __DIR__ . '/vendor/autoload.php';

use Szczyglis\UlamSpiralGenerator\UlamSpiral;

// configuration
$config = [ 
	'raw' => false, // if true then displays raw spiral (without CSS)
	'append_css' => true, // enables CSS stylizing
	'append_js' => true, // enables JS features
	'no_append_jquery' => false, // disables jQuery script appending if true
	'counters_mode' => 'count', // sets counters mode (sum of values or occurencies count)
	'row_counters' => true, // enables vertical counters
	'col_counters' => true, // enables horizontal counters
	'cell_width' => 35, // sets width of cell in pixels,
	'cell_height' => 35, // sets height of cell in pixels
	'cell_font_size' => 12, // sets font size in pixels
];

$dataset = range(1, 1000); // create dataset

$ulam = new UlamSpiral($config); // create new generator
$ulam->setDataset($dataset); // define dataset
$ulam->addCounter('sum', function($value) { // add custom callbacks for counters ( optional )
	return true;
});
$ulam->addCounter('prime', function($value) {
	if (is_integer($value)) {						
		if (UlamSpiral::isPrime($value)) {
			return true;		
		}						
	}	
});
$ulam->addMarker('prime', function($value) { // add custom callbacks for markers ( optional )
	if (is_integer($value)) {						
		if (UlamSpiral::isPrime($value)) {
			return '#e9e9e9';		
		}						
	}	
});

$ulam->buildMatrix(); // build Ulam spiral matrix
echo $ulam->render(); // render spiral

$matrix = $ulam->getMatrix(); // returns spiral's matrix

```
You can use any PHP array filled by numbers/or chars in `$ulam->dataset`, all values from array will be placed on spiral.

After execute`$ulam->buildMatrix()`, the matrix created by this method will be available in `$ulam->matrix` array, x and y coords accorded to placed on spiral values will be available in `$ulam->coords` array. You have access to matrix with `$ulam->getMatrix()` method.

## Screenshots:

CSS-styled version:

![gggg](https://user-images.githubusercontent.com/61396542/75218510-bfa40900-579a-11ea-9370-ee434dc0c48f.png)


Raw version:

![spiral_raw](https://user-images.githubusercontent.com/61396542/75210792-66c87680-5782-11ea-8dc5-8417e59288a1.png)



## Repository includes:

- `src/UlamSpiral.php` - base class

- `example.php` - usage example

## Configuration:

You can configure generator by creating `$config` array and put it into constructor. 

All keys in an array are described below:

`raw` (bool) - `[true|false]` if true then displays raw spiral (without CSS), default: `false`

`append_css` (bool) - `[true|false]` enables CSS, default: `true`

`append_js` (bool) - `[true|false]` enables JS, default: `true`

`no_append_jquery` (bool) `[true|false]` - disables jQuery script appending, default: `false`

`counters_mode` (string) `[sum|count]` - sets counters mode (sum of values or occurencies count), default: `count`

`row_counters` (bool) `[true|false]` - enables vertical counters, default: `true`

`col_counters` (bool) `[true|false]` - enables horizontal counters, default: `true`

`cell_width` (int) - sets width of cell in pixels, default: `35`

`cell_height` (int) - sets height of cell in pixels, default: `35`

`cell_font_size` (int) - sets font size in pixels, default: `15`


## Defining custom callbacks:

### Numbers highlighting

You can create your own marker callback for highlight specified numbers (e.g. prime numbers, even numbers, numbers greater than specified one, etc.). Callback takes one argument with current number and must return `HTML color code` with which to highlight. If callback returns `null` or `false` then number will not be affected. You can create as many markers as you like, each one for a different number type.

Example shows how to create marker callback for even numbers:

```php
$ulam = new UlamSpiral();
$ulam->addMarker('even', function($value) {					
	if (is_integer($value)) {
		if ($value %2 == 0) {
			return '#e9e9e9';		
		}	
	}
});
```

### Screenshot with even numbers highlighted:

![mark_even](https://user-images.githubusercontent.com/61396542/75211118-4c42cd00-5783-11ea-94ad-4fc9075d5ccc.png)

### Screenshot with prime numbers highlighted:

![mark_prime](https://user-images.githubusercontent.com/61396542/75211085-359c7600-5783-11ea-932f-dba29e17c94c.png)


### Numbers counters per row/column

Counter callbacks are for creating counters in spiral headers (horizontal and vertical). Counters can count specific numbers in row or column and display a result in row or column header. There are 2 types of counters: `count` and `sum`. You can choose behaviour in config. First type - `count` counts all occurencies of specified type of numbers in row or column, second type: `sum` displays sum of their values. If callback returns `true` then number will be affected by counter. you can create as many counters as you like, each one for a different number type. 

Example shows how to create counter callback for even numbers:

```php
$ulam = new UlamSpiral();
$ulam->addCounter('even', function($value) {					
	if (is_integer($value)) {
		if ($value %2 == 0) {
			return true;		
		}	
	}
});

```
### Screenshot with even numbers counted in header:

![count_even](https://user-images.githubusercontent.com/61396542/75210943-d179b200-5782-11ea-8a7a-76ef1133fc90.png)


### Screenshot with prime numbers counted in header:

![nnnn](https://user-images.githubusercontent.com/61396542/75220518-e6187300-579f-11ea-9645-6911c925f0eb.png)


## Highlighting rows, columns and crosses on mouse hover is also included:

![hhhhhh](https://user-images.githubusercontent.com/61396542/75211828-77c6b700-5785-11ea-9161-a2de9d80500d.png)


### Changelog 

- `1.2` -- package was added to packagist (2022-04-23)

- `1.2.1` -- updated PHPDoc (2022-04-25)

### Ulam Spiral Generator is free to use but if you liked then you can donate project via BTC: 

**14X6zSCbkU5wojcXZMgT9a4EnJNcieTrcr**

or by PayPal:
 **[https://www.paypal.me/szczyglinski](https://www.paypal.me/szczyglinski)**


**Enjoy!**

MIT License | 2022 Marcin 'szczyglis' Szczygliński

https://github.com/szczyglis-dev/php-ulam-spiral-generator

https://szczyglis.dev/ulam-spiral-generator

Contact: szczyglis@protonmail.com
