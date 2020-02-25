<?php

/**
 * @package Ulam Spiral Generator
 * @author Marcin Szczyglinski <szczyglis@protonmail.com>
 * @link https://github.com/szczyglis-dev/php-ulam-spiral-generator
 * @license MIT
 * @version 1.0 | 2020.02.25
 */
class UlamSpiral
{
    /**
     * @var array
     */
    public $dataset = [];

    /**
     * @var array
     */
    public $matrix = [];

    /**
     * @var array
     */
    public $coords = [];

    /**
     * @var array
     */
    public $config = [];

    /**
     * @var array
     */
    public $dataIndexed = [];

    /**
     * @var array
     */
    private $counters = [];

    /**
     * @var array
     */
    private $counterCallbacks = [];

    /**
     * @var array
     */
    private $markerCallbacks = [];

    /**
     * UlamSpiral constructor.
     * @param array $config
     */
    public function __construct($config = [])
    {
        $this->config = [
            'raw' => false,
            'append_css' => true,
            'append_js' => true,
            'no_append_jquery' => false,
            'mark_prime' => true,
            'counters_mode' => 'count', /* count|sum */
            'row_counters' => true,
            'col_counters' => true,
            'cell_width' => 35,
            'cell_height' => 35,
            'cell_font_size' => 12
        ];

        if (is_array($config)) {
            foreach ($config as $k => $v) {
                $this->config[$k] = $v;
            }
        }
    }

    /**
     * @param $id
     * @param $callback
     */
    public function addCounter($id, $callback)
    {
        if (is_callable($callback)) {
        	$this->counterCallbacks[$id] = $callback;
        }
    }

    /**
     * @param $id
     * @param $callback
     */
    public function addMarker($id, $callback)
    {
        if (is_callable($callback)) {
        	$this->markerCallbacks[$id] = $callback;
        }
    }

    /**
     * @param array $arr
     * @return int|string|null
     */
    private function arrayKeyFirst($arr)
    {
        foreach ($arr as $key => $tmp) {
            return $key;
        }
        return null;
    }

    /**
     * @param array $arr
     * @return int|string|null
     */
    private function arrayKeyLast($arr)
    {
        if (!is_array($arr) || empty($arr)) {
            return null;
        }

        return array_keys($arr)[count($arr) - 1];
    }

    /**
     * @return array
     */
    public function buildMatrix()
    {
        $this->coords = [];
        $this->matrix = [];

        $x = 0;
        $y = 0;
        $steps = 1;
        $counter = 0;
        $step = 0;
        $directions = ['right', 'up', 'left', 'down'];
        $currentDirectionIndex = 0;
        $i = 0;

        foreach ($this->dataset as $value) {
            $switchDirection = false;
            if ($step == $steps) {
                if ($steps > 1) $switchDirection = true;
                $step = 2;
                if ($counter % 2 == 0) {
                    $steps++;
                }
                $counter++;
            } else {
                $step++;
            }

            if ($switchDirection) {
                if (isset($directions[$currentDirectionIndex + 1])) {
                    $currentDirectionIndex++;
                } else {
                    $currentDirectionIndex = 0;
                }
            }

            $currentDirection = $directions[$currentDirectionIndex];
            switch ($currentDirection) {
                case 'right':
                    $x++;
                    break;

                case 'up':
                    $y--;
                    break;

                case 'left':
                    $x--;
                    break;

                case 'down':
                    $y++;
                    break;
            }

            $this->matrix[$y][$x] = $value;
            $this->coords[$i] = [
                'y' => $y,
                'x' => $x
            ];

            $i++;
        }

        ksort($this->matrix);
        foreach ($this->matrix as $i => $row) {
            $cols = $row;
            ksort($cols);
            $this->matrix[$i] = $cols;
        }

        return $this->matrix;
    }

    /**
     * @param $number
     * @return bool
     */
    public static function isPrime($number)
    {
        if ($number == 1) {
            return false;
        } else if ($number == 2) {
            return true;
        }

        $n = sqrt($number);
        $n = floor($n);

        for ($i = 2; $i <= $n; ++$i) {
            if ($number % $i == 0) {
                break;
            }
        }
        if ($n == $i - 1) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param $value
     * @param $id
     * @param $rowIndex
     * @param $colIndex
     */
    private function updateCounter($value, $id, $rowIndex, $colIndex)
    {
        
        if (!isset($this->counters[$id][$rowIndex][$colIndex])) {
            $this->counters[$id][$rowIndex][$colIndex] = 0;
        }

        if ($this->config['counters_mode'] == 'count') {

            $this->counters[$id][$rowIndex][$colIndex] = 1;

        } else if ($this->config['counters_mode'] == 'sum') {

            if (!isset($this->counters[$id][$rowIndex][$colIndex])) {
                $this->counters[$id][$rowIndex][$colIndex] = 0;
            }
            if (is_integer($value)) {
                $this->counters[$id][$rowIndex][$colIndex] += $value;
            }
        }
    }

    /**
     * @param $row
     * @param $rowIndex
     * @param bool $reverse
     * @return string
     */
    private function attachVerticalHeader($row, $rowIndex, $reverse = false)
    {
        $render = '';
        $data = [];

        foreach ($this->counters as $id => $counter) {
            $data[$id] = array_sum($counter[$rowIndex]);
        }

        if ($reverse) {
            $data = array_reverse($data);
        }

        $header = '';
        foreach ($data as $k => $items) {        	
            if (!$this->config['raw']) {
                $header .= '<td class="hdr-y-' . $rowIndex . ' hdr" title="' . $k . '">' . $items . '</td>';
            } else {
                $header .= '<td>' . $items . '</td>';
            }
        }
        return $header;
    }

    /**
     * @param $row
     * @param $maxColumns
     * @param bool $reverse
     * @return string
     */
    private function attachHorizontalHeader($row, $maxColumns, $reverse = false)
    {
        $headers = [];

        for ($i = 0; $i < $maxColumns; $i++) {

            foreach ($this->counters as $id => $counter) {
                $sum = 0;
                foreach ($counter as $row) {
                    if (isset($row[$i]) && is_integer($row[$i])) {
                        $sum += $row[$i];
                    }
                }
                $headers[$id][] = $sum;
            }
        }

        if ($reverse) {
            $headers = array_reverse($headers);
        }

        $header = '';
        foreach ($headers as $k => $part) {
            $header .= '<tr>';
            if (!$this->config['raw']) {
                $header .= str_repeat('<td class="hdr"></td>', count($this->counters));
            } else {
                $header .= str_repeat('<td></td>', count($this->counters));
            }

            foreach ($part as $i => $data) {
                if (!$this->config['raw']) {
                    $header .= '<td class="hdr-x-' . $i . ' hdr" title="' . $k . '">' . $data . '</td>';
                } else {
                    $header .= '<td>' . $data . '</td>';
                }
            }

            if (!$this->config['raw']) {
                $header .= str_repeat('<td class="hdr"></td>', count($this->counters));
            } else {
                $header .= str_repeat('<td></td>', count($this->counters));
            }

            $header .= '</tr>';
        }

        return $header;
    }

    /**
     * @param array $config
     * @return string
     */
    public function render($config = [])
    {
        if (empty($this->matrix)) {
            $this->buildMatrix();
        }

        $i = 0;
        $firstX = 0;
        $lastX = 0;
        $columnsLength = [];
        $firstKeys = [];
        $lastKeys = [];
        foreach ($this->matrix as $row) {
            $columnsLength[] = count($row);
            $firstKeys[] = $this->arrayKeyFirst($row);
            $lastKeys[] = $this->arrayKeyLast($row);
        }

        $maxColumns = max($columnsLength);
        $firstX = min($firstKeys);
        $lastX = max($lastKeys);
        $rows = count($this->matrix);
        $rowIndex = 0;
        $table = '';

        foreach ($this->matrix as $y => $row) {

            $rowData = '';

            $c = count($row);
            if ($c <= $maxColumns) {
                for ($j = $c; $j < $maxColumns; $j++) {
                    $currentFirstX = $this->arrayKeyFirst($row);
                    if ($currentFirstX > $firstX) {
                        if (!$this->config['raw']) {
                            $rowData .= '<td data-x="" data-y="" id="" class="matrix-cell">-</td>';
                        } else {
                            $rowData .= '<td>-</td>';
                        }
                    }
                }
            }

            $colIndex = 0;
            foreach ($row as $x => $value) {

                $marker = '';
                foreach ($this->markerCallbacks as $id => $callback) {
                    if (is_callable($callback)) {
                        $result = $callback($value);
                        if (!empty($result)) {
                            $marker = $result;
                        }
                    }
                }

                foreach ($this->counterCallbacks as $id => $callback) {
                    if (is_callable($callback)) {
                        if ($callback($value) === true) {
                            $this->updateCounter($value, $id, $rowIndex, $colIndex);
                        }
                    }
                }

                $style = '';
                if (!empty($marker)) {
                    $style = 'style="background:' . $marker . '"';
                }

                if ($y == 0 && $x == 1) {
                    if (!$this->config['raw']) {
                        $value = '<span class="num_c">' . $value . '</span>';
                    }
                }

                if (!$this->config['raw']) {
                    $rowData .= '<td data-x-index="' . $colIndex . '" data-y-index="' . $rowIndex . '" data-x="' . $x . '" data-y="' . $y . '" id="matrix_' . $x . '_' . $y . '" class="matrix-cell" ' . $style . ' title="' . strip_tags($value) . ' (' . $x . ',' . $y . ')">' . $value . '</td>';
                } else {
                    $rowData .= '<td>' . $value . '</td>';
                }

                $this->dataIndexed[$rowIndex][$colIndex] = $value;

                $colIndex++;
            }

            $c = count($row);
            if ($c <= $maxColumns) {
                for ($j = $c; $j < $maxColumns; $j++) {
                    $currentLastX = $this->arrayKeyLast($row);
                    if ($currentLastX < $lastX) {
                        if (!$this->config['raw']) {
                            $rowData .= '<td data-x="" data-y="" id="" class="matrix-cell">-</td>';
                        } else {
                            $rowData .= '<td>-</td>';
                        }
                    }
                }
            }

            $table .= '<tr>';

            if ($this->config['col_counters']) {
                $table .= $this->attachVerticalHeader($row, $rowIndex, false);
            }

            $table .= $rowData;

            if ($this->config['col_counters']) {
                $table .= $this->attachVerticalHeader($row, $rowIndex, true);
            }

            $table .= '</tr>';

            $i++;

            $rowIndex++;
        }

        $render = '';
        if (!$this->config['raw']) {
            if ($this->config['append_css']) {
                $render .= '<style>' . $this->getCSS() . '</style>';
            }
            if ($this->config['append_js']) {
                $render .= $this->getJavascript();
            }
        }

        if (!$this->config['raw']) {
            $render .= '<table class="table">';
        } else {
            $render .= '<table>';
        }

        if ($this->config['row_counters']) {
            $render .= $this->attachHorizontalHeader($row, $maxColumns, false);
        }
        $render .= $table;

        if ($this->config['row_counters']) {
            $render .= $this->attachHorizontalHeader($row, $maxColumns, true);
        }

        $render .= '</table>';

        if (!$this->config['raw']) {
            $render .= '<div id="matrix_status">[matrix coords]</div>';
        }

        return $render;

    }

    /**
     * @return string
     */
    public function getJavascript()
    {
        $js = '';
        if ($this->config['no_append_jquery'] !== true) {
            $js .= '<script type="text/javascript" src="https://code.jquery.com/jquery-3.4.1.min.js"></script>';
        }

        $js .= '
		<script>
		$(document).ready(function() {

			$(".matrix-cell").hover(function() {

				$(".table td").each(function() {
					$(this).removeClass("m_line");
				});

				var x = parseInt($(this).attr("data-x"));
				var y = parseInt($(this).attr("data-y"));
				var my_x = x;
				var my_y = y;
				var x_index = parseInt($(this).attr("data-x-index"));
				var y_index = parseInt($(this).attr("data-y-index"));
				var val = $(this).text();
				var status = "val: "+val+"<br>x: "+x+"<br>y: "+y+"<br>x_index: "+x_index+"<br>y_index: "+y_index;
				$("#matrix_status").html(status);
				$(".hdr-y-"+y_index).addClass("m_line_hdr");
				$(".hdr-x-"+x_index).addClass("m_line_hdr");				

				// y-
				var id_more = "matrix_"+x+"_"+(y-1);	
				var is_more = false;	
				if(document.getElementById(id_more)) {			
					y = y - 1;
					is_more = true;
				}
				while (is_more) {
					var id_now = "matrix_"+x+"_"+y;
					$("#" + id_now).addClass("m_line");
					var id_more = "matrix_"+x+"_"+(y-1);
					if(document.getElementById(id_more)) {
						y = y - 1;
						is_more = true;
					} else {
						is_more = false;
					}
				}

				// y+
				id_more = "matrix_"+x+"_"+(my_y+1);	
				is_more = false;	
				if(document.getElementById(id_more)) {			
					y = my_y + 1;
					is_more = true;
				}
				while (is_more) {
					var id_now = "matrix_"+x+"_"+y;
					$("#" + id_now).addClass("m_line");
					var id_more = "matrix_"+x+"_"+(y+1);
					if(document.getElementById(id_more)) {
						y = y + 1;
						is_more = true;
					} else {
						is_more = false;
					}
				}

				// x-				
				is_more = false;
				id_more = "matrix_"+(x-1)+"_"+my_y;		
				if(document.getElementById(id_more)) {			
					x = x - 1;
					is_more = true;
				}
				while (is_more) {
					var id_now = "matrix_"+x+"_"+my_y;
					$("#" + id_now).addClass("m_line");
					var id_more = "matrix_"+(x-1)+"_"+my_y;
					if(document.getElementById(id_more)) {
						x = x - 1;
						is_more = true;
					} else {
						is_more = false;
					}
				}

				// +
				is_more = false;
				id_more = "matrix_"+(my_x+1)+"_"+my_y;		
				if(document.getElementById(id_more)) {			
					x = my_x + 1;
					is_more = true;
				}
				while (is_more) {
					var id_now = "matrix_"+x+"_"+my_y;
					$("#" + id_now).addClass("m_line");
					var id_more = "matrix_"+(x+1)+"_"+my_y;
					if(document.getElementById(id_more)) {
						x = x + 1;
						is_more = true;
					} else {
						is_more = false;
					}
				}

				// cross x- y-		
				is_more = false;
				id_more = "matrix_"+(my_x-1)+"_"+(my_y-1);		
				if(document.getElementById(id_more)) {			
					x = my_x - 1;
					y = my_y - 1;
					is_more = true;
				}
				while (is_more) {
					var id_now = "matrix_"+x+"_"+y;
					$("#" + id_now).addClass("m_line");
					var id_more = "matrix_"+(x-1)+"_"+(y-1);
					if(document.getElementById(id_more)) {
						x = x - 1;
						y = y - 1;
						is_more = true;
					} else {
						is_more = false;
					}
				}

				// cross x+ y+		
				is_more = false;
				id_more = "matrix_"+(my_x+1)+"_"+(my_y+1);		
				if(document.getElementById(id_more)) {			
					x = my_x + 1;
					y = my_y + 1;
					is_more = true;
				}
				while (is_more) {
					var id_now = "matrix_"+x+"_"+y;
					$("#" + id_now).addClass("m_line");
					var id_more = "matrix_"+(x+1)+"_"+(y+1);
					if(document.getElementById(id_more)) {
						x = x + 1;
						y = y + 1;
						is_more = true;
					} else {
						is_more = false;
					}
				}

				// cross x- y+		
				is_more = false;
				id_more = "matrix_"+(my_x-1)+"_"+(my_y+1);		
				if(document.getElementById(id_more)) {			
					x = my_x - 1;
					y = my_y + 1;
					is_more = true;
				}
				while (is_more) {
					var id_now = "matrix_"+x+"_"+y;
					$("#" + id_now).addClass("m_line");
					var id_more = "matrix_"+(x-1)+"_"+(y+1);
					if(document.getElementById(id_more)) {
						x = x - 1;
						y = y + 1;
						is_more = true;
					} else {
						is_more = false;
					}
				}

				// cross x+ y-		
				is_more = false;
				id_more = "matrix_"+(my_x+1)+"_"+(my_y-1);		
				if(document.getElementById(id_more)) {			
					x = my_x + 1;
					y = my_y - 1;
					is_more = true;
				}
				while (is_more) {
					var id_now = "matrix_"+x+"_"+y;
					$("#" + id_now).addClass("m_line");
					var id_more = "matrix_"+(x+1)+"_"+(y-1);
					if(document.getElementById(id_more)) {
						x = x + 1;
						y = y - 1;
						is_more = true;
					} else {
						is_more = false;
					}
				}

			}, function(){
			    $(".table td").each(function() {
					$(this).removeClass("m_line");
					$(this).removeClass("m_line_hdr");
				});
			});
		});

		</script>';

        return $js;
    }

    /**
     * @return string
     */
    public function getCSS()
    {
        return '
		.table .hdr {
			background: #fbfbfb;
			font-weight: bold;
		}
		.table td {
			width: ' . $this->config['cell_width'] . 'px;
			height: ' . $this->config['cell_height'] . 'px;
			min-width: ' . $this->config['cell_width'] . 'px;
			min-height: ' . $this->config['cell_height'] . 'px;
			border: 1px solid transparent;
			padding: 0px;
			text-align: center;
			font-size: ' . $this->config['cell_font_size'] . 'px;
		}
		.table td:hover {
			border:1px solid #000;	
		}
		.num_c {
			font-weight: 
			bold;color:red; 
			font-size:13px;
		}
		.m_line {
			border:1px solid #f63b3b !important;
			color:red  !important;
		}
		.m_line_hdr {
			background: #f5f5b6 !important;
		}
		#matrix_status {
			position: fixed;
			bottom: 10px;
			right: 10px;
		}
		';
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string)$this->render();
    }
}