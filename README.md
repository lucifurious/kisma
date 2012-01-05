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

![](https://github.com/Pogostick/Kisma/raw/master/assets/jdepend.png) 


<pre>
Directories:                                          5
Files:                                               44

Lines of Code (LOC):                               9799
  Cyclomatic Complexity / Lines of Code:           0.08
Comment Lines of Code (CLOC):                      5031
Non-Comment Lines of Code (NCLOC):                 4768

Namespaces:                                           5
Interfaces:                                          40
Classes:                                             79
  Abstract:                                           6 (7.59%)
  Concrete:                                          73 (92.41%)
  Average Class Length (NCLOC):                      69
Methods:                                            313
  Scope:
    Non-Static:                                     215 (68.69%)
    Static:                                          98 (31.31%)
  Visibility:
    Public:                                         274 (87.54%)
    Non-Public:                                      39 (12.46%)
  Average Method Length (NCLOC):                     17
  Cyclomatic Complexity / Number of Methods:       2.23

Anonymous Functions:                                 28
Functions:                                            0

Constants:                                          249
  Global constants:                                   0
  Class constants:                                  249

Tests:
  Classes:                                            1
  Methods:                                            0
</pre>

![](https://github.com/Pogostick/Kisma/raw/master/assets/pyramid.png) 

Namespace Diagram
=================

![](https://github.com/Pogostick/Kisma/raw/master/assets/Kisma.png) 