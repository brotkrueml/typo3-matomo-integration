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

The :php:`\Brotkrueml\MatomoIntegration\Code\JavaScriptCode` object holds a piece
of arbitrary JavaScript code used in the :ref:`beforeTrackPageViewEvent`,
:ref:`afterTrackPageViewEvent` and :ref:`addToDataLayerEvent` events. This
object is necessary to distinguish between a "normal" string and JavaScript code
for later embedding.

Example:

.. code-block:: php

   $javaScriptCode = new \Brotkrueml\MatomoIntegration\Code\JavaScriptCode(
      '/* some JavaScript code */'
   );

The object provides the following method:

.. confval:: __toString(): string
   :name: javascriptcode-tostring

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


.. _modifySiteConfigurationEvent:

ModifySiteConfigurationEvent
----------------------------

.. versionadded:: 2.1.0

This event allows to modify some settings from the site configuration on runtime.

The event provides the following methods:

.. confval:: getRequest(): \Psr\Http\Message\ServerRequestInterface
   :name: modifysiteconfigurationevent-getrequest

   Get the current PSR-7 request object.

.. confval:: getSiteIdentifier(): string
   :name: modifysiteconfigurationevent-getsiteidentifier

   Get the site identifier.

.. confval:: getUrl(): string
   :name: modifysiteconfigurationevent-geturl

   Get the URL.

.. confval:: setUrl(string $url): void
   :name: modifysiteconfigurationevent-seturl

   Set a URL.

.. confval:: getSiteId(): int
   :name: modifysiteconfigurationevent-getsiteid

   Get the site ID.

.. confval:: setSiteId(int $siteId): void
   :name: modifysiteconfigurationevent-setsiteid

   Set a site ID.

.. confval:: getTagManagerContainerIds(): array
   :name: modifysiteconfigurationevent-gettagmanagercontainerids

   Get the list of container IDs for the Matomo Tag Manager.

.. confval:: setTagManagerContainerIds(array $containerIds): void
   :name: modifysiteconfigurationevent-settagmanagercontainerids

   Set a list of container IDs for the Matomo Tag Manager.

Example
~~~~~~~

The example below adjusts the site ID depending on the current language.

.. rst-class:: bignums-xxl

#. Create the event listener

   .. code-block:: php
      :caption: EXT:your_extension/Classes/EventListener/ModifyMatomoSiteId.php

      <?php
      declare(strict_types=1);

      namespace YourVender\YourExtension\EventListener;

      use Brotkrueml\MatomoIntegration\Event\ModifySiteConfigurationEvent;

      final class ModifyMatomoSiteId
      {
         public function __invoke(ModifySiteConfigurationEvent $event): void
         {
            if ($event->getRequest()->getAttribute('language')->getLanguageId() === 1) {
               // Override the site ID when in another language
               $event->setSiteId(42);
            }
         }
      }

#. Register your event listener

   .. code-block:: yaml
      :caption: EXT:your_extension/Configuration/Services.yaml

      services:
         YourVendor\YourExtension\EventListener\ModifyMatomoSiteId:
            tags:
               - name: event.listener
                 identifier: 'modifyMatomoSiteId'


.. _enrichScriptTagEvent:

EnrichScriptTagEvent
--------------------

With this event you can add attributes to the surrounding :html:`<script>` tag.
For a concrete usage have a look into the
:ref:`use cases <use-case-extend-script-tag>`.

The event provides the following methods:

.. confval:: getRequest(): \Psr\Http\Message\ServerRequestInterface
   :name: enrichscripttagevent-settagmanagercontainerids

   Get the current PSR-7 request object.

.. confval:: setId(string $id): void
   :name: enrichscripttagevent-setid

   Set the id.

.. confval:: setType(string $type): void
   :name: enrichscripttagevent-settype

   Set the type.

.. confval:: addDataAttribute(string $name, string $value = ''): void
   :name: enrichscripttagevent-adddataattribute

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

   .. code-block:: php
      :caption: EXT:your_extension/Classes/EventListener/AddAttributesToMatomoScriptTag.php

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

#. Register your event listener

   .. code-block:: yaml
      :caption: EXT:your_extension/Configuration/Services.yaml

      services:
         YourVendor\YourExtension\EventListener\AddAttributesToMatomoScriptTag:
            tags:
               - name: event.listener
                 identifier: 'addAttributesToMatomoScriptTag'


.. _beforeTrackPageViewEvent:

BeforeTrackPageViewEvent
------------------------

This event can be used to add calls **before** the embedding of the
`trackPageView` code.

This can be helpful when you want to adjust the `document title`_ or to add
`custom dimensions`_.

The event provides the following methods:

.. confval:: getRequest(): \Psr\Http\Message\ServerRequestInterface
   :name: beforetrackpageviewevent-getrequest

   Get the current PSR-7 request object.

.. confval:: addJavaScriptCode(string $code): void
   :name: beforetrackpageviewevent-addjavascriptcode

   Adds a JavaScript code snippet.

.. confval:: addMatomoMethodCall(string $method, ...$parameters): void
   :name: beforetrackpageviewevent-addmatomomethodcall

   Adds a Matomo method call for the given method and optional parameters.
   The value can be of type: :php:`array`, :php:`bool`, :php:`int`,
   :php:`float`, :php:`string` or
   :php:`\Brotkrueml\MatomoIntegration\Code\JavaScriptCode`.

Example
~~~~~~~

The example below results in the following code:

.. code-block:: js

   // ...
   _paq.push(["setDocumentTitle", "Some Document Title"]);
   _paq.push(["trackPageView"]);
   // ...

or (for illustration of the usage of the
:php:`\Brotkrueml\MatomoIntegration\Code\JavaScriptCode` object):

.. code-block:: js

   // ...
   function getDocumentTitle { return "Some Document Title"; }
   _paq.push(["setDocumentTitle", getDocumentTitle()]);
   _paq.push(["trackPageView"]);
   // ...

.. rst-class:: bignums-xxl

#. Create the event listener

   .. code-block:: php
      :caption: EXT:your_extension/Classes/EventListener/SetDocumentTitleExample.php

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

#. Register your event listener

   .. code-block:: yaml
      :caption: EXT:your_extension/Configuration/Services.yaml

      services:
         YourVendor\YourExtension\EventListener\SetDocumentTitleExample:
            tags:
               - name: event.listener
                 identifier: 'setDocumentTitleExample'


.. _trackSiteSearchEvent:

TrackSiteSearchEvent
--------------------

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

.. confval:: getRequest(): \Psr\Http\Message\ServerRequestInterface
   :name: tracksitesearchevent-getrequest

   Get the current PSR-7 request object.

.. confval:: setKeyword(string $keyword): void
   :name: tracksitesearchevent-setkeyword

   Sets the keyword.

.. confval:: setCategory(string|false $category): void
   :name: tracksitesearchevent-setcategory

   Sets an optional category.

.. confval:: setSearchCount(int|false $searchCount): void
   :name: tracksitesearchevent-setsearchcount

   Sets an optional search count.

.. confval:: addCustomDimension(int $id, string $value): void
   :name: tracksitesearchevent-addcustomdimension

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

   .. code-block:: php
      :caption: EXT:your_extension/Classes/EventListener/SomeTrackSiteSearchExample.php

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

#. Register your event listener

   .. code-block:: yaml
      :caption: EXT:your_extension/Configuration/Services.yaml

      services:
         YourVendor\YourExtension\EventListener\SomeTrackSiteSearchExample:
            tags:
               - name: event.listener
                 identifier: 'someTrackSiteSearchExample'


.. _enrichTrackPageViewEvent:

EnrichTrackPageViewEvent
------------------------

This event can be used to enrich the `trackPageView` call with a page title
and/or a `custom dimension only for the page view`_.

The event provides the following methods:

.. confval:: getRequest(): \Psr\Http\Message\ServerRequestInterface
   :name: enrichtrackpageviewevent-getrequest

   Get the current PSR-7 request object.

.. confval:: setPageTitle(string $pageTitle): void
   :name: enrichtrackpageviewevent-setpagetitle

Sets the page title.

.. confval:: addCustomDimension(int $id, string $value): void
   :name: enrichtrackpageviewevent-addcustomdimension

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

   .. code-block:: php
      :caption: EXT:your_extension/Classes/EventListener/SomeEnrichTrackPageViewExample.php

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

#. Register your event listener

   .. code-block:: yaml
      :caption: EXT:your_extension/Configuration/Services.yaml

      services:
         YourVendor\YourExtension\EventListener\SomeEnrichTrackPageViewExample:
            tags:
               - name: event.listener
                 identifier: 'someEnrichTrackPageViewExample'


.. _afterTrackPageViewEvent:

AfterTrackPageViewEvent
-----------------------

This event can be used to add calls **after** the embedding of the
`trackPageView` code.

The event provides the following methods:

.. confval:: getRequest(): \Psr\Http\Message\ServerRequestInterface
   :name: aftertrackpageviewevent-getrequest

   Get the current PSR-7 request object.

.. confval:: addJavaScriptCode(string $code): void
   :name: aftertrackpageviewevent-addjavascriptcode

   Adds a JavaScript code snippet.

.. confval:: addMatomoMethodCall(string $method, ...$parameters): void
   :name: aftertrackpageviewevent-addmatomomethodcall

   Adds a Matomo method call for the given method and optional parameters.
   The value can be of type: :php:`array`, :php:`bool`, :php:`int`,
   :php:`float`, :php:`string` or
   :php:`\Brotkrueml\MatomoIntegration\Code\JavaScriptCode`.

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

   .. code-block:: php
      :caption: EXT:your_extension/Classes/EventListener/EnableHeartBeatTimerWithActiveSecondsExample.php

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

#. Register your event listener

   .. code-block:: yaml
      :caption: EXT:your_extension/Configuration/Services.yaml

      services:
         YourVendor\YourExtension\EventListener\EnableHeartBeatTimerWithActiveSecondsExample:
            tags:
               - name: event.listener
                 identifier: 'enableHeartBeatTimerWithActiveSecondsExample'


.. _addToDataLayerEvent:

AddToDataLayerEvent
-------------------

With this event you can add variables to the Matomo tag manager `data layer`_.

The event provides the following method:

.. confval:: getRequest(): \Psr\Http\Message\ServerRequestInterface
   :name: addtodatalayerevent-getrequest

   Get the current PSR-7 request object.

.. confval:: addVariable(string $name, $value): void
   :name: addtodatalayerevent-addvariable

   Adds a variable with a name and value. The value can be of type:
   :php:`string`, :php:`int`, :php:`float` or
   :php:`\Brotkrueml\MatomoIntegration\Code\JavaScriptCode`.


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

   .. code-block:: php
      :caption: EXT:your_extension/Classes/EventListener/AddOrderDetailsToDataLayerExample.php

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

#. Register your event listener

   .. code-block:: yaml
      :caption: EXT:your_extension/Configuration/Services.yaml

      services:
         YourVendor\YourExtension\EventListener\AddOrderDetailsToDataLayerExample:
            tags:
               - name: event.listener
                 identifier: 'addOrderDetailsToDataLayerExample'


.. _custom dimensions: https://developer.matomo.org/guides/tracking-javascript-guide#custom-dimensions
.. _custom dimension only for the page view: https://developer.matomo.org/guides/tracking-javascript-guide#tracking-a-custom-dimension-for-one-specific-action-only
.. _data layer: https://developer.matomo.org/guides/tagmanager/datalayer
.. _document title: https://developer.matomo.org/guides/tracking-javascript-guide#custom-page-title
.. _Site search tracking and reporting: https://matomo.org/docs/site-search/
.. _JavaScript Tracking Client - Internal search tracking: https://developer.matomo.org/guides/tracking-javascript-guide#internal-search-tracking
