# Faker-FillThis Provider

Faker-FillThis is a custom provider for [Faker](https://github.com/fakerphp/faker) that generates image URLs and downloads images from [fillthis.io](https://fillthis.io). It supports default images, images with specific dimensions and text, category images (including the special "all" category for random images from all categories), and font parameters.

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

## Usage Examples

### Generating Image URLs

- **Default image (no dimensions):**

  ```php
  echo FillThisImage::imageUrl(0, 0);
  // Output: https://fillthis.io/i
  ```

- **Image with dimensions and text ("TestText"):**

  ```php
  echo FillThisImage::imageUrl(800, 600, null, false, "TestText", false, "png");
  // Output: https://fillthis.io/i/800x600.png?text=TestText
  ```

- **Square image (500x500):**

  ```php
  echo FillThisImage::imageUrl(500, 500, null, false, null, false, "png");
  // Output: https://fillthis.io/i/500.png
  ```

- **Image with random text:**

  ```php
  echo FillThisImage::imageUrl(800, 600, null, true, null, false, "png");
  // Output: https://fillthis.io/i/800x600.png?text=<randomWord>
  ```

### Category Images

- **Image from a specific category ("anime"):**

  ```php
  echo FillThisImage::imageUrl(0, 0, "anime", false, null, false, "png");
  // Output: https://fillthis.io/i/category/anime
  ```

- **Image from a category with dimensions (e.g., 800x600 from "anime"):**

  ```php
  echo FillThisImage::imageUrl(800, 600, "anime", false, null, false, "png");
  // Output: https://fillthis.io/i/category/anime/800x600.png
  ```

- **Image from the special "all" category:**

  ```php
  echo FillThisImage::imageUrl(0, 0, "all", false, null, false, "png");
  // Output: https://fillthis.io/i/category/all
  ```

- **Image from the "all" category with dimensions:**

  ```php
  echo FillThisImage::imageUrl(800, 600, "all", false, null, false, "png");
  // Output: https://fillthis.io/i/category/all/800x600.png
  ```

### Font Parameter Examples

You can append a font parameter to the generated URL. For example:

```php
echo FillThisImage::imageUrl(800, 600, null, false, "Custom", false, "png") . "&font=lato";
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

The provider also supports downloading images to disk using the `image` method. For example:

```php
use TeaRiot\FakerFillThis\Faker\Provider\FillThisImage;

$imagePath = FillThisImage::image(__DIR__, 800, 600, null, true, true, "TestImage", false, "png");
if ($imagePath !== false) {
    echo "Image downloaded at: " . $imagePath;
} else {
    echo "Error downloading image.";
}
```

## Running the Demo

A complete demonstration is provided in the `test.php` file. To run the demo, execute:

```bash
php test.php
```

## Categories (Static Data)

The provider includes a static list of categories (for reference):

```json
[
  { "id": 1, "name": "abstract" },
  { "id": 2, "name": "animals" },
  { "id": 3, "name": "business" },
  { "id": 4, "name": "cats" },
  { "id": 5, "name": "city" },
  { "id": 6, "name": "food" },
  { "id": 7, "name": "nightlife" },
  { "id": 8, "name": "fashion" },
  { "id": 9, "name": "people" },
  { "id": 10, "name": "nature" },
  { "id": 11, "name": "sports" },
  { "id": 12, "name": "technics" },
  { "id": 13, "name": "transport" },
  { "id": 14, "name": "anime" }
]
```

Passing `"all"` as the category is supported and will return a random image from all categories.