.. include:: /Includes.rst.txt

.. _introduction:

============
Introduction
============

.. _what-it-does:

What does it do?
================

The extension integrates `Matomo Analytics <https://www.matomo.org/>`_ easily
into TYPO3. The extension supports features of Matomo 4, so it is recommended
to use a current Matomo version.

.. tip::
   If you use Matomo, the :ref:`Matomo Widgets <matomo_widgets:introduction>`
   and :ref:`Matomo Opt-Out <matomo_optout:introduction>` extensions might be of
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


.. _screenshots:

Screenshots
===========

.. figure:: /Images/SiteConfiguration.png
   :alt: Integration of Matomo in the site configuration

   Integration of Matomo in the site configuration


.. _Matomo tag manager: https://matomo.org/docs/tag-manager/
