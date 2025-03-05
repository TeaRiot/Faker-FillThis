# FakerFillThis

FakerFillThis is a custom Faker provider for generating placeholder image URLs and downloading images from [fillthis.io](https://fillthis.io). This provider conforms to the Laravel Faker interface for `imageUrl`, `image`, and `url` methods.

## Installation

Install via Composer:

```bash
  composer require teariot/faker-fill-this
```

## Usage

Add the provider to your Faker instance:

```php
use Faker\Factory;
use TeaRiot\FakerFillThis\Faker\Provider\FillThisImage;

$faker = Factory::create();
$faker->addProvider(new FillThisImage($faker));
```

### Generating an Image URL

- **Default Image:** Returns the default endpoint.
  ```php
  echo $faker->imageUrl(0, 0);
  ```
- **Custom Dimensions:** For a 500x500 image.
  ```php
  echo $faker->imageUrl(500, 500, null, false, null, false, "png");
  ```
- **With Random Word:** Appends a random word to the text parameter.
  ```php
  echo $faker->imageUrl(800, 600, null, true, null, false, "png");
  ```
- **Category-based Image:** When specifying a category (e.g., "fashion" or "all"), the library generates a URL with a seed that directly points to an image from that category. Note that in this mode, the width and height parameters are ignored. This approach is implemented for improved performance by avoiding the need to resize images.
  ```php
  echo $faker->imageUrl(0, 0, "fashion", false, null, false, "png");
  ```

### Generating a Video URL

When the type is set to `"video"`, the URL will include a `.mp4` extension for clear identification:

```php
echo $faker->url('video');
```

### Downloading an Image

Download an image to a specified directory (or system temporary directory by default):

```php
$imagePath = $faker->image(__DIR__, 800, 600, null, true, true, "TestImage", false, "png");
if ($imagePath !== false) {
    echo "Image downloaded at: " . $imagePath;
} else {
    echo "Error downloading image.";
}
```

## API Reference

- **imageUrl**
  ```php
  imageUrl(
      int $width = 640,
      int $height = 480,
      ?string $category = null,
      bool $randomize = true,
      ?string $word = null,
      bool $gray = false,
      string $format = 'png'
  ): string
  ```
- **url**
  ```php
  url(?string $type = null): string
  ```
- **image**
  ```php
  image(
      ?string $dir = null,
      int $width = 640,
      int $height = 480,
      ?string $category = null,
      bool $fullPath = true,
      bool $randomize = true,
      ?string $word = null,
      bool $gray = false,
      string $format = 'png'
  ): string|false
  ```

## Categories

> **Note:** The list of categories is deprecated. To obtain the complete list of categories, call the API at [https://fill.this.local/categories](https://fill.this.local/categories).