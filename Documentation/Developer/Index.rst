.. include:: /Includes.rst.txt

.. _developer:

================
Developer corner
================

Target group: **Developers**

.. contents:: Table of Contents
   :depth: 2
   :local:



Objects
=======

A data object is available for use in the :ref:`psr14-events`:

.. _object-JavaScriptCode:

JavaScriptCode
--------------

The :php:`Brotkrueml\MatomoIntegration\Code\JavaScriptCode` object holds a piece
of arbitrary JavaScript code used in the :ref:`beforeTrackPageViewEvent`,
:ref:`afterTrackPageViewEvent` and :ref:`addToDataLayerEvent` events. This
object is necessary to distinguish between a "normal" string and JavaScript code
for later embedding.

Example::

   $javaScriptCode = new Brotkrueml\MatomoIntegration\Code\JavaScriptCode(
      '/* some JavaScript code */'
   );

The object provides the following method:

.. option:: __toString(): string

Returns the JavaScript code.


.. _psr14-events:

PSR-14 events
=============

To enrich Matomo's JavaScript tracking code with additional calls,
PSR-14 events are available.

.. seealso::
   You can find more information about PSR-14 events in the blog article
   `PSR-14 Events in TYPO3 <https://usetypo3.com/psr-14-events.html>`_
   and the official :ref:`TYPO3 documentation <t3coreapi:EventDispatcher>`.


.. _beforeTrackPageViewEvent:

BeforeTrackPageViewEvent
------------------------

This event can be used to add calls **before** the embedding of the
`trackPageView` code.

This can be helpful when you want to adjust the `document title`_ or to add
`custom dimensions`_.

.. include:: AbstractTrackPageViewEventMethods.rst.txt

Example
~~~~~~~

The given example results in the following code:

.. code-block:: js

   // ...
   _paq.push(["setDocumentTitle", "Some Document Title"]);
   _paq.push(["trackPageView"]);
   // ...

or (for illustration of the usage of the
:php:`Brotkrueml\MatomoIntegration\Code\JavaScriptCode` object):

.. code-block:: js

   // ...
   function getDocumentTitle { return "Some Document Title"; }
   _paq.push(["setDocumentTitle", getDocumentTitle()]);
   _paq.push(["trackPageView"]);
   // ...

.. rst-class:: bignums-xxl

#. Create the event listener

   ::

      <?php
      declare(strict_types=1);

      namespace YourVender\YourExtension\EventListener;

      use Brotkrueml\MatomoIntegration\Event\BeforeTrackPageViewEvent;

      final class SetDocumentTitleExample
      {
         public function __invoke(BeforeTrackPageViewEvent $event): void
         {
            // Set the document title
            $event->addMatomoMethodCall('setDocumentTitle', 'Some Document Title');

            // OR:
            // Add some JavaScript code
            $event->addJavaScriptCode('function getDocumentTitle { return "Some Document Title"; }');
            // Set the document title
            $event->addMatomoMethodCall('setDocumentTitle', new JavaScriptCode('getDocumentTitle()');]);
         }
      }

#. Register your event listener in :file:`Configuration/Services.yaml`

   .. code-block:: yaml

      services:
         YourVendor\YourExtension\EventListener\SetDocumentTitleExample:
            tags:
               - name: event.listener
                 identifier: 'setDocumentTitleExample'
                 event: Brotkrueml\MatomoIntegration\Event\BeforeTrackPageViewEvent


.. _enrichTrackPageViewEvent:

EnrichTrackPageViewEvent
------------------------

This event can be used to enrich the `trackPageView` call with a page title
and/or a `custom dimension only for the page view`_.

The event provides the following methods:

.. option:: setPageTitle(string $pageTitle): void

Sets the page title.

.. option:: addCustomDimension(int $id, string $value): void

Adds a custom dimension with the given ID and value.

Example
~~~~~~~

The given example results in the following code:

.. code-block:: js

   // ...
   _paq.push(["trackPageView", "Some Page Title", {"dimension3": "Some Custom Dimension Value"}]);
   // ...

.. rst-class:: bignums-xxl

#. Create the event listener

   ::

      <?php
      declare(strict_types=1);

      namespace YourVender\YourExtension\EventListener;

      use Brotkrueml\MatomoIntegration\Event\EnrichTrackPageViewEvent;

      final class SomeEnrichTrackPageViewExample
      {
         public function __invoke(EnrichTrackPageViewEvent $event): void
         {
            // You can set another page title
            $event->setPageTitle('Some Page Title');
            // And/or you can set a custom dimension only for the track page view call
            $event->addCustomDimension(3, 'Some Custom Dimension Value');
         }
      }

#. Register your event listener in :file:`Configuration/Services.yaml`

   .. code-block:: yaml

      services:
         YourVendor\YourExtension\EventListener\SomeEnrichTrackPageViewExample:
            tags:
               - name: event.listener
                 identifier: 'someEnrichTrackPageViewExample'
                 event: Brotkrueml\MatomoIntegration\Event\EnrichTrackPageViewEvent


.. _afterTrackPageViewEvent:

AfterTrackPageViewEvent
-----------------------

This event can be used to add calls **after** the embedding of the
`trackPageView` code.

.. include:: AbstractTrackPageViewEventMethods.rst.txt

Example
~~~~~~~

The given example results in the following code:

.. code-block:: js

   // ...
   _paq.push(["trackPageView"]);
   _paq.push(["enableHeartBeatTimer", 30]);
   // ...

.. rst-class:: bignums-xxl

#. Create the event listener

   ::

      <?php
      declare(strict_types=1);

      namespace YourVender\YourExtension\EventListener;

      use Brotkrueml\MatomoIntegration\Event\AfterTrackPageViewEvent;

      final class EnableHeartBeatTimerWithActiveSecondsExample
      {
         public function __invoke(AfterTrackPageViewEvent $event): void
         {
            $event->addMatomoMethodCall('enableHeartBeatTimer', 30);
         }
      }

#. Register your event listener in :file:`Configuration/Services.yaml`

   .. code-block:: yaml

      services:
         YourVendor\YourExtension\EventListener\EnableHeartBeatTimerWithActiveSecondsExample:
            tags:
               - name: event.listener
                 identifier: 'enableHeartBeatTimerWithActiveSecondsExample'
                 event: Brotkrueml\MatomoIntegration\Event\BeforeTrackPageViewEvent


.. _addToDataLayerEvent:

AddToDataLayerEvent
-------------------

With this event you can add variables to the Matomo tag manager `data layer`_.

The event provides the following method:

.. option:: addVariable(string $name, $value): void

Adds a variable with a name and value. The value can be of type:
:php:`string`, :php:`int`, :php:`float` or
:php:`Brotkrueml\MatomoIntegration\Code\JavaScriptCode`.


Example
~~~~~~~

The given example results in the following code:

.. code-block:: js

   var _mtm=window._mtm||[];
   _mtm.push({"mtm.startTime": (new Date().getTime()), "event": "mtm.Start", "orderTotal": 4545.45, "orderCurrency": "EUR"});
   // ...

The :js:`mtm.startTime` and :js:`event` variables are added always by default.

.. rst-class:: bignums-xxl

#. Create the event listener

   ::

      <?php
      declare(strict_types=1);

      namespace YourVender\YourExtension\EventListener;

      use Brotkrueml\MatomoIntegration\Event\AddToDataLayerEvent;

      final class AddOrderDetailsToDataLayerExample
      {
          public function __invoke(AddToDataLayerEvent $event): void
          {
              $event->addVariable('orderTotal', 4545.45);
              $event->addVariable('orderCurrency', 'EUR');
          }
      }

#. Register your event listener in :file:`Configuration/Services.yaml`

   .. code-block:: yaml

      services:
         YourVendor\YourExtension\EventListener\AddOrderDetailsToDataLayerExample:
            tags:
               - name: event.listener
                 identifier: 'addOrderDetailsToDataLayerExample'
                 event: Brotkrueml\MatomoIntegration\Event\AddToDataLayerEvent


.. _custom dimensions: https://developer.matomo.org/guides/tracking-javascript-guide#custom-dimensions
.. _custom dimension only for the page view: https://developer.matomo.org/guides/tracking-javascript-guide#tracking-a-custom-dimension-for-one-specific-action-only
.. _data layer: https://developer.matomo.org/guides/tagmanager/datalayer
.. _document title: https://developer.matomo.org/guides/tracking-javascript-guide#custom-page-title
