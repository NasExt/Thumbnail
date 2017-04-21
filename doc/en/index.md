NasExt/Thumbnail
===========================

Thumbnail for Nette Framework.

Requirements
------------

NasExt/Thumbnail requires PHP 5.3.2 or higher.

- [Nette Framework](https://github.com/nette/nette)

Installation
------------

The best way to install NasExt/Thumbnail is using  [Composer](http://getcomposer.org/):

```sh
$ composer require nasext/thumbnail
```

Enable the extension using your neon config.

```yml
extensions:
	nasext.thumbnail: NasExt\Thumbnail\DI\ThumbnailExtension
```

Configuration
```yml
nasext.thumbnail:
	thumbsDir: %wwwDir%/thumbs
	prependRoutesToRouter: TRUE
	routes:
        -
            mask: '/themesStorage[/<namespace .+>]/<size>[-<algorithm>]/<filename>.<extension>'
            defaults:
                param1: value1
                storage: themesStorage
        -
            mask: '/<storage>[/<namespace .+>]/<size>[-<algorithm>]/<filename>.<extension>'
            defaults:
                param2: value2
            secured: TRUE
	storages:
        someStorage: SomeStorage(%appDir%/...)
	rules:
        - [width = 100, height = 100, algorithm = fill]
        - [width = 100, height = 50, algorithm = exact]
        - [storage = someStorage, width = 0, height = 0, algorithm = fill]
```

## Using in Latte

This extension gives you new latte macro **n:src**. Now you're ready to use it.

```html
<a n:src="someStorage::products/filename.jpg"><img n:src="someStorage::products/filename.jpg, 200x200, fill, someUrlParam => someurlValue" /></a>
{src products/filename.jpg, '100x100'} // Use default storage registred as first
{img //products/filename.jpg, '100x100'} // Generate absolute url to image
```

Parameters of this macro are:

* **path** - full path to the image with storage name eg.: *someStorage::some/namespace/product-image.jpg*
* **size** - image size. It could be only width or width and height eg.: *150* or *50x50*
* **algorithm** - (optional) resize algorithm which is used to convert image

## Resizing algorithm

For resizing (third argument) you can use these keywords - `fit`, `fill`, `exact`, `stretch`, `shrink_only`. For details see comments above [these constants](http://api.nette.org/2.0/source-common.Image.php.html#105)


## Use in presenter, control...
Inject `\NasExt\Thumbnail\LinkGenerator` and use
```php
$this->thumbnailLinkGenerator->link(array('someStorage::products/filename.jpg', '200x200', 'fill', array('someUrlParam' => 'someurlValue')))
```

-----

Repository [http://github.com/nasext/thumbnail](http://github.com/nasext/thumbnail).
