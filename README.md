# Glide

Glide is a wonderfully easy image manipulation library written in PHP. It's straightforward API is exposed via HTTP, similar to cloud image processing services like [Imgix](http://www.imgix.com/) and [Cloudinary](http://cloudinary.com/). It doesn't try to reinvent the wheel, but leverages powerful libraries like [Intervention Image](http://image.intervention.io/) (image handling and manipulation) and [Flysystem](http://flysystem.thephpleague.com/) (file system abstraction). Glide was created by [Jonathan Reinink](https://twitter.com/reinink).

[![© Photo Joel Reynolds](https://glide.herokuapp.com/kayaks.jpg?w=1000)](https://glide.herokuapp.com/kayaks.jpg?w=1000)
> © Photo Joel Reynolds

## Highlights

- Adjust, resize and add effects to images using a simple HTTP based API.
- Manipulated images are automatically cached and served with far-future expires headers.
- Create your own image processing server or integrate Glide directly into your app.
- Supports both the [GD](http://php.net/manual/en/book.image.php) library and the [Imagick](http://php.net/manual/en/book.imagick.php) PHP extension.
- Ability to secure image URLs using a private signing key.
- Works with many different file systems, thanks to the [Flysystem](http://flysystem.thephpleague.com/) library.
- Powered by the battle tested [Intervention Image](http://image.intervention.io/) image handling and manipulation library.

## The API

### Size

- **Width** `w`
    - Sets the width of the image, in pixels.
    - Example: [kayaks.jpg?w=1000](https://glide.herokuapp.com/kayaks.jpg?w=1000)
- **Height** `h`
    - Sets the height of the image, in pixels.
    - Example: [kayaks.jpg?h=500](https://glide.herokuapp.com/kayaks.jpg?h=500)
- **Fit** `fit`
    - Sets how the image is fitted to its target dimensions.
    - Accepts `clip`, `scale` or `crop`. Default is `clip`.
    - Example: [kayaks.jpg?w=500&fit=crop](https://glide.herokuapp.com/kayaks.jpg?w=500&fit=crop)
- **Crop Position** `crop`
    - Sets where the image is cropped when the `fit` parameter is set to `crop`.
    - Accepts `top-left`, `top`, `top-right`, `left`, `center`, `right`, `bottom-left`, `bottom` or `bottom-right`. Default is `center`.
    - Example: [kayaks.jpg?w=500&fit=crop&crop=left](https://glide.herokuapp.com/kayaks.jpg?w=500&fit=crop&crop=left)
- **Rectangle** `rect`
    - Crops the image to specific dimensions.
    - Happens prior to any other resize operations.
    - Required format: `width,height,x,y`
    - Example: [kayaks.jpg?rect=100,100,915,155](https://glide.herokuapp.com/kayaks.jpg?rect=100,100,915,155)
- **Orientation** `ori`
    - Rotates the image.
    - Accepts `auto`, `0`, `90`, `180` or `270`. Default is `auto`.
    - The `auto` option uses Exif data to automatically orient images correctly.
    - Example: [kayaks.jpg?h=500&ori=90](https://glide.herokuapp.com/kayaks.jpg?h=500&ori=90)

### Adjustments

- **Brightness** `bri`
    - Adjusts the image brightness.
    - Use values between `-100` and `+100`, where `0` represents no change.
    - Example: [kayaks.jpg?w=1000&bri=50](https://glide.herokuapp.com/kayaks.jpg?w=1000&bri=50)
- **Contrast** `con`
    - Adjusts the image contrast.
    - Use values between `-100` and `+100`, where `0` represents no change.
    - Example: [kayaks.jpg?w=1000&con=50](https://glide.herokuapp.com/kayaks.jpg?w=1000&con=50)
- **Gamma** `gam`
    - Adjusts the image gamma.
    - Example: [kayaks.jpg?w=1000&gam=2](https://glide.herokuapp.com/kayaks.jpg?w=1000&gam=2)
- **Sharpen** `sharp`
    - Sharpen the image.
    - Use values between `0` and `100`.
    - Example: [kayaks.jpg?w=1000&sharp=15](https://glide.herokuapp.com/kayaks.jpg?w=1000&sharp=15)

### Effects

- **Blur** `blur`
    - Adds a blur effect to the image.
    - Use values between `0` and `100`.
    - Example: [kayaks.jpg?w=1000&blur=15](https://glide.herokuapp.com/kayaks.jpg?w=1000&blur=15)
- **Pixelate** `pixel`
    - Applies a pixelation effect to the image.
    - Use values between `0` and `1000`.
    - Example: [kayaks.jpg?w=1000&pixel=12](https://glide.herokuapp.com/kayaks.jpg?w=1000&pixel=12)
- **Filter** `filt`
    - Applies a filter effect to the image.
    - Accepts `greyscale` or `sepia`.
    - Example: [kayaks.jpg?w=1000&filt=sepia](https://glide.herokuapp.com/kayaks.jpg?w=1000&filt=sepia)

### Output

- **Quality** `q`
    - Defines the quality of the image.
    - Use values between `0` and `100`. Defaults to `90`.
    - Only relevant if the format is set to `jpg`.
    - Example: [kayaks.jpg?w=1000&q=50](https://glide.herokuapp.com/kayaks.jpg?w=1000&q=50)
- **Format** `fm`
    - Encodes the image to a specific format.
    - Accepts `jpg`, `png` or `gif`. Defaults to `jpg`.
    - Example: [kayaks.jpg?w=1000&fm=png](https://glide.herokuapp.com/kayaks.jpg?w=1000&fm=png)

## Example

The following example illustrates how easy Glide is to configure. This particular example uses Amazon S3 as the image source, and a local disk as the image cache (where the manipulated images are saved).

```php
use Aws\S3\S3Client;
use League\Flysystem\Adapter\AwsS3 as S3Adapter;
use League\Flysystem\Adapter\Local as LocalAdapter;
use League\Flysystem\Filesystem;

// Connect to S3 account
$s3Client = S3Client::factory([
    'key' => 'your-key',
    'secret' => 'your-secret',
]);

// Setup Glide server
$glide = Glide\Factory::server([
    'source' => new Filesystem(new S3Adapter($s3Client, 'bucket-name')),
    'cache' => new Filesystem(new LocalAdapter('cache-folder')),
]);

// Output image based on the current URL
$glide->output($_SERVER['SCRIPT_NAME'], $_GET);
```

## Configuration Options

### Source & Cache

Glide makes it possible to access images stored in a variety of file systems. It does this using the [Flysystem](http://flysystem.thephpleague.com/) file system abstraction library. For example, you may choose to store your source images on [Amazon S3](http://aws.amazon.com/s3/), but keep your rendered images (the cache) on a local disk.

To set your source and cache locations, simply pass an instance of `League\Flysystem\Filesystem` for each. Alternatively, if you are only using the local disk, you can simply pass a path as a string.

```php
use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Local;

// Setup Glide server
$glide = Glide\Factory::server([
    'source' => new Filesystem(new Local('source-folder')),
    'cache' => new Filesystem(new Local('cache-folder')),
]);

// Pass strings when using local disk only
$glide = Glide\Factory::server([
    'source' => 'source-folder',
    'cache' => 'cache-folder',
]);
```

### Driver

By default Glide uses the [GD](http://php.net/manual/en/book.image.php) library. However you can also use Glide with [Imagemagick](http://www.imagemagick.org/) if the [Imagick](http://php.net/manual/en/book.imagick.php) PHP extension is installed.

```php
// Set driver in Glide configuration
$glide = Glide\Factory::server([
    'driver' => 'imagick',
]);
```

### Securing Images

If you want additional security on your images, you can add a secure signature so that no one can alter the image parameters. Start by setting a signing key in your Glide server:

```php
// Add signing key in Glide configuration
$glide = Glide\Factory::server([
    'sign_key' => 'your-sign-key',
]);
```

Next, generate a secure token whenever you request an image from your server. For example, instead of requesting `image.jpg?w=1000`, you would instead request `image.jpg?w=1000&token=6db10b02a4132a8714b6485d1138fc87`. Glide comes with a URL builder to make this process easy.

```php
// Create an instance of the URL builder
$urlBuilder = new Glide\UrlBuilder('http://your-website.com', 'your-sign-key');

// Generate a url
$url = $urlBuilder->getUrl('image.jpg', ['w' => 1000]);

// Use the url in your app
echo '<img src="' . $url . '">';

// Prints out
// <img src="http://your-website.com/image.jpg?w=1000&token=af3dc18fc6bfb2afb521e587c348b904">
```

### Max Image Size

If you're not securing images with a signing key, you can choose to limit how large images can be generated. The following setting will set the maximum allowed total image size, in pixels.

```php
// Set max image size in Glide configuration
$glide = Glide\Factory::server([
    'max_image_size' => 2000*2000,
]);
```