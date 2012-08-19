![Kisma](https://github.com/lucifurious/kisma/raw/master/assets/kisma-logo-64x64.png)

NOTE: I'm in the process of restructuring the library. Please wait until I release a beta before you try to use it.

Kisma! A Fun-Sized PHP Framework
==================================
Thanks for checking out *Kisma*!

<a href="http://www.jetbrains.com/phpstorm/" style="display:block;alt="PHP IDE with advanced HTML/CSS/JavaScript editor for hardcore web-developers" title="PHP IDE with advanced HTML/CSS/JavaScript editor for hardcore web-developers">
<span style="margin: 3px 0 0 65px;padding: 0;float: left;font-size: 12px;cursor:pointer;  background-image:none;border:0;color: #fff; font-family: trebuchet ms,arial,sans-serif;font-weight: normal;text-align:left;">Developed with</span><br/>
![](http://www.jetbrains.com/phpstorm/documentation/phpstorm_banners/phpstorm1/phpstorm468x60_violet.gif)
</a>

About the name...
=================
Besides being a town in the Rift Valley of Kenya, "kisma" is the Quechuan word for "womb". Since all living things are birthed from a womb (sort of) I thought why not applications? So the that's where I came up with the name. Yes, it's whimsical. Big whoop, whaddya gonna do about it?

The core base class of many Kisma library classes is called "Seed", as it is from this class that all life (i.e. application functionality) springs.

Secondly, the "size" of the library is labeled as "fun-sized". Yes, more whimsicality. I've grown weary of the micro-, macro-, nano-, mega- framework arguments of late. So cope.

A framework is supposed to help you, the coder, develop whatever it is you're developing in a timely, productive fashion. If you have to jump through a thousand hoops just to bootstrap the damned framework, it's not easy.  If there are choices for configuration file formats, that's not easy. I'm all for flexibility, but I'm more in favor of maintainability. I can't have one person on my team writing his config files in YAML, another in PHP, one in XML, etc. I'm not knocking frameworks that accept this. I'm just saying that I've avoided that for the sake of consistency and readability.

Design Goals
============

These are the design goals of Kisma. My main design goal is consistency. Too many frameworks use inconsistent naming conventions, property access, and/or usage. These are subject to change at my will of course. I've actually changed them a few times. Honestly, I'm not sure what the goal is any more.

This framework is NOT designed for ultra-fast performance (but it isn't slow either). While the code is, for the most part, stream-lined and fast, I'm sure there are areas where it could be improved to make it faster. However, I've focused on readability and consistency over speed. Can you use this framework for your web site? Absolutely. Will it handle thousands of requests per second? No clue.

* Fully leverage PHP 5.3, its features such as namespaces, embracing the DRY KISS.
* Use built-in PHP library calls whenever possible for speed.
* Consistent access/interface usage by all objects
* Completely extensible from the base up, minimal cohesion and coupling.
* Usable from/with any other framework or library
* ABSOLUTELY NO USE OF MAGIC __get() AND __set()

I will be working on more documentation when I flesh out my model more.

Features
========

* Easy to use/grasp/grok/work with framework
* Quicker to code repetitive tasks
* All setters return $this for easy chaining
* Easy to configure
* PSR-0 compliant
* Registered with Packagist, Composer-compatible!

Notes
=====
[todo]

Installation
============
[todo]

Requirements
============
* PHP v5.3+
 Kisma requires PHP v5.3.0+.
