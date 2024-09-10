<?php

use Carbon\Carbon;
use App\User;
use App\JobCable;


/*
    Check whether user has a specific permission
*/
function userHasPermissions($user, $check_permissions){
    $permissions = $user->permissions()->pluck('name')->toArray();

    foreach($check_permissions as $permission){
        if(!in_array($permission, $permissions)){
            $flag = false;
            break;
        }else{
            $flag = true;
        }
    }

    return $flag;
}

function claculateNightsTwoDates($d1, $d2)
{
    $date1 = new DateTime($d1);
    $date2 = new DateTime($d2);
    $numberOfNights= $date2->diff($date1)->format("%a");
    return $numberOfNights+1;
}

function formatDate($date)
{
    return date("d-m-Y", strtotime($date));
}

/**
 * @param $identifier
 * @param $oldSerialNo
 * @return mixed
 */
function generateAutoSerialNumber($identifier, $exstingCount) 
{
    $newCount = ($exstingCount + 1);
    $number = str_pad($newCount, 3, '0', STR_PAD_LEFT);
    return $identifier .'-'. $number;
}
/**
 *  Accepts a signature created by signature pad in Json format
 *  Converts it to an image resource
 *  The image resource can then be changed into png, jpg whatever PHP GD supports
 *
 *  To create a nicely anti-aliased graphic the signature is drawn 12 times it's original size then shrunken
 *
 *  @param string|array $json
 *  @param array $options OPTIONAL; the options for image creation
 *    imageSize => array(width, height)
 *    bgColour => array(red, green, blue) | transparent
 *    penWidth => int
 *    penColour => array(red, green, blue)
 *    drawMultiplier => int
 *
 *  @return object
 */
function sigJsonToImage ($json, $options = array()) {
  $defaultOptions = array(
    'imageSize' => array(198, 55)
    ,'bgColour' => 'transparent'
    ,'penWidth' => 2
    ,'penColour' => array(0x14, 0x53, 0x94)
    ,'drawMultiplier'=> 12
  );

  $options = array_merge($defaultOptions, $options);

  $img = imagecreatetruecolor($options['imageSize'][0] * $options['drawMultiplier'], $options['imageSize'][1] * $options['drawMultiplier']);

  if ($options['bgColour'] == 'transparent') {
    imagesavealpha($img, true);
    $bg = imagecolorallocatealpha($img, 0, 0, 0, 127);
  } else {
    $bg = imagecolorallocate($img, $options['bgColour'][0], $options['bgColour'][1], $options['bgColour'][2]);
  }

  $pen = imagecolorallocate($img, $options['penColour'][0], $options['penColour'][1], $options['penColour'][2]);
  imagefill($img, 0, 0, $bg);

  if (is_string($json))
    $json = json_decode(stripslashes($json));

  foreach ($json as $v)
    drawThickLine($img, $v->lx * $options['drawMultiplier'], $v->ly * $options['drawMultiplier'], $v->mx * $options['drawMultiplier'], $v->my * $options['drawMultiplier'], $pen, $options['penWidth'] * ($options['drawMultiplier'] / 2));

  $imgDest = imagecreatetruecolor($options['imageSize'][0], $options['imageSize'][1]);

  if ($options['bgColour'] == 'transparent') {
    imagealphablending($imgDest, false);
    imagesavealpha($imgDest, true);
  }

  imagecopyresampled($imgDest, $img, 0, 0, 0, 0, $options['imageSize'][0], $options['imageSize'][0], $options['imageSize'][0] * $options['drawMultiplier'], $options['imageSize'][0] * $options['drawMultiplier']);
  imagedestroy($img);

  return $imgDest;
}

/**
 *  Draws a thick line
 *  Changing the thickness of a line using imagesetthickness doesn't produce as nice of result
 *
 *  @param object $img
 *  @param int $startX
 *  @param int $startY
 *  @param int $endX
 *  @param int $endY
 *  @param object $colour
 *  @param int $thickness
 *
 *  @return void
 */
function drawThickLine ($img, $startX, $startY, $endX, $endY, $colour, $thickness) {
  $angle = (atan2(($startY - $endY), ($endX - $startX)));

  $dist_x = $thickness * (sin($angle));
  $dist_y = $thickness * (cos($angle));

  $p1x = ceil(($startX + $dist_x));
  $p1y = ceil(($startY + $dist_y));
  $p2x = ceil(($endX + $dist_x));
  $p2y = ceil(($endY + $dist_y));
  $p3x = ceil(($endX - $dist_x));
  $p3y = ceil(($endY - $dist_y));
  $p4x = ceil(($startX - $dist_x));
  $p4y = ceil(($startY - $dist_y));

  $array = array(0=>$p1x, $p1y, $p2x, $p2y, $p3x, $p3y, $p4x, $p4y);
  imagefilledpolygon($img, $array, (count($array)/2), $colour);
}

function createImage($imageJson = null){
    $json = $imageJson;
    try{
        $img = sigJsonToImage($json);
        ob_start(); // start output buffer
        imagepng($img); // output image data to buffer
        imagedestroy($img);
        // create Data URI for a PNG image, using the binary image
        // data captured from the output buffer
        $dataURI = 'data:image/png;base64,'.base64_encode(ob_get_clean());
        $response['status'] = "success";
        $response['message'] = "Image saved successfully";
        $response['signature'] = $dataURI;
    }catch(Exception $e){
        $response['status'] = 'error';
        $response['message'] = $e->getMessage();
    }
    return json_encode($response);
}

function removeSign(){
    $sign = $_POST['sign'];
    $path  = STORE_DIRECTORY."/html/media/".$sign;
    unlink($path);
    return true;
}
?>