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

<pre>
Directories:									6
Files:											22

Lines of Code (LOC):							4734
	Cyclomatic Complexity / Lines of Code:		<b>0.07</b>
Comment Lines of Code (CLOC):					2921
Non-Comment Lines of Code (NCLOC):				1813

Namespaces:										7
Interfaces:										33
Classes:										44
	Abstract:									4 (9.09%)
	Concrete:									40 (90.91%)
	Average Class Length (NCLOC):				53
Methods:										188
	Scope:
		Non-Static:								155 (82.45%)
		Static:									33 (17.55%)
	Visibility:
		Public:									160 (85.11%)
		Non-Public:								28 (14.89%)
	Average Method Length (NCLOC):				12
	Cyclomatic Complexity / Number of Methods:	<b>1.67</b>

Anonymous Functions:							1
Functions:										<b>0</b>

Constants:										121
	Global constants:							0
	Class constants:							121
</pre>