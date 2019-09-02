<?php 

/**
 * @author Andrei Coelho
 * @version 0.1
 */

namespace ImageBuilder;

use ImageBuilder\ImageBuilderException as ImageBuilderException;
use ImageBuilder\Image as Image;

class Build {

	public static function image(Image $image, $alias)
	{

		# actions... here we go!
		foreach ($image -> actions as $action => $value)
			$image -> resource = self::$action($image, $value);

   		# filters... it's your turn!
   		foreach ($image -> filters as $filter => $values)
			self::$filter();
		
		# save Image now!
		$suffixed = $image -> modify ? "" : "_".$alias; 
   		$create = $image -> mime."_create";
   		self::$create($image, $suffixed);
   	}

	
	/*                       *
	*------------------------*
	*      CREATE IMAGE      *
	*------------------------*
	*                        */

	private static function jpg_create(Image $image, $suffixed)
	{
		\imagejpeg($image -> resource, $image -> path . $image -> name . $suffixed . "." . $image -> mime, 100);
		\imagedestroy($image -> resource);
	}

	private static function png_create(Image $image, $suffixed)
	{
		\imagepng($image -> resource, $image -> path . $image -> name . $suffixed . "." . $image -> mime, 0);
		\imagedestroy($image -> resource);
	}

   
	/*                       *
	*------------------------*
	*         ACTIONS        *
	*------------------------*
	*                        */

	private static function resize(Image $image, string $value)
	{
	
		$sizes = self::generate_width_height_resize($value, $image -> sizes);
		
		$res = imagecreatetruecolor($sizes[0], $sizes[1]);
		if(self::isPng($image)) {
			imagealphablending($res, false);
            imagesavealpha($res, true);
		}

		imagecopyresized(
			$res, 
			$image -> resource, 0, 0, 0, 0, 
			$sizes[0],
			$sizes[1],
			$image -> sizes[0], 
			$image -> sizes[1]
		);

		$image -> sizes = $sizes;
		return $res;
		
	}

	public function crop(Image $image, string $cropsizes)
	{
	
		$values = self::generate_values_crop($cropsizes, $image -> sizes);

		$res = imagecreatetruecolor($values[2], $values[3]);
		if(self::isPng($image)) {
			imagealphablending($res, false);
            imagesavealpha($res, true);
		}

		imagecopyresampled(
			$res, // dest
			$image -> resource, 
			0, 0, // dest
			$values[0], $values[1],
			$values[2], $values[3], // dest
			$values[2], $values[3]
		);

		$image -> sizes = [$values[2], $values[3]];
		return $res;
	}

	private static function isPng(Image $image)
	{
		return $image -> mime === 'png';
	}

	private static function generate_width_height_resize(string $sizes, array $source)
    {
		$sizes = explode('x', $sizes);
	
        if ($sizes[0] == "*"){
            $multipl = $sizes[1] / $source[1];
            $h = $sizes[1];
            $w =  (int)($source[0] * $multipl);
        } else 
        if ($sizes[1] == "*"){
            $multipl = $sizes[0] / $source[0];
            $w = $sizes[0];
            $h =  (int)($source[1] * $multipl);
		} else 
		if($sizes[0] == "_"){
			$h = $sizes[1];
			$w = $source[0];
		} else 
		if($sizes[1] == "_"){
			$h = $source[1];
			$w = $sizes[0];
		} 
		else {
            $h = $sizes[1];
            $w = $sizes[0];
        }
        return [$w,$h];
	}
	
	private static function generate_values_crop(string $info, array $source)
	{
		$vals = explode(' ', $info);
		$sizesCrop = self::generate_width_height_resize($vals[2], $source);
		return [
			is_numeric($vals[0]) ? (int) $vals[0] : self::transform_value($vals[0], $source, $sizesCrop, 0),
			is_numeric($vals[1]) ? (int) $vals[1] : self::transform_value($vals[1], $source, $sizesCrop, 1),
			$sizesCrop[0],
			$sizesCrop[1]
		];
		
	}

	private static function transform_value(string $value, array $source, array $sizes, $type)
	{
		switch ($value) {
			case 'center':
				return (int)($source[$type] - $sizes[$type]) / 2;
			case 'left':
			case 'top':
				return 0;
			case 'right':
				return $source[0] - $sizes[0];
			case 'bottom':
				return $source[1] - $sizes[1];
		}
	}

}