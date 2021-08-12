.. include:: /Includes.rst.txt

.. _developer:

================
Developer corner
================

Target group: **Developers**


.. _psr14-events:

PSR-14 events
=============

To enrich Matomo's JavaScript tracking code with additional calls
`PSR-14 events`_ are available.

.. seealso::
   You can find more information about PSR-14 events in the blog article
   `PSR-14 Events in TYPO3 <https://usetypo3.com/psr-14-events.html>`_
   and the official :ref:`TYPO3 documentation <t3coreapi:EventDispatcher>`.


BeforeTrackPageViewEvent
------------------------

This event can be used to add calls **before** the embedding of the
:js:`_paq.push(['trackPageView']);` code.

This can be helpful when you want to adjust the `document title`_ or to add
`custom dimensions`_.

TODO: Add code examples


EnrichTrackPageViewEvent
------------------------

This event can be used to enrich the :js:`_paq.push(['trackPageView']);` call
with a page title and/or `custom dimensions`_.

TODO: Add code examples

AfterTrackPageViewEvent
-----------------------

This event can be used to add calls **after** the embedding of the
:js:`_paq.push(['trackPageView']);` code.

TODO: Add code examples


.. _custom dimensions: https://developer.matomo.org/guides/tracking-javascript-guide#custom-dimensions
.. _document title: https://developer.matomo.org/guides/tracking-javascript-guide#custom-page-title

