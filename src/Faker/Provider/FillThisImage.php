<?php
declare(strict_types=1);

namespace TeaRiot\FakerFillThis\Faker\Provider;

use Faker\Factory;
use Faker\Provider\Base;
use Faker\Provider\Lorem;
use Random\RandomException;

/**
 * Class FillThisImage
 *
 * This provider generates image URLs and downloads images from the fillthis.io service.
 * The imageUrl, url, and image methods conform to the Laravel Faker provider interface.
 *
 * @property string $imageUrl
 * @method string imageUrl(int $width = 640, int $height = 480, ?string $category = null, bool $randomize = true, ?string $word = null, bool $gray = false, string $format = 'png')
 *
 * @property string $image
 * @method string image(?string $dir = null, int $width = 640, int $height = 480, ?string $category = null, bool $fullPath = true, bool $randomize = true, ?string $word = null, bool $gray = false, string $format = 'png')
 *
 * @property string $url
 * @method string url(?string $type = null)
 */
class FillThisImage extends Base
{
    /**
     * Base URL for the fillthis.io service.
     */
//    public const BASE_URL = 'https://fillthis.io';
     public const BASE_URL = 'https://fill.this.local';

    public const FORMAT_JPG  = 'jpg';
    public const FORMAT_JPEG = 'jpeg';
    public const FORMAT_PNG  = 'png';
    public const FORMAT_WEBP = 'webp';

    /**
     * List of categories available in the API.
     *
     * @deprecated Categories are now referenced as strings in the API.
     * To obtain the complete list of categories, call the API at https://fill.this.local/categories
     *
     * @var string[]
     */
    protected static array $categories = [
        'abstract', 'animals', 'business', 'cats', 'city', 'food', 'nightlife',
        'fashion', 'people', 'nature', 'sports', 'technics', 'transport'
    ];

    /**
     * Generates an image URL from the fillthis.io service.
     *
     * Examples:
     *   1. Default image:
     *       https://fillthis.io/i
     *
     *   2. Image with dimensions 800x600 and text "TestText":
     *       https://fillthis.io/i/800x600.png?text=TestText
     *
     *   3. Image with single number (500x500):
     *       https://fillthis.io/i/500.png
     *
     *   4. Image with random parameter:
     *       https://fillthis.io/i/800x600.png?text=<randomWord>
     *
     *   5. Image with custom colors (background red, text green):
     *       https://fillthis.io/i/800x600/FF0000/00FF00.jpg?text=Custom
     *
     *   6. Image from a category:
     *       https://fillthis.io/i/category/{category}
     *
     * Note: The standard image URL uses the PNG extension regardless of the specified format.
     *
     * @param int         $width     Logical width of the image.
     * @param int         $height    Logical height of the image.
     * @param string|null $category  Category name (if any, including "all").
     * @param bool        $randomize If true, adds a random word (via Lorem::word()) to the text parameter.
     * @param string|null $word      Word to include in the text parameter.
     * @param bool        $gray      Not used in this implementation but reserved for future enhancements.
     * @param string      $format    Desired image format (png, jpg, webp). Allowed formats are validated.
     *
     * @return string Generated image URL.
     * @throws RandomException If random bytes generation fails.
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
            self::FORMAT_JPG,
            self::FORMAT_JPEG,
            self::FORMAT_PNG,
            self::FORMAT_WEBP,
        ];
        $format = strtolower($format);
        if (!in_array($format, $allowedFormats, true)) {
            throw new \InvalidArgumentException(sprintf(
                'Invalid image format "%s". Allowed formats are: %s',
                $format,
                implode(', ', $allowedFormats)
            ));
        }

        $query = '';
        if ($category !== null) {
            // Return image from a specific category, with a seed for reproducibility.
            $path = '/i/category/' . urlencode($category);
            $query = '?seed=' . self::seed();
        } else {
            // If both dimensions are zero, return the default endpoint.
            if ($width === 0 && $height === 0) {
                $path = '/i';
            } else {
                // Use a single number if width equals height; otherwise, use "widthxheight".
                $sizePart = ($width === $height) ? (string)$width : sprintf('%dx%d', $width, $height);
                // Standard images are forced to use PNG extension.
                $path = '/i/' . $sizePart . '.png';
            }

            $textParts = [];
            if ($word !== null) {
                $textParts[] = $word;
            }
            if ($randomize === true) {
                $textParts[] = Lorem::word();
            }
            if (count($textParts) > 0) {
                $query = '?text=' . urlencode(implode(' ', $textParts));
            }
        }

        return self::BASE_URL . $path . $query;
    }

    /**
     * Returns a URL.
     *
     * If the type is "video", returns a video URL with a seed for reproducibility.
     * The URL now includes a .mp4 extension so that linters or frontend tools can clearly identify it as a video.
     * Otherwise, returns a random URL generated by Faker.
     *
     * @param string|null $type Type of URL (e.g., "video").
     *
     * @return string Generated URL.
     * @throws RandomException If seed generation fails.
     */
    public static function url(?string $type = null): string
    {
        if ($type === 'video') {
            return self::BASE_URL . '/video.mp4?seed=' . self::seed();
        }

        return Factory::create()->url();
    }

    /**
     * Downloads the image from the generated URL and saves it to disk.
     *
     * The file is saved as a PNG even if a different format is specified (standard mode).
     * Requires either cURL or allow_url_fopen to be enabled.
     *
     * @param string|null $dir       Directory to save the file (default: system temporary directory).
     * @param int         $width     Logical width of the image.
     * @param int         $height    Logical height of the image.
     * @param string|null $category  Category name (if any, including "all").
     * @param bool        $fullPath  If true, returns the full file path; otherwise, only the filename.
     * @param bool        $randomize Whether to add a random word to the text parameter.
     * @param string|null $word      Word to include in the text parameter.
     * @param bool        $gray      Not used in this implementation but reserved for future enhancements.
     * @param string      $format    Image format (png, jpg, webp) – only used for custom color mode.
     *
     * @return string|false File path (or filename) of the saved image, or false on failure.
     * @throws RandomException If seed generation fails.
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

        // Generate a unique filename based on server address (if available) to minimize collisions.
        $uniqueSource = $_SERVER['SERVER_ADDR'] ?? '';
        $name = md5(uniqid($uniqueSource, true));
        // Standard images are saved as PNG.
        $filename = sprintf('%s.png', $name);
        $filepath = rtrim($dir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $filename;

        $url = self::imageUrl($width, $height, $category, $randomize, $word, $gray, $format);

        if (function_exists('curl_exec')) {
            $fp = fopen($filepath, 'w');
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_FILE, $fp);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_FAILONERROR, true);
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
            throw new \RuntimeException('cURL or allow_url_fopen is required to download images.');
        }

        return $fullPath ? $filepath : $filename;
    }

    /**
     * Returns a list of supported image formats.
     *
     * @return array List of supported image format strings.
     */
    public static function getFormats(): array
    {
        return array_keys(self::getFormatConstants());
    }

    /**
     * Returns an array mapping supported formats to their PHP image type constants.
     *
     * Note: WebP is not included as its constant may not be available in all PHP versions.
     *
     * @return array Associative array mapping format strings to PHP image type constants.
     */
    public static function getFormatConstants(): array
    {
        return [
            self::FORMAT_JPG  => defined('IMAGETYPE_JPEG') ? IMAGETYPE_JPEG : 2,
            self::FORMAT_JPEG => defined('IMAGETYPE_JPEG') ? IMAGETYPE_JPEG : 2,
            self::FORMAT_PNG  => defined('IMAGETYPE_PNG')  ? IMAGETYPE_PNG  : 3,
            // Optionally, add support for WebP if available:
            // self::FORMAT_WEBP => defined('IMAGETYPE_WEBP') ? IMAGETYPE_WEBP : 18,
        ];
    }

    /**
     * Generates a random seed string.
     *
     * This method creates a version 4 UUID-like string based on random bytes.
     *
     * @return string Random seed string.
     * @throws RandomException If random bytes generation fails.
     */
    private static function seed(): string
    {
        $data = random_bytes(16);
        // Set version to 0100
        $data[6] = chr((ord($data[6]) & 0x0f) | 0x40);
        // Set bits 6-7 to 10
        $data[8] = chr((ord($data[8]) & 0x3f) | 0x80);

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}