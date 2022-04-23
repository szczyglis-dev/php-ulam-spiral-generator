# [PHP] Ulam Spiral Generator
### PHP 5.6+ / PHP 7

Current version: 1.0

Mathematic Ulam spiral generator and renderer with programmable callbacks written in PHP.

## Live Demo: https://szczyglis.dev/ulam-spiral-generator

## What is Ulam spiral?
from https://en.wikipedia.org/wiki/Ulam_spiral:
> The Ulam spiral or prime spiral is a graphical depiction of the set of prime numbers, devised by mathematician Stanisław Ulam in 1963 and popularized in Martin Gardner's Mathematical Games column in Scientific American a short time later. It is constructed by writing the positive integers in a square spiral and specially marking the prime numbers. [...]

![300px-Ulam_1](https://user-images.githubusercontent.com/61396542/75210085-6d55ee80-5780-11ea-86fd-ab6bcabdab88.png)


## Features:

- Ulam spiral matrix builder working with any dataset
- Built in on-screen spiral renderer (as HTML table or raw data)
- For standalone and external usage (it is only one PHP class)
- Easy to use
- Programmable callbacks for numbers highlighting and counting in rows and columns
- Javascript-based real time rows/columns/crosses highlighter


## Usage example:
```
<?php

include __DIR__.'/UlamSpiral.php';


/* Create config (optional) */
$config = [
	'raw' => false,
	'append_css' => true,
	'append_js' => true,
	'no_append_jquery' => false,
	'counters_mode' => 'sum',
	'row_counters' => true,
	'col_counters' => true,
	'cell_width' => 35,
	'cell_height' => 35,
	'cell_font_size' => 12
];


/* Create new generator */
$ulam = new UlamSpiral($config);


/* Add dataset */
$ulam->dataset = range(1, 1000);


/* Add custom callbacks for counters and markers  (optional) */
$ulam->addCounter('sum', function($value) {
	return true;
});
$ulam->addCounter('prime', function($value) {
	if (is_integer($value)) {						
		if (UlamSpiral::isPrime($value)) {
			return true;		
		}						
	}	
});
$ulam->addMarker('prime', function($value) {
	if (is_integer($value)) {						
		if (UlamSpiral::isPrime($value)) {
			return '#e9e9e9';		
		}						
	}	
});

/* Build Ulam spiral matrix */
$ulam->buildMatrix();
/* $ulam->matrix is created now. */

/* Render spiral */
echo $ulam->render();

```
You can use any PHP array filled with numbers or chars in `$ulam->dataset`, all values from array will be placed on spiral.

After execute`$ulam->buildMatrix()`, the matrix created by this method is always available in array `$ulam->matrix`, x and y coords of placed on spiral values are available in array `$ulam->coords`.

## Screenshots:

CSS-styled version:

![gggg](https://user-images.githubusercontent.com/61396542/75218510-bfa40900-579a-11ea-9370-ee434dc0c48f.png)


Raw version:

![spiral_raw](https://user-images.githubusercontent.com/61396542/75210792-66c87680-5782-11ea-8dc5-8417e59288a1.png)



## Repository includes:

- UlamSpiral.php - base class
- example.php - usage example

## Configuration:
You can configure generator by creating `$config` array and put it into constructor. 
All keys in array are described below:

`raw` (bool) - `[true|false]` display raw spiral (without CSS), default: `false`

`append_css` (bool) - `[true|false]` enable CSS, default: `true`

`append_js` (bool) - `[true|false]` enable JS, default: `true`

`no_append_jquery` (bool) `[true|false]` - disable jQuery script appending, default: `false`

`counters_mode` (string) `[sum|count]` - counters mode (sum of values or occurencies count), default: `count`

`row_counters` (bool) `[true|false]` - enable vertical counters, default: `true`

`col_counters` (bool) `[true|false]` - enable horizontal counters, default: `true`

`cell_width` (int) - cell width in pixels, default: `35`

`cell_height` (int) - cell height in pixels, default: `35`

`cell_font_size` (int) - font size in pixels, default: `15`


## Custom callbacks:

### Numbers highlighting

You can create your own marker callback for highlight specified numbers (e.g. prime numbers, even numbers, numbers greater than specified one, etc.). Callback takes one argument with current number and must return `HTML color code` with which to highlight. If callback returns `null` or `false` then number will not be affected. You can create as many markers as you like, each one for a different number type.

Example showing how to create marker callback for even numbers:
```
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

Example showing how to create counter callback for even numbers:
```
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


### Ulam Spiral Generator is free to use but if you liked then you can donate project via BTC: 

**14X6zSCbkU5wojcXZMgT9a4EnJNcieTrcr**

or by PayPal:
 **[https://www.paypal.me/szczyglinski](https://www.paypal.me/szczyglinski)**


Enjoy!


MIT License | 2020 Marcin 'szczyglis' Szczygliński

https://github.com/szczyglis-dev/php-ulam-spiral-generator

Contact: szczyglis@protonmail.com
