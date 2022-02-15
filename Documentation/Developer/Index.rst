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
PSR-14 events are available. You can draw inspiration from the :ref:`use-cases`
chapter.

.. seealso::
   You can find more information about PSR-14 events in the blog article
   `PSR-14 Events in TYPO3 <https://usetypo3.com/psr-14-events.html>`_
   and the official :ref:`TYPO3 documentation <t3coreapi:EventDispatcher>`.


.. _enrichScriptTagEvent:

EnrichScriptTagEvent
--------------------

.. versionadded:: 1.3.0

With this event you can add attributes to the surrounding :html:`<script>` tag.
For a concrete usage have a look into the
:ref:`use cases <use-case-extend-script-tag>`.

The event provides the following methods:

.. option:: setId(string $id): void

   Set the id.

.. option:: setType(string $type): void

   Set the type.

.. option:: addDataAttribute(string $name, string $value = ''): void

   Add a data attribute with the :php:`$name` without the `data-` prefix.
   The value is optional, if it is not given or an empty string only the
   name is rendered.


Example
~~~~~~~

The example below results in the following script snippet:

.. code-block:: html

   <script id="some-id" data-foo="bar" data-qux>/* the tracking code */</script>


.. rst-class:: bignums-xxl

#. Create the event listener

   ::

      <?php
      declare(strict_types=1);

      namespace YourVender\YourExtension\EventListener;

      use Brotkrueml\MatomoIntegration\Event\EnrichScriptTagEvent;

      final class AddAttributesToMatomoScriptTag
      {
         public function __invoke(EnrichScriptTagEvent $event): void
         {
            // Set the id
            $event->setId('some-id');

            // Add data attributes
            $event->addDataAttribute('foo', 'bar');
            $event->addDataAttribute('qux');
         }
      }

#. Register your event listener in :file:`Configuration/Services.yaml`

   .. code-block:: yaml

      services:
         YourVendor\YourExtension\EventListener\AddAttributesToMatomoScriptTag:
            tags:
               - name: event.listener
                 identifier: 'addAttributesToMatomoScriptTag'
                 # The event tag can be omitted for TYPO3 v11+
                 event: Brotkrueml\MatomoIntegration\Event\EnrichScriptTagEvent


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

The example below results in the following code:

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
                 # The event tag can be omitted for TYPO3 v11+
                 event: Brotkrueml\MatomoIntegration\Event\BeforeTrackPageViewEvent


.. _trackSiteSearchEvent:

TrackSiteSearchEvent
--------------------

.. versionadded:: 1.3.0

The event is useful for tracking site search metrics, such as the keyword or the
number of results. Especially the number of results can be interesting, since
Matomo displays a list of keywords without results.

Further information can be found on the Matomo website:

- `Site search tracking and reporting`_
- `JavaScript Tracking Client - Internal search tracking`_

.. important::
   If this event is used and the keyword is not empty, the default
   `trackPageView` call is replaced by a `trackSiteSearch` call, as recommended
   by Matomo.

The event provides the following methods:

.. option:: setKeyword(string $keyword): void

Sets the keyword.

.. option:: setCategory(string|false $category): void

Sets an optional category.

.. option:: setSearchCount(int|false $searchCount): void

Sets an optional search count.

.. option:: addCustomDimension(int $id, string $value): void

Adds a custom dimension with the given ID and value.

Example
~~~~~~~

The example below results in the following code:

.. code-block:: js

   // ...
   _paq.push(["trackSiteSearch", "some search keyword", false, 42, {"dimension3": "Some custom dimension value"}]);
   // ...

.. rst-class:: bignums-xxl

#. Create the event listener

   ::

      <?php
      declare(strict_types=1);

      namespace YourVender\YourExtension\EventListener;

      use Brotkrueml\MatomoIntegration\Event\TrackSiteSearchEvent;

      final class SomeTrackSiteSearchExample
      {
         public function __invoke(TrackSiteSearchEvent $event): void
         {
            $event->setKeyword('some search keyword');
            $event->setSearchCount(42);
            $event->addCustomDimension(3, 'some custom dimension value');
         }
      }

#. Register your event listener in :file:`Configuration/Services.yaml`

   .. code-block:: yaml

      services:
         YourVendor\YourExtension\EventListener\SomeTrackSiteSearchExample:
            tags:
               - name: event.listener
                 identifier: 'someTrackSiteSearchExample'
                 # The event tag can be omitted for TYPO3 v11+
                 event: Brotkrueml\MatomoIntegration\Event\TrackSiteSearchEvent


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

The example below results in the following code:

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
                 # The event tag can be omitted for TYPO3 v11+
                 event: Brotkrueml\MatomoIntegration\Event\EnrichTrackPageViewEvent


.. _afterTrackPageViewEvent:

AfterTrackPageViewEvent
-----------------------

This event can be used to add calls **after** the embedding of the
`trackPageView` code.

.. include:: AbstractTrackPageViewEventMethods.rst.txt

Example
~~~~~~~

The example below results in the following code:

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
                 # The event tag can be omitted for TYPO3 v11+
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

The example below results in the following code:

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
                 # The event tag can be omitted for TYPO3 v11+
                 event: Brotkrueml\MatomoIntegration\Event\AddToDataLayerEvent


.. _custom dimensions: https://developer.matomo.org/guides/tracking-javascript-guide#custom-dimensions
.. _custom dimension only for the page view: https://developer.matomo.org/guides/tracking-javascript-guide#tracking-a-custom-dimension-for-one-specific-action-only
.. _data layer: https://developer.matomo.org/guides/tagmanager/datalayer
.. _document title: https://developer.matomo.org/guides/tracking-javascript-guide#custom-page-title
.. _Site search tracking and reporting: https://matomo.org/docs/site-search/
.. _JavaScript Tracking Client - Internal search tracking: https://developer.matomo.org/guides/tracking-javascript-guide#internal-search-tracking
