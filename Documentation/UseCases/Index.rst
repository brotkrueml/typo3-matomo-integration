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
            $event->addCustomDimension($this->customDimensionId, $pageType);
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


Colour scheme as custom dimension
=================================

When providing your page with a light and dark colour scheme it might be
interesting how many visitors prefer which colour scheme. This can be
analysed in Matomo with a visit `custom dimension`_.

In contrast to the above example, where the custom dimension should only be
used for tracking of a page view, this custom dimension can be defined
"globally", so we can use the :ref:`beforeTrackPageViewEvent` event.

.. rst-class:: bignums-xxl

#. The event listener

   The event provides an :php:`addCode()` method. With this method you can
   inject arbitrary JavaScript code, so be careful, what you are doing. In
   this case we check the :js:`window.matchMedia()` method to get the
   currently used colour scheme.

   As with in the above example the ID of the custom dimension is injected
   via dependency injection.

   ::

      <?php
      declare(strict_types=1);

      namespace YourVendor\YourExtension\EventListener;

      use Brotkrueml\MatomoIntegration\Code\JavaScriptCode;
      use Brotkrueml\MatomoIntegration\Event\BeforeTrackPageViewEvent;

      final class AddColourSchemeToMatomoTracking
      {
         private int $customDimensionId;

         public function __construct(int $customDimensionId)
         {
            $this->customDimensionId = $customDimensionId;
         }

         public function __invoke(BeforeTrackPageViewEvent $event): void
         {
            $event->addMatomoMethodCall(
               'setCustomDimension',
               $this->customDimensionId,
               new JavaScriptCode('window.matchMedia&&window.matchMedia("(prefers-color-scheme:dark)").matches?"dark":"light"')
            );
         }
      }

#. Registration of the event listener in :file:`Configuration/Services.yaml`

   .. code-block:: yaml

      YourVendor\YourExtension\EventListener\AddColourSchemeToMatomoTracking:
         arguments:
            $customDimensionId: 1
         tags:
            - name: event.listener
              identifier: 'your-ext/addColourSchemeToMatomoTracking'
              event: Brotkrueml\MatomoIntegration\Event\BeforeTrackPageViewEvent


Remove person-identifiable parts from URL
=========================================

In a project we have person-identifiable parts in the URL, namely tokens. They
are used after sending a form for requesting access to downloads or videos.
Those tokens are unique and can be used to identify a user via other sources
and to retrieve her Matomo visit information.

To be compliant with the GDPR and to simplify the analysing of URLs (the token
is not needed here) from the logged URL the token is stripped off. This can be
achieved with `setting a custom URL`_ and the :ref:`beforeTrackPageViewEvent`
event of this extension.

.. rst-class:: bignums-xxl

#. The event listener

   A possible URL looks like `https://example.com/downloads/detail/some-download/hz6dFgz9/`
   where `hz6dFgz9` is the token we want to be removed from the URL.

   The TYPO3 request object provides the information of the current URL,
   so the last part of the URL have to be stripped off and set for Matomo's
   `setCustomUrl` method.

   ::

      <?php
      declare(strict_types=1);

      namespace YourVendor\YourExtension\EventListener;

      use Brotkrueml\MatomoIntegration\Event\BeforeTrackPageViewEvent;
      use Psr\Http\Message\ServerRequestInterface;

      final class RemoveTokenFromUrlForMatomoTracking
      {
         public function __invoke(BeforeTrackPageViewEvent $event)
         {
            if (!isset($queryParams['tx_myext']['token']) {
                  return;
            }

            $uri = $this->getRequest()->getUri();
            $pathParts = explode('/', $uri->getPath());
            // The path ends with a slash, which we want to preserve, so we
            // need to remove the second last part (which is the token)
            unset($pathParts[count($pathParts) - 2]);
            $tokenRemovedUri = $uri->withPath(implode('/', $pathParts));

            $event->addMatomoMethodCall('setCustomUrl', (string)$tokenRemovedUri);
         }

         private function getRequest(): ServerRequestInterface
         {
            return $GLOBALS['TYPO3_REQUEST'];
         }
      }

#. Registration of the event listener in :file:`Configuration/Services.yaml`

   .. code-block:: yaml

      YourVendor\YourExtension\EventListener\RemoveTokenFromUrlForMatomoTracking:
         tags:
            - name: event.listener
              identifier: 'your-ext/removeTokenFromUrlForMatomoTracking'
              event: Brotkrueml\MatomoIntegration\Event\BeforeTrackPageViewEvent


.. _custom dimension: https://matomo.org/docs/custom-dimensions/
.. _setting a custom URL: https://matomo.org/faq/how-to/how-do-i-set-a-custom-url-using-the-matomo-javascript-tracker/
