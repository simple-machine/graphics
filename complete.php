<?php 

function generateSensors($type = 'T', $start = 0, $num = 10, $size = 50, $error_correction = 'Q') {

$path = dirname(__FILE__);

$tcpdf_include_dirs = array(
        realpath( $path . '/TCPDF/tcpdf.php')
);

foreach ($tcpdf_include_dirs as $tcpdf_include_path) {
        if (@file_exists($tcpdf_include_path)) {
                require_once($tcpdf_include_path);
                break;
        }
}


// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);


$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// remove default header/footer
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

// set margins
$pdf->SetMargins(0, 0, 0);
$pdf->SetHeaderMargin(0);
$pdf->SetFooterMargin(0);

// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set some language-dependent strings (optional)
if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
	require_once(dirname(__FILE__).'/lang/eng.php');
	$pdf->setLanguageArray($l);
}

// ---------------------------------------------------------

// NOTE: 2D barcode algorithms must be implemented on 2dbarcode.php class file.

// set font
$pdf->SetFont('helvetica', '', 11);

// add a page
$pdf->AddPage('P', 'A1');


$pdf->SetFont('helvetica', '', 8);

// - - - - - - - - - - - - - - - - - - - - - - - - - - - - -





// set style for barcode
$style = array(
	'border' => 0,
	'vpadding' => 0,
	'hpadding' => 0,
	'fgcolor' => array(0,0,0),
	'bgcolor' => false, //array(255,255,255)
	'module_width' => 1, // width of a single module in points
	'module_height' => 1 // height of a single module in points
);

$y_off = 24;
$page_width = 190;
//$size=20;
//$num = 100;
$space = $size / 5;
//$space = 0;

$fontsize = $size / 2;
$pdf->SetFont('helvetica', '', $fontsize);

$cols = floor($page_width / ( $size + $space))+1;
//$cols = 4;


// Landmarks

$ip_manometer_range = 500;
$flow_manometer_range = 200;
$landmark_size = 50;
$cable_tie_hole_d = 6;
$drive_wheel_d = 200;
$drive_wheel = array($drive_wheel_d/2+20, $drive_wheel_d/2);

$manometers_y = $ip_manometer_range+$landmark_size+$cable_tie_hole_d*2;

$landmarks_origin = array(0,$ip_manometer_range+$landmark_size*2+$cable_tie_hole_d*4);

$landmarks = array(array(0,0), array(0,100), array(100,0));

$landmarks = array(
	// beside wheel
	array($drive_wheel_d+40+$landmark_size/2,$landmark_size/2),
	// above wheel
	array($landmark_size/2+10,$manometers_y - $flow_manometer_range - $landmark_size*2 -20),
	array($drive_wheel_d/2+142,$manometers_y - $flow_manometer_range - $landmark_size*2 -20),
	// plumbbob hang point
	array($drive_wheel_d+20+$landmark_size*5,$manometers_y + $landmark_size/2),
	// top-left
	array($landmark_size/2+10,$manometers_y + $landmark_size/2),

);



foreach($landmarks as $k=>$landmark) {
	$x = $landmarks_origin[0] + $landmark[0] - $landmark_size/2;
	$y = $landmarks_origin[1] - $landmark[1] - $landmark_size/2; // our coordinate has up = y+
	// write landmark centered at coordinate
	$pdf->write2DBarcode("LM". ($k+1) . ",".$landmark[0].",".$landmark[1], 'QRCODE,'.$error_correction, $x, $y, 0, $landmark_size, $style, 'N');

}



$manometers = array(
array(
'identifier'=>'MIP',
'x'=>$drive_wheel_d+20+$landmark_size*2,
'y'=>$manometers_y,
'column_spacing'=>$landmark_size + 20,
'range'=>$ip_manometer_range,
'tube_diameter'=>12,
'incline'=>0
),

array(
'identifier'=>'MFLOW',
'x'=>$landmark_size + 40 + $cable_tie_hole_d*2 + $landmark_size*2,
'y'=>$manometers_y,
'column_spacing'=>$landmark_size + 20,
'range'=>$flow_manometer_range,
'tube_diameter'=>12,
'incline'=>0
),
array(
'identifier'=>'MO2',
'x'=>$landmark_size + 40,
'y'=>$manometers_y,
'column_spacing'=>$landmark_size + 20,
'range'=>$flow_manometer_range,
'tube_diameter'=>12,
'incline'=>0
),
);

foreach($manometers as $manometer) {
	$x = $landmarks_origin[0] + $manometer['x'];
	$y = $landmarks_origin[1] - $manometer['y']; // our coordinate has up = y+
	
	// cable tie holes
	// top-left
	$pdf->Circle($x-$cable_tie_hole_d/2-$manometer['tube_diameter']/2,$y-$cable_tie_hole_d/2,$cable_tie_hole_d/2, 0, 360, null);
	$pdf->Circle($x+$cable_tie_hole_d/2+$manometer['tube_diameter']/2,$y-$cable_tie_hole_d/2,$cable_tie_hole_d/2, 0, 360, null);
    // bottom-left
	$pdf->Circle($x-$cable_tie_hole_d/2-$manometer['tube_diameter']/2,$y+$cable_tie_hole_d/2+$manometer['range'],$cable_tie_hole_d/2, 0, 360, null);
	$pdf->Circle($x+$cable_tie_hole_d/2+$manometer['tube_diameter']/2,$y+$cable_tie_hole_d/2+$manometer['range'],$cable_tie_hole_d/2, 0, 360, null);

	// top-right
	$pdf->Circle($x-$cable_tie_hole_d/2-$manometer['tube_diameter']/2+$manometer['column_spacing']+$manometer['tube_diameter'],$y-$cable_tie_hole_d/2,$cable_tie_hole_d/2, 0, 360, null);
	$pdf->Circle($x+$cable_tie_hole_d/2+$manometer['tube_diameter']/2+$manometer['column_spacing']+$manometer['tube_diameter'],$y-$cable_tie_hole_d/2,$cable_tie_hole_d/2, 0, 360, null);
    // bottom-right
	$pdf->Circle($x-$cable_tie_hole_d/2-$manometer['tube_diameter']/2+$manometer['column_spacing']+$manometer['tube_diameter'],$y+$cable_tie_hole_d/2+$manometer['range'],$cable_tie_hole_d/2, 0, 360, null);
	$pdf->Circle($x+$cable_tie_hole_d/2+$manometer['tube_diameter']/2+$manometer['column_spacing']+$manometer['tube_diameter'],$y+$cable_tie_hole_d/2+$manometer['range'],$cable_tie_hole_d/2, 0, 360, null);

	// ident QR code
	$pdf->write2DBarcode($manometer['identifier'].",".$manometer['range'], 'QRCODE,'.$error_correction, $x+$manometer['tube_diameter']/2+$manometer['column_spacing']/2-$landmark_size/2,$y+$cable_tie_hole_d*2+$manometer['range'], 0, $landmark_size, $style, 'N');

}

// Drive wheel

$x = $landmarks_origin[0] + $drive_wheel[0];
$y = $landmarks_origin[1] - $drive_wheel[1]; // our coordinate has up = y+

// center
$pdf->Circle($x,$y,5, 0, 360, null);
// outside
$pdf->Circle($x,$y,$drive_wheel_d/2, 0, 360, null);
// angle qr codes
$pdf->write2DBarcode("WA,90", 'QRCODE,'.$error_correction, $x-$landmark_size/2,$y-$landmark_size/2-$drive_wheel_d/4, 0, $landmark_size, $style, 'N');
$pdf->write2DBarcode("WA,270", 'QRCODE,'.$error_correction, $x-$landmark_size/2,$y-$landmark_size/2+$drive_wheel_d/4, 0, $landmark_size, $style, 'N');



// ---------------------------------------------------------

//Close and output PDF document
$pdf->Output($path . '/sensors-complete.pdf', 'F');

//============================================================+
// END OF FILE
//============================================================+

	}


//generateSensorsPage('T', 200, 20, 18, 'H');

generateSensors('T', 200, 20, 18, 'H');

