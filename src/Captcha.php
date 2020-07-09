<?php
/**
 * sFire Framework (https://sfire.io)
 *
 * @link      https://github.com/sfire-framework/ for the canonical source repository
 * @copyright Copyright (c) 2014-2020 sFire Framework.
 * @license   http://sfire.io/license BSD 3-CLAUSE LICENSE
 */

declare(strict_types=1);

namespace sFire\Captcha;

use sFire\Captcha\Exception\BadFunctionCallException;
use sFire\Captcha\Exception\BadMethodCallException;
use sFire\Captcha\Exception\InvalidArgumentException;
use sFire\Captcha\Exception\RuntimeException;
use sFire\Image\Color\Color;
use sFire\FileControl\File;


/**
 * Class Captcha
 * @package sFire\Captcha
 */
class Captcha {


    /**
     * Contains a File instance for the background image of the captcha image
     * @var null|File
     */
    private ?File $backgroundImage = null;


    /**
     * Contains a File instance with the current font file
     * @var null|File
     */
    private ?File $font = null;


    /**
     * Contains the minimum and maximum font size to be displayed in the captcha image
     * @var array
     */
    private array $fontsize = ['min' => 15, 'max' => 20];


    /**
     * Contains the information for the angle/degree a single character is displayed in the captcha image
     * @var array
     */
    private array $angle = ['min' => -30, 'max' => 30];


    /**
     * Contains the font color in RGB format
     * @var array
     */
    private array $color = ['r' => 0, 'g' => 0, 'b' => 0];


    /**
     * Contains the text to be displayed in the captcha image
     * @var null|string
     */
    private ?string $text = null;


    /**
     * Contains the level of noise in the captcha image
     * The higher the level, the more noise
     * @var int
     */
    private int $noise = 5;


    /**
     * Constructor
     * Checks if all the required functions exists
     * @throws BadFunctionCallException
     */
    public function __construct() {

        if(false === function_exists('imagettfbbox')) {
            throw new BadFunctionCallException('Function imagettfbbox should be enabled to use the Captcha class');
        }
    }


    /**
     * Sets a background image by giving a file path as image
     * @param string $image A path to the background image
     * @return self
     * @throws RuntimeException
     */
    public function setBackgroundImage(string $image): self {

        $file = new File($image);

        if(false === $file -> exists()) {
            throw new RuntimeException(sprintf('Argument 1 passed to %s() must be an existing image', __METHOD__));
        }

        if(false === in_array(exif_imagetype($image), [IMAGETYPE_JPEG, IMAGETYPE_PNG])) {
            throw new RuntimeException(sprintf('Argument 1 passed to %s() must be an image of the type jpg, jpeg or png', __METHOD__));
        }

        $this -> backgroundImage = $file;

        return $this;
    }


    /**
     * Sets the font type by giving a file path as font
     * @param string $font Path to a TTF font file
     * @return self
     * @throws InvalidArgumentException
     */
    public function setFont(string $font): self {

        $file = new File($font);

        if(false === $file -> exists()) {
            throw new InvalidArgumentException(sprintf('Argument 1 passed to %s() must be an existing font file', __METHOD__));
        }

        $this -> font = $file;

        return $this;
    }


    /**
     * Sets the color in RGB or Hex format (with or without #)
     * @param string|int $r A hexadecimal font or integer between 0 and 255 representing the color red
     * @param int $g [optional] A integer between 0 and 255 representing the color green
     * @param int $b [optional] A integer between 0 and 255 representing the color blue
     * @return self
     * @throws InvalidArgumentException
     */
    public function setFontColor($r, int $g = null, int $b = null): self {

        if(true === is_string($r) && null === $g && null === $b) {

            if(false === Color::validateHex($r)) {
                throw new InvalidArgumentException(sprintf('Argument 1 passed to %s() must be a valid hexadecimal color', __METHOD__));
            }

            list($r, $g, $b) = sscanf(ltrim($r, '#'), '%02x%02x%02x');
        }
        elseif(false === Color::validateRgb($r, $g, $b)) {
            throw new InvalidArgumentException(sprintf('Each color passed to %s() must be between 0 and 255', __METHOD__));
        }

        $this -> color = ['r' => $r, 'g' => $g, 'b' => $b];

        return $this;
    }


    /**
     * Sets the minimum and maximum font size
     * If no maximum font size is set, maximum will be the same as the minimum font size.
     * @param int $min The minimum font size
     * @param int $max [optional] The maximum font size
     * @return self
     * @throws InvalidArgumentException
     */
    public function setFontSize(int $min, int $max = null): self {

        if($min < 1) {
            throw new InvalidArgumentException(sprintf('Argument 1 passed to %s() must be at least 1', __METHOD__));
        }

        if(null !== $max && $max < 1) {
            throw new InvalidArgumentException(sprintf('Argument 2 passed to %s() must be at least 1', __METHOD__));
        }

        $this -> fontsize = ['min' => $min, 'max' => ($max ?? $min)];
        return $this;
    }


    /**
     * Sets the minimum and maximum font angle in degrees.
     * If no maximum font angle is set, maximum will be the same as minimum font angle.
     * @param int $min The minimum font angle in degrees
     * @param int $max [optional] The maximum font angle in degrees
     * @return self
     */
    public function setFontAngle(int $min, int $max = null): self {

        $this -> angle = ['min' => $min, 'max' => ($max ?? $min)];
        return $this;
    }


    /**
     * Adds random noise to the captcha image
     * @param int $level The higher the number, the more noise
     * @return self
     * @throws InvalidArgumentException
     */
    public function setNoise(int $level): self {

        if($level < 1) {
            throw new InvalidArgumentException(sprintf('Argument 1 passed to %s() must be at least 1', __METHOD__));
        }

        $this -> noise = $level;
        return $this;
    }


    /**
     * Sets the text that will be displayed in the captcha image
     * @param string $text THe text that will be displayed in the captcha image
     * @return self
     */
    public function setText(string $text): self {

        $this -> text = $text;
        return $this;
    }


    /**
     * Returns the font
     * @return null|File
     */
    public function getFont(): ?File {
        return $this -> font;
    }


    /**
     * Returns the font size
     * @return array
     */
    public function getFontSize(): array {
        return $this -> fontsize;
    }


    /**
     * Returns the angle
     * @return array
     */
    public function getFontAngle(): array {
        return $this -> angle;
    }


    /**
     * Returns the background image
     * @return null|File
     */
    public function getBackgroundImage(): ?File {
        return $this -> backgroundImage;
    }


    /**
     * Returns the color in RGB format
     * @return array
     */
    public function getFontColor(): array {
        return $this -> color;
    }


    /**
     * Returns the text
     * @return null|string
     */
    public function getText(): ?string {
        return $this -> text;
    }


    /**
     * Returns the noise level
     * @return null|int
     */
    public function getNoise(): ?int {
        return $this -> noice;
    }


    /**
     * Generates a random captcha value (without similar characters like o, O and 0)
     * @param int $length The length of the text, default 5
     * @param array $characters [optional] An array with characters to choose from
     * @return string
     */
    public function generateText(int $length = 5, array $characters = []): string {

        $caseInsensitive = ['a', 'c', 'd', 'e', 'f', 'h', 'k', 'm', 'n', 'p', 'r', 't', 'u', 'v', 'w', 'x', 'y'];
        $caseSensitive 	 = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'K', 'M', 'N', 'P', 'R', 'T', 'U', 'V', 'W', 'X', 'Y'];
        $numbersArray 	 = [3, 4, 5, 6, 7, 8, 9];
        $array 			 = array_merge($caseInsensitive, $caseSensitive, $numbersArray);
        $str 			 = '';

        if(count($characters) > 0) {
            $array = $characters;
        }

        for($i = 0; $i < $length; $i++) {
            $str .= $array[array_rand($array, 1)];
        }

        $this -> text = $str;

        return $str;
    }


    /**
     * Generates the captcha image and save it to disk or displays directly
     * @param string $file [optional] A file path location for saving the image to disk
     * @return void
     * @throws InvalidArgumentException
     * @throws BadMethodCallException
     */
    public function generate(string $file = null): void {

        if(null === $this -> backgroundImage) {
            throw new BadMethodCallException('Can not generate captcha image without valid background image. Set background image with setBackgroundImage() method');
        }

        if(null === $this -> font) {
            throw new BadMethodCallException('Can not generate captcha image without valid font. Set font with setFont() method');
        }

        //Generate and set text if not already set
        if(null === $this -> getText()) {
            $this -> setText($this -> generateText());
        }

        $text 	= $this -> getText();
        $image 	= $this -> backgroundImage;
        $font 	= $this -> font;
        $width = 0;

        //Add image header
        if(null === $file) {
            header(sprintf('Content-type: %s', $this -> backgroundImage -> getMimeType()));
        }
        else {

            $file = new File($file);

            if(false === in_array($file -> getExtension(), ['png', 'jpg', 'jpeg'])) {
                throw new InvalidArgumentException(sprintf('Argument 1 passed to %s() must have the jpg, jpeg or png extension, "%s" given', __METHOD__, $file -> getExtension()));
            }
        }

        //Create image
        switch(strtolower($image -> getExtension())) {

            case 'png' 	: $img = imagecreatefrompng($image -> getPath()); break;
            default 	: $img = imagecreatefromjpeg($image -> getPath());
        }

        //Generate output object
        $output = [];

        //Create color
        $color = imagecolorallocate($img, $this -> color['r'], $this -> color['g'], $this -> color['b']);

        for($i = 0; $i < strlen($text); $i++) {

            $fontsize 	= rand($this -> fontsize['min'], $this -> fontsize['max']);
            $angle 		= rand($this -> angle['min'], $this -> angle['max']);
            $tb 		= imagettfbbox($fontsize, $angle, $font -> getPath(), $text[$i]);

            $width 		= $image -> getWidth() / strlen($text);
            $width 		= $width < 2 ? 3 : $width;
            $tries 		= 0;

            if($tb[2] >= $width && $tries < 20) {

                $this -> fontsize['min']--;
                $i--;
                $tries++;

                if($tries > 20) {
                    break;
                }
            }

            $output[] = [

                'text' 		=> $text[$i],
                'width' 	=> $tb[2],
                'height' 	=> $tb[1],
                'angle'		=> $angle,
                'fontsize' 	=> $fontsize,
                'color' 	=> $color
            ];
        }

        foreach($output as $index => $char) {

            $x = $width * $index + rand(0, $width - $char['width']);
            $y = rand($char['fontsize'], $image -> getHeight() - $char['height']);

            imagettftext($img, $char['fontsize'], $char['angle'], $x, $y, $char['color'], $font -> getPath(), $char['text']);
        }

        $extension 	= $file !== null ? $file -> getExtension() : $image -> getExtension();
        $file 		= $file !== null ? $file -> getPath() : null;

        //Add noise
        if($this -> noise > 0) {

            for($i = 0; $i < $this -> noise; $i++) {

                imagesetthickness($img, mt_rand(0, 2));

                $x1 = rand(0, $image -> getWidth());
                $x2 = rand(0, $image -> getWidth());
                $y1 = rand(0, $image -> getHeight());
                $y2 = rand(0, $image -> getHeight());

                imageline($img, $x1, $x2, $y1, $y2, $color);
            }
        }

        //Show image
        switch(strtolower($extension)) {

            case 'png' 	: imagepng($img, $file); break;
            default 	: imagejpeg($img, $file);
        }
    }
}