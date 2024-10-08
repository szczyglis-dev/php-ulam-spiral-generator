<?php

/**
 * This file is part of szczyglis/php-ulam-spiral-generator.
 *
 * (c) Marcin Szczyglinski <szczyglis@protonmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require __DIR__ . '/vendor/autoload.php';

use Szczyglis\UlamSpiralGenerator\UlamSpiral;

/**
 * @package szczyglis/php-ulam-spiral-generator
 * @author Marcin Szczyglinski <szczyglis@protonmail.com>
 * @copyright 2022 Marcin Szczyglinski
 * @license   http://www.opensource.org/licenses/MIT The MIT License
 * @link https://github.com/szczyglis-dev/php-ulam-spiral-generator
 */

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