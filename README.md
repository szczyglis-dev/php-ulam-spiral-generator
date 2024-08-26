PHP **7.2.5+, 8.0+**, current release: **1.2.2** build 2022-04-28

# Ulam Spiral Generator

A mathematical Ulam spiral generator and renderer with programmable callbacks written in PHP.

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

- Ulam spiral matrix builder compatible with any dataset.
- Built-in on-screen spiral renderer (as an HTML table or raw data).
- Suitable for standalone and external usage (consists of a single PHP class).
- Programmable callbacks for highlighting and counting numbers in rows and columns.
- JavaScript-based real-time highlighter for rows, columns, and crosses.
- Easy to use.


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
You can use any PHP array filled with numbers or characters in the `$ulam->dataset`. All values from the array will be placed in the spiral.

After executing `$ulam->buildMatrix()`, the matrix created by this method will be available in the `$ulam->matrix` array. The x and y coordinates corresponding to the values placed in the spiral will be available in the `$ulam->coords` array. You can access the matrix using the `$ulam->getMatrix()` method.

## Screenshots:

CSS-styled version:

![gggg](https://user-images.githubusercontent.com/61396542/75218510-bfa40900-579a-11ea-9370-ee434dc0c48f.png)


Raw version:

![spiral_raw](https://user-images.githubusercontent.com/61396542/75210792-66c87680-5782-11ea-8dc5-8417e59288a1.png)



## Repository includes:

- `src/UlamSpiral.php` - Base class

- `example.php` - Usage example

## Configuration:

You can configure the generator by creating a `$config` array and passing it into the constructor.

All keys in the array are described below:

- `raw` (bool) - `[true|false]` If `true`, displays a raw spiral (without CSS). Default: `false`
- `append_css` (bool) - `[true|false]` Enables CSS. Default: `true`
- `append_js` (bool) - `[true|false]` Enables JavaScript. Default: `true`
- `no_append_jquery` (bool) - `[true|false]` Disables appending of the jQuery script. Default: `false`
- `counters_mode` (string) - `[sum|count]` Sets the counters mode (sum of values or count of occurrences). Default: `count`
- `row_counters` (bool) - `[true|false]` Enables vertical counters. Default: `true`
- `col_counters` (bool) - `[true|false]` Enables horizontal counters. Default: `true`
- `cell_width` (int) - Sets the width of each cell in pixels. Default: `35`
- `cell_height` (int) - Sets the height of each cell in pixels. Default: `35`
- `cell_font_size` (int) - Sets the font size in pixels. Default: `15`


## Defining custom callbacks:

### Number Highlighting

You can create your own marker callback to highlight specific numbers (e.g., prime numbers, even numbers, numbers greater than a specified value, etc.). The callback takes one argument, which is the current number, and must return an HTML color code to use for highlighting. If the callback returns `null` or `false`, the number will not be affected. You can create as many markers as you like, each for a different type of number.

The following example demonstrates how to create a marker callback for even numbers:

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

### Screenshot with Even Numbers highlighted:

![mark_even](https://user-images.githubusercontent.com/61396542/75211118-4c42cd00-5783-11ea-94ad-4fc9075d5ccc.png)

### Screenshot with Prime Numbers highlighted:

![mark_prime](https://user-images.githubusercontent.com/61396542/75211085-359c7600-5783-11ea-932f-dba29e17c94c.png)


### Number Counters per Row/Column

Counter callbacks are used for creating counters in the spiral headers (horizontal and vertical). Counters can count specific numbers in a row or column and display the result in the row or column header. There are two types of counters: `count` and `sum`. You can choose the behavior in the config. 

The first type - `count` - counts all occurrences of a specified type of number in a row or column. The second type - `sum` - displays the sum of their values. If the callback returns `true`, then the number will be affected by the counter. You can create as many counters as you like, each for a different type of number.

The following example shows how to create a counter callback for even numbers:

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
### Screenshot with Even Numbers counted in header:

![count_even](https://user-images.githubusercontent.com/61396542/75210943-d179b200-5782-11ea-8a7a-76ef1133fc90.png)


### Screenshot with Prime Numbers counted in header:

![nnnn](https://user-images.githubusercontent.com/61396542/75220518-e6187300-579f-11ea-9645-6911c925f0eb.png)


## Highlighting Rows, Columns, and Crosses on Mouse Hover:

![hhhhhh](https://user-images.githubusercontent.com/61396542/75211828-77c6b700-5785-11ea-9161-a2de9d80500d.png)


### Changelog 

- `1.2.0` - Package added to Packagist (2022-04-23)
- `1.2.1` - Updated PHPDoc (2022-04-25)
- `1.2.2` - Updated composer.json (2022-04-28)

--- 
**Ulam Spiral Generator is free to use, but if you like it, you can support my work by buying me a coffee ;)**

https://www.buymeacoffee.com/szczyglis

**Enjoy!**

MIT License | 2022 Marcin 'szczyglis' Szczygliński

https://github.com/szczyglis-dev/php-ulam-spiral-generator

https://szczyglis.dev/ulam-spiral-generator

Contact: szczyglis@protonmail.com
