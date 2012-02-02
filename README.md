Kisma
===============================
Thanks for checking out *Kisma*!

<a href="http://www.jetbrains.com/phpstorm/" style="display:block;alt="PHP IDE with advanced HTML/CSS/JavaScript editor for hardcore web-developers" title="PHP IDE with advanced HTML/CSS/JavaScript editor for hardcore web-developers">
<span style="margin: 3px 0 0 65px;padding: 0;float: left;font-size: 12px;cursor:pointer;  background-image:none;border:0;color: #fff; font-family: trebuchet ms,arial,sans-serif;font-weight: normal;text-align:left;">Developed with</span><br/>
![](http://www.jetbrains.com/phpstorm/documentation/phpstorm_banners/phpstorm1/phpstorm468x60_violet.gif)
</a>

Design Goals
============

These are the design goals of Kisma. My main design goal is consistency. Too many frameworks use inconsistent naming conventions, property access, and/or usage.

This framework is NOT designed for ultra-fast performance (but it isn't slow either). While the code is, for the most part, stream-lined and fast, I'm sure there are areas where it could be improved to make it faster. However, I've focused on readability and consistency over speed. Can you use this framework for your web site? Absolutely. Will it handle thousands of requests per second? No clue.

* Fully leverage PHP 5.3, its features such as namespaces. In addition prepare for 5.4 and beyond with fresh code, embracing the DRY KISS.
* Consistent interface to all objects
* Completely extensible from the base up, minimal cohesion and coupling
* Usable from any other framework
* Use built-in PHP library calls whenever possible for speed.

It is important to mention that I changed the direction of the project in late 2011. After using Silex for a time, I felt that it could be a very interesting core for Kisma as it already did many things that I was doing or going to do. So I shifted the focus of Kisma to be a framework that sits on top of Silex.

Silex does a lot of things really well. However, it makes your code look like shit. I hate that. Being very anal about the way my code presents itself, I couldn't fathom delivering Silex-style code to clients. This led to where Kisma stands today.

I will be working on more documentation soon. 

Features
========

* Provides many useful features that make working with Silex a breeze!
* Quicker to code repetitive tasks
* All setters return $this for easy chaining
* Easy to configure
* PSR-0 compliant

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

Metrics
==============

![](https://github.com/Pogostick/Kisma/raw/master/assets/jdepend.png) 


<pre>
Directories:                                          6
Files:                                               44

Lines of Code (LOC):                               9641
  Cyclomatic Complexity / Lines of Code:           0.08
Comment Lines of Code (CLOC):                      4974
Non-Comment Lines of Code (NCLOC):                 4667

Namespaces:                                           6
Interfaces:                                          41
Classes:                                             80
  Abstract:                                           6 (7.50%)
  Concrete:                                          74 (92.50%)
  Average Class Length (NCLOC):                      66
Methods:                                            295
  Scope:
    Non-Static:                                     204 (69.15%)
    Static:                                          91 (30.85%)
  Visibility:
    Public:                                         255 (86.44%)
    Non-Public:                                      40 (13.56%)
  Average Method Length (NCLOC):                     18
  Cyclomatic Complexity / Number of Methods:       2.27

Anonymous Functions:                                 27
Functions:                                            0

Constants:                                          255
  Global constants:                                   0
  Class constants:                                  255
</pre>

![](https://github.com/Pogostick/Kisma/raw/master/assets/pyramid.png) 
