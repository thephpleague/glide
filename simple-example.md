---
layout: default
permalink: simple-example/
title: Simple Example
---

# Simple Example

The following example illustrates how easy Glide is to configure. This particular example uses Amazon S3 as the image source (where the original images are saved) and the local disk as the image cache (where the manipulated images are saved).

~~~ php
use Aws\S3\S3Client;
use League\Flysystem\Adapter\AwsS3 as S3Adapter;
use League\Flysystem\Adapter\Local as LocalAdapter;
use League\Flysystem\Filesystem;
use League\Glide\Factories\Server;
use Symfony\Component\HttpFoundation\Request;

// Connect to S3 account
$s3Client = S3Client::factory([
    'key' => 'your-key',
    'secret' => 'your-secret',
]);

// Setup Glide server
$glide = Server::create([
    'source' => new Filesystem(new S3Adapter($s3Client, 'bucket-name')),
    'cache' => new Filesystem(new LocalAdapter('cache-folder')),
]);

// Create request object using HttpFoundation
$request = Request::createFromGlobals();

// Output image based on the current URL
$glide->outputImage(
    $request->getPathInfo(),
    $request->query->all()
);
~~~ 