Kisma
===============================
Thanks for checking out *Kisma*!

Design Goals
============

These are the design goals of Kisma. My main design goal is consistency. Too many frameworks use inconsistent naming conventions, property access, and/or usage.

This framework is NOT designed for ultra-fast performance. While the code is, for the most part, stream-lined and fast, I'm sure there are areas where it could be improved to make it faster. However, I've focused on readability and consistency over speed. Can you use this framework for your web site? Absolutely. Will it handle thousands of requests per second? No clue.

* Small footprint
* Consistent interface to all objects
* Completely extensible
* Distribute in a Phar archive

Features
========

* Quicker to code repetitive tasks
* All setters return $this for easy chaining
* Streamlined the entire autoloader sequence to lessen burden on configuration

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

PHPLOC Metrics
==============

 * Cyclomatic Complexity Score means:
  * < 4 = Low Complexity
  * 5 - 7 = Medium Complexity
  * 6 - 10 = Highly Complex
  * > 10 = Very Complex

<pre>
Directories:                                          4
Files:                                               24

Lines of Code (LOC):                               5420
  Cyclomatic Complexity / Lines of Code:           0.07
Comment Lines of Code (CLOC):                      3266
Non-Comment Lines of Code (NCLOC):                 2154

Namespaces:                                           5
Interfaces:                                          30
Classes:                                             52
  Abstract:                                           6 (11.54%)
  Concrete:                                          46 (88.46%)
  Average Class Length (NCLOC):                      50
Methods:                                            213
  Scope:
    Non-Static:                                     172 (80.75%)
    Static:                                          41 (19.25%)
  Visibility:
    Public:                                         182 (85.45%)
    Non-Public:                                      31 (14.55%)
  Average Method Length (NCLOC):                     12
  Cyclomatic Complexity / Number of Methods:       1.66

Anonymous Functions:                                  1
Functions:                                            1

Constants:                                          127
  Global constants:                                   0
  Class constants:                                  127
</pre>