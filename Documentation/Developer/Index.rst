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

Some objects are available for usage in the :ref:`psr14-events`:


.. _object-CustomDimension:

CustomDimension
---------------

The :php:`Brotkrueml\MatomoIntegration\Entity\CustomDimension` object holds
the information for a custom dimension used in the
:ref:`enrichTrackPageViewEvent` event. It is an immutable value object.

Example::

   $customDimension = new Brotkrueml\MatomoIntegration\Entity\CustomDimension(
      3, // The ID of the custom dimension
      'some value' // The value for the custom dimension
   );

The value object provides the following methods:

.. option:: getId(): int

Returns the id of the custom dimension.

.. option:: getValue(): string

Returns the value of the custom dimension.


.. _object-JavaScriptCode:

JavaScriptCode
--------------

The :php:`Brotkrueml\MatomoIntegration\Code\JavaScriptCode` object holds
a piece of arbitrary JavaScript code used in the :ref:`beforeTrackPageViewEvent`
and :ref:`afterTrackPageViewEvent` events.

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

To enrich Matomo's JavaScript tracking code with additional calls
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

.. rst-class:: bignums-xxl

#. Create the event listener

   ::

      <?php
      declare(strict_types=1);

      namespace YourVender\YourExtension\EventListener;

      use Brotkrueml\MatomoIntegration\Event\BeforeTrackPageViewEvent;

      final class SetDocumentTitleExample
      {
         public function __invoke(RegisterAdditionalTypePropertiesEvent $event): void
         {
            // Set the document title
            $event->addMatomoMethodCall('setDocumentTitle', 'Some Document Title');

            // OR:
            // Add some JavaScript code
            $event->addJavaScriptCode('function getDocumentTitle { return "Some Document Title"; }');
            // Set the document title
            $event->addMatomoMethodCall('setDocumentTitle', new JavaScriptCode('getDocumentTitle();');]);
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

.. option:: getPageTitle(): string

Returns a previously set page title (or empty string if not set).

.. option:: addCustomDimension(\\Brotkrueml\\MatomoIntegration\\Entity\\CustomDimension $customDimension): void

Adds a custom dimension as a value object
(see :ref:`object-CustomDimension`).

.. option:: getCustomDimensions(): array

Returns all defined custom dimensions.

Example
~~~~~~~

.. rst-class:: bignums-xxl

#. Create the event listener

   ::

      <?php
      declare(strict_types=1);

      namespace YourVender\YourExtension\EventListener;

      use Brotkrueml\MatomoIntegration\Entity\CustomDimension;
      use Brotkrueml\MatomoIntegration\Event\EnrichTrackPageViewEvent;

      final class SomeEnrichTrackPageViewExample
      {
         public function __invoke(RegisterAdditionalTypePropertiesEvent $event): void
         {
            // You can set another page title
            $event->setPageTitle('Some Page Title');
            // And/or you can set a custom dimension only for the track page view call
            $event->addCustomDimension(new CustomDimension(3, 'Some Custom Dimension Value'));
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

.. rst-class:: bignums-xxl

#. Create the event listener

   ::

      <?php
      declare(strict_types=1);

      namespace YourVender\YourExtension\EventListener;

      use Brotkrueml\MatomoIntegration\Event\BeforeTrackPageViewEvent;

      final class TrackGoalExample
      {
         public function __invoke(RegisterAdditionalTypePropertiesEvent $event): void
         {
            $event->addMatomoMethodCall('TrackGoal', 1);
         }
      }

#. Register your event listener in :file:`Configuration/Services.yaml`

   .. code-block:: yaml

      services:
         YourVendor\YourExtension\EventListener\TrackGoalExample:
            tags:
               - name: event.listener
                 identifier: 'trackGoalExample'
                 event: Brotkrueml\MatomoIntegration\Event\BeforeTrackPageViewEvent


.. _custom dimensions: https://developer.matomo.org/guides/tracking-javascript-guide#custom-dimensions
.. _custom dimension only for the page view: https://developer.matomo.org/guides/tracking-javascript-guide#tracking-a-custom-dimension-for-one-specific-action-only
.. _document title: https://developer.matomo.org/guides/tracking-javascript-guide#custom-page-title
