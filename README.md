# Glide

The purpose of this library is to offer extremely easy image manipulation in a similar fashion to cloud image processing services like [Imgix](http://www.imgix.com/) and [Cloudinary](http://cloudinary.com/).

## Highlights

- Adjust, resize and add effects to images using a simple URL based API.
- Manipulated images are automatically cached and served with far-future expires headers.
- Create your own image processing server or integrate directly into your app.
- Supports the [GD Library](http://php.net/manual/en/book.image.php) and [Imagick PHP extension](http://php.net/manual/en/book.imagick.php).
- Ability to secure image URLs using a private signing key.
- Works with many different file systems, using the [Flysystem](http://flysystem.thephpleague.com/) library.
- Powered by the [Intervention Image](http://image.intervention.io/) image handling and manipulation library.

## The API

- **Width** `w`
    - The width in pixels of the output image.
    - Example: `image.jpg?w=300`
- **Height** `h`
    - The height in pixels of the output image.
    - Example: `image.jpg?h=300`
- **Fit** `fit`
    - Controls how the output image is fitted to its target dimensions.
    - Accepts: `clip`, `scale`, `crop`
    - Example: `image.jpg?w=300&fit=crop`
- **Rectangle** `rect`
    - Crops an image to specific dimensions.
    - Example: `image.jpg?rect=100,100,25,90`
- **Crop Position** `crop`
    - Controls how the input image is aligned when the `fit` parameter is set to `crop`.
    - Accepts: `top-left`, `top`, `top-right`, `left`, `center`, `right`, `bottom-left`, `bottom`, `bottom-right`
    - Example: `image.jpg?rect=100,100,25,90`
- **Orientation** `orient`
    - Rotates an image by supplied angle.
    - By default it uses Exif data to automatically orient images correctly. 
    - Example: `image.jpg?orient=90`
- **Brightness** `bri`
    - Adjusts the image brightness.
    - Use values between `-100` and `+100`.
    - Example: `image.jpg?bri=50`
- **Contrast** `con`
    - Adjusts the image contrast.
    - Use values between `-100` for min. contrast, `0` for no change and `+100` for max. contrast.
    - Example: `image.jpg?con=50`
- **Gamma** `gam`
    - Adjusts the image gamma.
    - Example: `image.jpg?gam=1.6`
- **Blur** `blur`
    - Blurs an image by supplied blur strength.
    - Use values between `0` and `100`.
    - Example: `image.jpg?blur=15`

## Example

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

// Setup server and define source and cache
$glide = new Glide\Server(
    new Filesystem(new S3Adapter($s3Client, 'bucket-name')),
    new Filesystem(new LocalAdapter('cache-folder'))
);

// Enable private URLs
$glide->setSignKey('your-signing-key');

// Output Image
$glide->output(
    $request->getPathInfo(),
    $request->query->all()
);
```