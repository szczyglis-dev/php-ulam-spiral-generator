<?php

include __DIR__.'/UlamSpiral.php';


/* Create config (optional) */
$config = [
	'raw' => false,
	'append_css' => true,
	'append_js' => true,
	'no_append_jquery' => false,
	'counters_mode' => 'count',
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