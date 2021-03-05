---
layout: default
permalink: /
title: Introduction
---

# Introduction

[![Author](https://img.shields.io/badge/author-@reinink-blue.svg?style=flat-square)](https://twitter.com/reinink)
[![Source Code](https://img.shields.io/badge/github-thephpleague/glide-blue.svg?style=flat-square)](https://github.com/thephpleague/glide)
[![Latest Version](https://img.shields.io/github/release/thephpleague/glide.svg?style=flat-square)](https://github.com/thephpleague/glide/releases)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](https://github.com/thephpleague/glide/blob/master/LICENSE)<br>
[![Build Status](https://img.shields.io/github/workflow/status/thephpleague/glide/glide/master?style=flat-square)](https://github.com/thephpleague/glide/actions/workflows/test.yaml?query=branch%3Amaster++)
[![Code Coverage](https://img.shields.io/codecov/c/github/thephpleague/glide/master?style=flat-square)](https://app.codecov.io/gh/thephpleague/glide/)
[![Total Downloads](https://img.shields.io/packagist/dt/league/glide.svg?style=flat-square)](https://packagist.org/packages/league/glide)

Glide is a wonderfully easy on-demand image manipulation library written in PHP. Its straightforward API is exposed via HTTP, similar to cloud image processing services like [Imgix](http://www.imgix.com/) and [Cloudinary](http://cloudinary.com/). Glide leverages powerful libraries like [Intervention Image](http://image.intervention.io/) (for image handling and manipulation) and [Flysystem](http://flysystem.thephpleague.com/) (for file system abstraction).

[![© Photo Joel Reynolds](https://glide.herokuapp.com/2.0/kayaks.jpg?w=1000&gam=.9&sharp=8)](https://glide.herokuapp.com/2.0/kayaks.jpg?w=1000&gam=.9&sharp=8)

<p class="photo_credit">© Photo <a href="http://www.joelreynolds.ca/">Joel Reynolds</a></p>

## Highlights

- Adjust, resize and add effects to images using a simple HTTP based API.
- Manipulated images are automatically cached and served with far-future expires headers.
- Create your own image processing server or integrate Glide directly into your app.
- Supports both the [GD](http://php.net/manual/en/book.image.php) library and the [Imagick](http://php.net/manual/en/book.imagick.php) PHP extension.
- Supports many response methods, including [PSR-7](http://www.php-fig.org/psr/psr-7/), [HttpFoundation](http://symfony.com/doc/current/components/http_foundation/introduction.html) and more.
- Ability to secure image URLs using HTTP signatures.
- Works with many different file systems, thanks to the [Flysystem](http://flysystem.thephpleague.com/) library.
- Powered by the battle tested [Intervention Image](http://image.intervention.io/) image handling and manipulation library.
- Framework-agnostic, will work with any project.
- Composer ready and PSR-2 compliant.

## Questions?

Glide was created by [Jonathan Reinink](https://twitter.com/reinink). Submit issues to [Github](https://github.com/thephpleague/glide/issues).