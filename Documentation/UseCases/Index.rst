.. include:: /Includes.rst.txt

.. _use-cases:

=========
Use cases
=========

Target group: **Developers**

.. contents:: Table of Contents
   :depth: 2
   :local:

Page types as custom dimension
==============================

Dependent on you URL structure it is not easy to accumulate the page views for
one or more sections on a web site. But you can use an action
`custom dimension`_ to measure those sections (like "Blog", "Jobs", "Videos").

In this use case a page type is set for a given page if a configured section
is available in the root line. The page type should only be available for the
`trackPageView` call, so we implement an event listener based on the
:ref:`enrichTrackPageViewEvent` event.


.. rst-class:: bignums-xxl

#. The event listener

   The root line is available via the :php:`TypoScriptFrontendController` class,
   so we inject it via the constructor of the event listener.

   To separate the configuration from the implementation, the ID of the custom
   dimension and the configuration of the page types are also injected.

   The :php:`$pageTypes` property is a simple associative array with the page ID
   of the parent page of a section as key and the value of the custom dimension
   is set as value of the array.

   ::

      <?php
         declare(strict_types=1);

         namespace YourVender\YourExtension\EventListener;

         use Brotkrueml\MatomoIntegration\Entity\CustomDimension;
         use Brotkrueml\MatomoIntegration\Event\EnrichTrackPageViewEvent;
         use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

         final class AddPageTypeToMatomoTracking
         {
            private TypoScriptFrontendController $typoScriptFrontendController;
            private int $customDimensionId;
            private array $pageTypes;

            public function __construct(
               TypoScriptFrontendController $typoScriptFrontendController,
               int $customDimensionId,
               array $pageTypes
            ) {
               $this->typoScriptFrontendController = $typoScriptFrontendController;
               $this->customDimensionId = $customDimensionId;
               $this->pageTypes = $pageTypes;
            }

            public function __invoke(EnrichTrackPageViewEvent $event): void
            {
               $pageIds = array_keys($this->pageTypes);
               $hits = array_filter(
                  $this->typoScriptFrontendController->rootLine,
                  static fn (array $page): bool => in_array($page['uid'], $pageIds)
               );
               if ($hits === []) {
                  return;
               }

               $pageType = $this->pageTypes[current($hits)['uid']];
               $event->addCustomDimension(
                  new CustomDimension($this->customDimensionId, $pageType)
               );
            }
         }

#. Registration of the event listener in :file:`Configuration/Services.yaml`

   We need to inject the custom dimension ID and the page types configuration
   into the event listener:

   .. code-block:: yaml

      YourVender\YourExtension\EventListener\AddPageTypeToMatomoTracking:
         arguments:
            $customDimensionId: 2
            $pageTypes:
               # key: page ID, value: value to set for the custom dimension
               167: Blog
               218: Jobs
               112: Videos
               # ... and possibly some more types
         tags:
            - name: event.listener
              identifier: 'your-ext/addPageTypeToMatomoTracking'
              event: Brotkrueml\MatomoIntegration\Event\EnrichTrackPageViewEvent

Some more ideas how to determine the page type:

- Set the Matomo page type dependent on the :ref:`TYPO3 page type
  <t3coreapi:page-types>`.
- Use a separate field in the page properties to select the Matomo page type.


.. _custom dimension: https://matomo.org/docs/custom-dimensions/
