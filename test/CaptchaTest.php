<?php
/**
 * sFire Framework (https://sfire.io)
 *
 * @link      https://github.com/sfire-framework/ for the canonical source repository
 * @copyright Copyright (c) 2014-2020 sFire Framework.
 * @license   http://sfire.io/license BSD 3-CLAUSE LICENSE
 */

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use sFire\Captcha\Captcha;
use sFire\FileControl\File;


/**
 * Class CaptchaTest
 */
final class CaptchaTest extends TestCase {


    /**
     * The path to the assets folder
     * @var null|string
     */
    private ?string $path = null;


    /**
     * Setup
     */
    public function setUp(): void {

        $this -> path = getcwd() . DIRECTORY_SEPARATOR . 'test' . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR;

        @unlink($this -> path . 'captcha.png');
        @unlink($this -> path . 'captcha.jpg');
    }


    /**
     * Testing captcha text generation
     * @return void
     */
    public function testTextGeneration(): void {

        $captcha = new Captcha();
        $text = $captcha -> generateText();
        $this -> assertRegExp('/[a-zA-Z0-9]{5}/', $text);

        $text = $captcha -> generateText(10);
        $this -> assertRegExp('/[a-zA-Z0-9]{10}/', $text);
    }


    /**
     * Testing setting a background image
     * Testing setting a non-existing background image
     * @return void
     */
    public function testSettingBackgroundImage(): void {

        $captcha = new Captcha();
        $captcha -> setBackgroundImage($this -> path . 'captcha-bg.jpg');
        $this -> assertInstanceOf(File :: class, $captcha -> getBackgroundImage());

        $this -> expectException(ErrorException :: class);
        $captcha = new Captcha();
        $captcha -> setBackgroundImage($this -> path . 'non-existing.jpg');
    }


    /**
     * Testing setting a font
     * Testing setting a non-existing font
     * @return void
     */
    public function testSettingFont(): void {

        $captcha = new Captcha();
        $captcha -> setFont($this -> path . 'captcha.ttf');
        $this -> assertInstanceOf(File :: class, $captcha -> getFont());

        $this -> expectException(ErrorException :: class);
        $captcha = new Captcha();
        $captcha -> setFont($this -> path . 'non-existing.ttf');
    }


    /**
     * Testing setting a font color
     * Testing setting an invalid font color
     * @return void
     */
    public function testSettingFontColor(): void {

        $captcha = new Captcha();
        $captcha -> setFontColor('525252');
        $this -> assertEquals(['r' => 82, 'g' => 82, 'b' => 82], $captcha -> getFontColor());

        $captcha = new Captcha();
        $captcha -> setFontColor('#525252');
        $this -> assertEquals(['r' => 82, 'g' => 82, 'b' => 82], $captcha -> getFontColor());

        $captcha = new Captcha();
        $captcha -> setFontColor(82, 82, 82);
        $this -> assertEquals(['r' => 82, 'g' => 82, 'b' => 82], $captcha -> getFontColor());

        $this -> expectException(ErrorException :: class);
        $captcha = new Captcha();
        $captcha -> setFontColor('#000');

        $captcha = new Captcha();
        $captcha -> setFontColor(256, 256, 256);

        $captcha = new Captcha();
        $captcha -> setFontColor(-1, -1, -1);
    }


    /**
     * Testing setting font size
     * Testing setting invalid font size
     * @return void
     */
    public function testSettingFontSize(): void {

        $captcha = new Captcha();
        $captcha -> setFontSize(10);
        $this -> assertEquals(['min' => 10, 'max' => 10], $captcha -> getFontSize());
        $captcha -> setFontSize(10, 20);
        $this -> assertEquals(['min' => 10, 'max' => 20], $captcha -> getFontSize());

        $this -> expectException(ErrorException :: class);
        $captcha -> setFontSize(-10);
        $captcha -> setFontSize(-1);
        $captcha -> setFontSize(-1, -1);
    }


    /**
     * Testing setting font size
     * Testing setting invalid font size
     * @return void
     */
    public function testSettingFontAngle(): void {

        $captcha = new Captcha();
        $captcha -> setFontAngle(10);
        $this -> assertEquals(['min' => 10, 'max' => 10], $captcha -> getFontAngle());
        $captcha -> setFontAngle(10, 20);
        $this -> assertEquals(['min' => 10, 'max' => 20], $captcha -> getFontAngle());

        $this -> addToAssertionCount(1);
    }


    /**
     * Testing setting noise level
     * Testing setting invalid noise level
     * @return void
     */
    public function testSettingNoise(): void {

        $captcha = new Captcha();
        $this -> assertNull($captcha -> getNoise());
        $captcha -> setNoise(10);
        $this -> assertEquals(10, $captcha -> getNoise());

        $this -> expectException(ErrorException :: class);
        $captcha -> setNoise(0);
        $captcha -> setNoise(-1);
    }


    /**
     * Testing setting the captcha text manually
     * @return void
     */
    public function testSettingText(): void {

        $captcha = new Captcha();
        $captcha -> setText('test');
        $this -> assertEquals('test', $captcha -> getText());
    }


    /**
     * Testing generating captcha
     * @return void
     */
    public function testGenerationCaptcha(): void {

        $captcha = new Captcha();
        $text = $captcha -> generateText(5);

        $captcha -> setBackgroundImage($this -> path . 'captcha-bg.jpg');
        $captcha -> setText($text);
        $captcha -> setFont($this -> path . 'captcha.ttf');
        $captcha -> generate($this -> path . 'captcha.png');
        $captcha -> generate($this -> path . 'captcha.jpg');

        $this -> assertFileExists($this -> path . 'captcha.png');
        $this -> assertFileExists($this -> path . 'captcha.jpg');

        $this -> expectException(ErrorException :: class);
        $captcha = new Captcha();
        $captcha -> generate();

        $captcha = new Captcha();
        $captcha -> setBackgroundImage($this -> path . 'captcha-bg.jpg');
        $captcha -> generate();

        $captcha = new Captcha();
        $captcha -> setFont($this -> path . 'captcha.ttf');
        $captcha -> generate();
    }
}