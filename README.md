# Faker-FillThis Provider

Faker-FillThis is a custom provider for [Faker](https://github.com/fakerphp/faker) that generates image URLs and
downloads images from [fillthis.io](https://fillthis.io). It supports default images, images with specific dimensions
and text, category images (including the special "all" category for random images from all categories), and font
parameters.  
Additionally, the library extends the standard URL interface: if you pass `"video"` as the type to the `url()` method (
accessed via `$faker->url('video')`), it will make an HTTP request to the `/video` route, retrieve the final video URL,
and return that URL in a JSON object. For any other type, it behaves as usual.

> **Note:** Retina images are not supported by this provider.

## Installation

Install the package via Composer:

```bash
  composer require teariot/Faker-FillThis
```

Then update the Composer autoloader:

```bash
  composer dump-autoload
```

## Adding the Provider

To add the provider to your Faker instance, include it in your code as follows:

```php
use Faker\Factory;
use TeaRiot\FakerFillThis\Faker\Provider\FillThisImage;

$faker = Factory::create();
$faker->addProvider(new FillThisImage($faker));
```

## Direct Link Mode

The provider supports a direct link mode. If you call the chainable setter `->setDirectLink(true)` on your provider
instance, the `imageUrl()` method will perform an HTTP request to follow redirects and return the final direct URL of
the image file.

Example:

```php
$fakerDirect = Factory::create();
$fakerDirect->addProvider((new FillThisImage($fakerDirect))->setDirectLink(true));

echo $fakerDirect->imageUrl(0, 0, "all", false, null, false, "png");
// Output: Direct URL to the file (e.g., "https://fillthis.io/storage/images/...")
```

## Video URL Method

When you need a video URL, call the `url()` method with `"video"` as the type using `$faker->url('video')`. This method
makes an HTTP request to the `/video` route, retrieves the JSON response, extracts the video URL, and returns it in a
JSON object.

Example:

```php
echo $faker->url('video');
// Example output:
// Video URL: https://youtu.be/QJO3ROT-A4E
```

For any other type, the method returns a randomly generated URL.

## Usage Examples

### Generating Image URLs

- **Default image (no dimensions):**

  ```php
  echo $faker->imageUrl(0, 0);
  // Output: https://fillthis.io/i
  ```

- **Image with dimensions and text ("TestText"):**

  ```php
  echo $faker->imageUrl(800, 600, null, false, "TestText", false, "png");
  // Output: https://fillthis.io/i/800x600.png?text=TestText
  ```

- **Square image (500x500):**

  ```php
  echo $faker->imageUrl(500, 500, null, false, null, false, "png");
  // Output: https://fillthis.io/i/500.png
  ```

- **Image with random text:**

  ```php
  echo $faker->imageUrl(800, 600, null, true, null, false, "png");
  // Output: https://fillthis.io/i/800x600.png?text=<randomWord>
  ```

### Category Images

- **Image from a specific category ("fashion"):**

  ```php
  echo $faker->imageUrl(0, 0, "fashion", false, null, false, "png");
  // Output: https://fillthis.io/i/category/fashion
  ```

- **Image from a category with dimensions (e.g., 800x600 from "fashion"):**

  ```php
  echo $faker->imageUrl(800, 600, "fashion", false, null, false, "png");
  // Output: https://fillthis.io/i/category/fashion/800x600.png
  ```

- **Image from the special "all" category:**

  ```php
  echo $faker->imageUrl(0, 0, "all", false, null, false, "png");
  // Output: https://fillthis.io/i/category/all
  ```

- **Image from the "all" category with dimensions:**

  ```php
  echo $faker->imageUrl(800, 600, "all", false, null, false, "png");
  // Output: https://fillthis.io/i/category/all/800x600.png
  ```

### Video URL

To get a video URL (using the `/video` route):

```php
echo $faker->url('video');
// Example output:
// Video URL: https://youtu.be/QJO3ROT-A4E
```

### Font Parameter Examples

You can append a font parameter to the generated URL. For example:

```php
echo $faker->imageUrl(800, 600, null, false, "Custom", false, "png") . "&font=lato";
// Output: https://fillthis.io/i/800x600.png?text=Custom&font=lato
```

Other supported fonts include:
- lato
- lora
- montserrat
- noto-sans
- open-sans
- oswald
- playfair-display
- poppins
- pt-sans
- raleway
- roboto
- source-sans-pro

If the specified font is not available, the API defaults to using Roboto.

### Downloading Images

The provider also supports downloading images to disk using the `image()` method:

```php
use TeaRiot\FakerFillThis\Faker\Provider\FillThisImage;

$imagePath = $faker->image(__DIR__, 800, 600, null, true, true, "TestImage", false, "png");
if ($imagePath !== false) {
    echo "Image downloaded at: " . $imagePath;
} else {
    echo "Error downloading image.";
}
```

## Categories

List of categories: [https://fillthis.io/categories](https://fillthis.io/categories)  
Images for category "
fashion": [https://fillthis.io/categories/fashion/images](https://fillthis.io/categories/fashion/images)

Passing `"all"` as the category is supported and will return a random image from all categories.