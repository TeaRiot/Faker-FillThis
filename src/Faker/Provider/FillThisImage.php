<?php

namespace TeaRiot\FakerFillThis\Faker\Provider;

use Faker\Provider\Base;
use Faker\Provider\Lorem;

class FillThisImage extends Base
{
    /**
     * Base URL for the fillthis.io service.
     */
    public const BASE_URL = 'https://fillthis.io';

    public const FORMAT_JPG  = 'jpg';
    public const FORMAT_JPEG = 'jpeg';
    public const FORMAT_PNG  = 'png';
    public const FORMAT_WEBP = 'webp';

    /**
     * List of categories available in the API.
     *
     * @deprecated Categories are now referenced as strings in the API.
     *
     * @var string[]
     */
    protected static array $categories = [
        'abstract', 'animals', 'business', 'cats', 'city', 'food', 'nightlife',
        'fashion', 'people', 'nature', 'sports', 'technics', 'transport', 'anime'
    ];

    /**
     * Generate the URL that will return an image.
     *
     * Set randomize to false to remove the random GET parameter.
     *
     * Examples:
     *   - 2.1. Default image:
     *       https://fillthis.io/i
     *
     *   - 2.2. Image with dimensions 800x600 and text "TestText":
     *       https://fillthis.io/i/800x600.png?text=TestText
     *
     *   - 2.3. Image with single number (500x500):
     *       https://fillthis.io/i/500.png
     *
     *   - 2.4. Retina image with multiplier 2 (400@2x):
     *       https://fillthis.io/i/400@2x.png
     *
     *   - 2.5. Retina image with multiplier 3 (800x600@3x):
     *       https://fillthis.io/i/800x600@3x.png?text=Hello
     *
     *   - 2.6. Image with random parameter:
     *       https://fillthis.io/i/800x600.png?text=<randomWord>
     *
     *   - 2.7. Image with custom colors (background red, text green):
     *       https://fillthis.io/i/800x600/FF0000/00FF00.jpg?text=Custom
     *
     *   - 2.8. Image with WebP and custom colors (background green, text red):
     *       https://fillthis.io/i/800x600/00FF00/FF0000.webp?text=WebP
     *
     *   - 3.1. Category placeholder for nonexistent category:
     *       https://fillthis.io/i/category/nonexistent/800x600.png
     *
     *   - 3.2. Image from category "anime" (default):
     *       https://fillthis.io/i/category/anime
     *
     *   - 3.3. Image from category "anime" with dimensions 800x600:
     *       https://fillthis.io/i/category/anime/800x600.png
     *
     *   - 3.4. Retina image from category "anime" (800x600@2x, WebP):
     *       https://fillthis.io/i/category/anime/800x600@2x.webp
     *
     *   - 5.x. Font examples: appended as GET parameter, e.g. ?font=lato
     *
     * Additionally, the special category "all" is supported. Passing "all" as the category
     * will return a random image from all categories.
     *
     * @param int         $width     Logical width of the image.
     * @param int         $height    Logical height of the image.
     * @param string|null $category  Category name (if any, including "all").
     * @param bool        $randomize If true, adds a random word (using Lorem::word()) to the text parameter.
     * @param string|null $word      Word to include in the text parameter.
     * @param bool        $gray      If true, uses a gray background (CCCCCC); otherwise a random safe color is used.
     * @param string      $format    Desired image format (png, jpg, webp). However, for standard images the extension is forced to .png.
     *
     * @return string Image URL.
     */
    public static function imageUrl(
        int $width = 640,
        int $height = 480,
        ?string $category = null,
        bool $randomize = true,
        ?string $word = null,
        bool $gray = false,
        string $format = 'png'
    ): string {
        $allowedFormats = [
            static::FORMAT_JPG,
            static::FORMAT_JPEG,
            static::FORMAT_PNG,
            static::FORMAT_WEBP,
        ];
        if (!in_array(strtolower($format), $allowedFormats, true)) {
            throw new \InvalidArgumentException(sprintf(
                'Invalid image format "%s". Allowed formats are: %s',
                $format,
                implode(', ', $allowedFormats)
            ));
        }

        // If width and height are zero, return the default endpoint.
        if ($width === 0 && $height === 0) {
            $path = $category !== null ? '/i/category/' . urlencode($category) : '/i';
        } else {
            // Use single number if width equals height; otherwise, use "widthxheight".
            $sizePart = ($width === $height) ? (string)$width : sprintf('%dx%d', $width, $height);
            if ($category !== null) {
                $path = '/i/category/' . urlencode($category) . '/' . $sizePart;
            } else {
                $path = '/i/' . $sizePart;
            }
            $path .= '.png';
        }
        $query = '';

        $textParts = [];
        if ($word !== null) {
            $textParts[] = $word;
        }
        if ($randomize === true) {
            $textParts[] = Lorem::word();
        }
        if (count($textParts) > 0) {
            $query = ($query === '' ? '?' : '&') . 'text=' . urlencode(implode(' ', $textParts));
        }

        return self::BASE_URL . $path . $query;
    }

    /**
     * Download the image from the generated URL and save it to disk.
     *
     * Requires cURL or allow_url_fopen.
     *
     * @param string|null $dir        Directory to save the file (default: system temporary directory).
     * @param int         $width      Logical width of the image.
     * @param int         $height     Logical height of the image.
     * @param string|null $category   Category name (if any, including "all").
     * @param bool        $fullPath   If true, returns the full file path; otherwise, only the filename.
     * @param bool        $randomize  Whether to add a random word to the text parameter.
     * @param string|null $word       Word to include in the text parameter.
     * @param bool        $gray       If true, uses a gray background.
     * @param string      $format     Image format (png, jpg, webp) – only used for custom color mode.
     *
     * @return string|false File path of the saved image, or false on failure.
     */
    public static function image(
        ?string $dir = null,
        int $width = 640,
        int $height = 480,
        ?string $category = null,
        bool $fullPath = true,
        bool $randomize = true,
        ?string $word = null,
        bool $gray = false,
        string $format = 'png'
    ): string|false {
        $dir = $dir ?? sys_get_temp_dir();
        if (!is_dir($dir) || !is_writable($dir)) {
            throw new \InvalidArgumentException(sprintf('Cannot write to directory "%s"', $dir));
        }

        // Generate a random filename using the server address to minimize collisions.
        $name = md5(uniqid(empty($_SERVER['SERVER_ADDR']) ? '' : $_SERVER['SERVER_ADDR'], true));
        $filename = sprintf('%s.png', $name);
        $filepath = rtrim($dir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $filename;

        $url = self::imageUrl($width, $height, $category, $randomize, $word, $gray, $format);

        if (function_exists('curl_exec')) {
            $fp = fopen($filepath, 'w');
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_FILE, $fp);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            $success = curl_exec($ch) && curl_getinfo($ch, CURLINFO_HTTP_CODE) === 200;
            curl_close($ch);
            fclose($fp);

            if (!$success) {
                unlink($filepath);
                return false;
            }
        } elseif (ini_get('allow_url_fopen')) {
            $success = copy($url, $filepath);
            if (!$success) {
                return false;
            }
        } else {
            throw new \RuntimeException('cURL or allow_url_fopen is required.');
        }

        return $fullPath ? $filepath : $filename;
    }

    /**
     * Return the list of supported image formats.
     *
     * @return array
     */
    public static function getFormats(): array {
        return array_keys(static::getFormatConstants());
    }

    /**
     * Return an array mapping formats to their PHP constants.
     *
     * @return array
     */
    public static function getFormatConstants(): array {
        return [
            static::FORMAT_JPG => constant('IMAGETYPE_JPEG'),
            static::FORMAT_JPEG => constant('IMAGETYPE_JPEG'),
            static::FORMAT_PNG => constant('IMAGETYPE_PNG'),
        ];
    }
}