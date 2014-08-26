![Kisma](https://github.com/lucifurious/kisma/raw/master/assets/logo-kisma.png)

# Kisma&trade;: PHP Utility Belt v0.2.57
Thanks for checking out *Kisma*!

<a href="http://www.jetbrains.com/phpstorm/" style="display:block;alt="PHP IDE with advanced HTML/CSS/JavaScript editor for hardcore web-developers" title="PHP IDE with advanced HTML/CSS/JavaScript editor for hardcore web-developers">
<span style="margin: 3px 0 0 65px;padding: 0;float: left;font-size: 12px;cursor:pointer;  background-image:none;border:0;color: #fff; font-family: trebuchet ms,arial,sans-serif;font-weight: normal;text-align:left;">Proudly developed with</span><br/>
![](http://www.jetbrains.com/phpstorm/documentation/phpstorm_banners/phpstorm1/phpstorm468x60_violet.gif)
</a>

# About the name...
Besides being a town in the Rift Valley of Kenya, "kisma" is the [Quechuan](http://en.wikipedia.org/wiki/Quechua_people) word for "womb". Since all living things are birthed
from a womb (sort of) I thought why not applications? So the that's where I came up with the name. Yes, it's whimsical. Big whoop, whaddya gonna do about it?

The base class of many Kisma&trade; classes is called "Seed", as it is from this class that all life (i.e. application functionality) springs. This is a lightweight base object that
provides very limited, but useful functionality (like an event hook interface). No magic methods, no chicanery. Just pure PHP.

Secondly, the "size" of the library is labeled as "fun-sized". Yes, more whimsicality. I've grown weary of the micro-, macro-, nano-, mega- framework arguments of late. So cope.

A library is supposed to help you, the coder, develop whatever it is you're trying to develop in a timely, productive fashion. If you have to jump through a thousand hoops just
to bootstrap the damned utility, it's not easy.  If there are choices for configuration file formats, that's not easy. I'm all for flexibility,
but I'm more in favor of maintainability. I can't have one person on my team writing his config files in YAML, another in PHP, one in XML,
etc. I'm not knocking frameworks that accept/allow this. I'm just saying that I've avoided that for the sake of consistency, maintainability, ease of use, and readability.

# Design Goals

These are the design goals of Kisma&trade;. My original goal was to create a really kick-ass web framework. But I don't have the time nor the inclination to take on that level of
coding. So I scaled it way back to just be a library of cool shit. This is basically all the utility classes and whatnot that I've written over the last decade assembled into a
5.3 namespaced library. You can use as much or as little of it as you want.

While the library is NOT specifically designed for ultra-fast performance (it ain't slow either), execution speed was the primary goal of certain areas (i.e. caching data for
subsequent calls, limited instantiation/invokation within loops, etc.). While the code is, for the most part, stream-lined and fast, I'm sure there are areas where it could be
improved to make it faster. However, I've focused on readability and consistency over speed. Can you use this library on your web site? Absolutely. Will it freak out
 (Symfony|Yii|Cake|Silex|<framework-du-jour>)? It shouldn't. Well, that's cool!

* Fully leverage PHP 5.3, its features such as namespaces, embracing the DRY KISS.
* Use built-in PHP library calls whenever possible for speed.
* Consistent access/interface usage by all objects
* Completely extensible from the base up, minimal cohesion and coupling.
* Usable from/with any other framework or library
* ABSOLUTELY NO USE OF MAGIC __get() AND __set() or public properties.

I will be working on more documentation when I flesh out my model more.

# Features

* Easy to use/grasp/grok/work with
* Quicker to code repetitive tasks
* All setters return $this for easy chaining
* Easy to configure
* PSR-0 compliant
* Registered with Packagist, Composer-compatible!

# Installation

## Composer

Kisma&trade; is PSR-0 compliant and can be installed using [composer](http://getcomposer.org/).  Simply add `kisma/kisma` to your composer.json file.  _Composer is the sane
alternative to PEAR.  It is excellent for managing dependancies in larger projects_.

    {
        "require": {
            "kisma/kisma": "*"
        }
    }

## Install from Source

Because Kisma&trade; is PSR-0 compliant, you can also just clone the repo and use a PSR-0 compatible autoloader to load the library, like [Symfony's](http://symfony.com/doc/current/components/class_loader.html).

## Phar

A [PHP Archive](http://php.net/manual/en/book.phar.php) (or .phar) file is not yet available.

# Requirements
* PHP v5.3+
 Kisma&trade; requires PHP v5.3.0+.
