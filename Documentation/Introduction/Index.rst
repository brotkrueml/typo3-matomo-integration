.. include:: /Includes.rst.txt

.. _introduction:

============
Introduction
============

.. contents:: Table of Contents
   :depth: 2
   :local:


.. _what-it-does:

What does it do?
================

The extension integrates `Matomo Analytics`_ easily into TYPO3. The extension
supports features of Matomo 4, so it is recommended to use a current Matomo
version.

The extension takes the
:ref:`Content Security Policy (CSP) <t3coreapi:content-security-policy>` into
account: a nonce attribute is added to the script tag, if the feature is enabled
for frontend. CSP was introduced with
TYPO3 v12.

.. tip::
   If you use Matomo, the :ref:`Matomo Widgets <ext_matomo_widgets:introduction>`
   and :ref:`Matomo Opt-Out <ext_matomo_optout:introduction>` extensions might be of
   interest to you.


When to use this extension
==========================

This extension is useful if you want to add further Matomo method calls
dependent on certain conditions — such as custom dimensions or setting the user
id. Another option is to enable the `Matomo tag manager`_ and add data layer
variables. :ref:`psr14-events` are available for these purposes. Also have a
look at the :ref:`use cases <use-cases>`.


When not to use this extension
==============================

If you only use Matomo's default tracking code or have only static values
for additional Matomo method calls, insert the JavaScript snippet directly
via TypoScript or the Fluid template.


Limitation
==========

This extension can only embed one tracking code for one Matomo instance. If you
have to add multiple tracking codes for one or more Matomo instances you cannot
use this extension and have to do it on your own.


.. _screenshots:

Screenshots
===========

.. figure:: /Images/SiteConfiguration.png
   :alt: Integration of Matomo in the site configuration

   Integration of Matomo in the site configuration


.. _release-management:

Release management
==================
This extension uses `semantic versioning`_ which basically means for you, that

* Bugfix updates (e.g. 1.0.0 => 1.0.1) just includes small bug fixes or security
  relevant stuff without breaking changes.
* Minor updates (e.g. 1.0.0 => 1.1.0) includes new features and smaller tasks
  without breaking changes.
* Major updates (e.g. 1.0.0 => 2.0.0) breaking changes which can be
  refactorings, features or bug fixes.

The changes between the different versions can be found in the
:ref:`changelog <changelog>`.


.. _Matomo Analytics: https://www.matomo.org/
.. _Matomo tag manager: https://matomo.org/docs/tag-manager/
.. _semantic versioning: https://semver.org/
