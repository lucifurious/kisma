Kisma
===============================
Thanks for checking out *Kisma*!

Design Goals
============

These are the design goals of Kisma. My main design goal is consistency. Too many frameworks use inconsistent naming conventions, property access, and/or usage.

This framework is NOT designed for ultra-fast performance. While the code is, for the most part, stream-lined and fast, I'm sure there are areas where it could be improved to make it faster. However, I've focused on readability and consistency over speed. Can you use this framework for your web site? Absolutely. Will it handle thousands of requests per second? No clue.

* Fully leverage PHP 5.3, its features such as namespaces. In addition prepare for 5.4 and beyond with fresh code, embracing the DRY KISS.
* Consistent interface to all objects
* Completely extensible from the base up, minimal cohesion and coupling
* Usable from any other framework
* Use built-in PHP library calls whenever possible for speed.

Features
========

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

![](http://github.com/pogostick/kisma/raw/master/jdepend.png) 

<pre>
Directories:                                         14
Files:                                               39

Lines of Code (LOC):                               8394
  Cyclomatic Complexity / Lines of Code:           0.07
Comment Lines of Code (CLOC):                      4649
Non-Comment Lines of Code (NCLOC):                 3745

Namespaces:                                          11
Interfaces:                                          37
Classes:                                             72
  Abstract:                                           8 (11.11%)
  Concrete:                                          64 (88.89%)
  Average Class Length (NCLOC):                      60
Methods:                                            343
  Scope:
    Non-Static:                                     266 (77.55%)
    Static:                                          77 (22.45%)
  Visibility:
    Public:                                         285 (83.09%)
    Non-Public:                                      58 (16.91%)
  Average Method Length (NCLOC):                     12
  Cyclomatic Complexity / Number of Methods:       1.72

Anonymous Functions:                                  1
Functions:                                            0

Constants:                                          203
  Global constants:                                   1
  Class constants:                                  202
</pre>

![](http://github.com/pogostick/kisma/raw/master/pyramid.png) 

Namespace Diagram
=================

![](http://github.com/pogostick/kisma/raw/master/Kisma.png) 