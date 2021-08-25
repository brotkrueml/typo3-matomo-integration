.. include:: /Includes.rst.txt

.. _introduction:

============
Introduction
============

.. _what-it-does:

What does it do?
================

The extension integrates `Matomo Analytics <https://www.matomo.org/>`_ easily
into TYPO3. The extension supports features from Matomo 4, so you are advised
to use a recent Matomo version.

.. tip::
   If you use Matomo, the :ref:`Matomo Widgets <matomo_widgets:introduction>`
   and :ref:`Matomo Opt-Out <matomo_optout:introduction>` extensions might be of
   interest to you.


When to use this extension
==========================

This extension is useful if you want to add further Matomo method calls
dependent on some conditions - like custom dimensions or setting the user id.
Another possibility is to activate the Matomo tag manager and add data layer
variables. For this purpose :ref:`psr14-events` are available. Also have a look
at the :ref:`use-cases`.


When not to use this extension
==============================

If you only use the default tracking code given by Matomo or you have only
static values to additional Matomo method calls, add the JavaScript snippet
directly via TypoScript or Fluid template.


.. _screenshots:

Screenshots
===========

.. figure:: /Images/SiteConfiguration.png
   :alt: Configuration in the site configuration

   Configuration in the site configuration
