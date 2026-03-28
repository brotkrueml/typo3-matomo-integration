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

Depending on the URL structure, it is not easy to accumulate page views for one
or more sections on a website. However, you can use an "action"
`custom dimension`_ to measure these sections (such as "Blog", "Jobs",
"Videos").

In this use case, a page type is set for a specific page when a configured
section is available in the root line. The page type should only be available
for the :js:`trackPageView` call, so we implement an event listener based on the
:ref:`enrichTrackPageViewEvent` event.

The given use case results in the following code when the current page id or a
parent page id is `167`:

.. code-block:: js

   // ...
   _paq.push(["trackPageView", "", {"dimension2": "Blog"}]);
   // ...

.. rst-class:: bignums-xxl

#. The event listener

   To separate the configuration from the implementation, the ID of the custom
   dimension and the configuration of the page types are also injected.

   The :php:`$pageTypes` argument is a simple associative array with the page ID
   of the parent page of a section as the key and the value of the custom
   dimension as the value of the array.

   .. literalinclude:: Snippets/_AddPageTypeToMatomoTracking.php
      :caption: EXT:your_extension/Classes/EventListener/AddPageTypeToMatomoTracking.php

#. Passing the arguments to the event listener

   We need to inject the custom dimension ID and the page types configuration
   into the event listener:

   .. code-block:: yaml
      :caption: EXT:your_extension/Configuration/Services.yaml

      YourVender\YourExtension\EventListener\AddPageTypeToMatomoTracking:
         arguments:
            $customDimensionId: 2
            $pageTypes:
               # key: parent page ID, value: value to set for the custom dimension
               167: Blog
               218: Jobs
               112: Videos
               # ... and possibly some more types

Some more ideas how to determine the page type:

-  Set the Matomo page type dependent on the :ref:`TYPO3 page type
   <t3coreapi:page-types>`.
-  Use a separate field in the page properties to select the Matomo page type.


Colour scheme as custom dimension
=================================

If you provide your page with a light and a dark colour scheme, it might be
interesting to see how many visitors prefer which colour scheme. This can be
analysed in Matomo with a "visit" `custom dimension`_.

In contrast to the use case above, where the custom dimension should only be
used for tracking a page view, this custom dimension can be defined "globally"
so we can use the :ref:`beforeTrackPageViewEvent` event.

The given use case results in the following code:

.. code-block:: js

   // ...
   _paq.push(["setCustomDimension", 1, window.matchMedia&&window.matchMedia("(prefers-color-scheme:dark)").matches?"dark":"light"]);
   _paq.push(["trackPageView"]);
   // ...

.. rst-class:: bignums-xxl

#. The event listener

   The event provides an :php:`addMatomoMethodCall()` method. With this method
   you can insert any JavaScript code, so be careful what you do. In this
   example, we use the :js:`window.matchMedia()` function to get the colour
   scheme currently in use

   As in the use case above, the ID of the custom dimension is injected via
   dependency injection.

   .. literalinclude:: Snippets/_AddColourSchemeToMatomoTracking.php
      :caption: EXT:your_extension/Classes/EventListener/AddColourSchemeToMatomoTracking.php

#. Passing the arguments to the event listener

   .. code-block:: yaml
      :caption: EXT:your_extension/Configuration/Services.yaml

      YourVendor\YourExtension\EventListener\AddColourSchemeToMatomoTracking:
         arguments:
            $customDimensionId: 1


Remove person-related parts from URL
====================================

In one project we have person-related parts in the URL, namely tokens. They are
used after submitting a form to request access to downloads or videos. These
tokens are unique and can be used to identify a user through other sources
and retrieve their Matomo visit data.

To be compliant with the GDPR and to simplify the analysis of URLs (the token
is not needed here), the token is stripped from the URL. This can be achieved
by `setting a custom URL`_ and the :ref:`beforeTrackPageViewEvent` event of this
extension.

The given use case results in the following code. The original URL is something
like `https://example.com/downloads/detail/some-download/hz6dFgz9/`, where
`hz6dFgz9` is the token to be removed from the logged URL.

.. code-block:: js

   // ...
   _paq.push(["setCustomUrl", "https://example.com/downloads/detail/some-download/"]);
   _paq.push(["trackPageView"]);
   // ...

The TYPO3 request object returns the information of the current URL, so the
last part of the URL must be removed and set for Matomo's `setCustomUrl`
method call.

.. literalinclude:: Snippets/_RemoveTokenFromUrlForMatomoTracking.php
   :caption: EXT:your_extension/Classes/EventListener/RemoveTokenFromUrlForMatomoTracking.php


.. _use-case-extend-script-tag:

Extending the script tag
========================

Using tracking tools like Matomo within the European Union need special treatments in order
to let the customer consent and agree with the tracking. Although Matomo respects the browser's "Do not track" setting, not everyone is aware of it.

Some GDPR tools like klaro.js require special attribute settings within the script tag in order to work.

Before the script tag is rendered the event `EnrichScriptTagEvent` dispatched from the injector.
This events allow to register an id, a type and add additional data attributes.

.. literalinclude:: Snippets/_PrepareScriptTagForKlaroJs.php
   :caption: EXT:your_extension/Classes/EventListener/PrepareScriptTagForKlaroJs.php


Add site search metrics
=======================

When offering a site search, the main thing you want to know is what users are
searching for. Also worth knowing are, for example, the search terms without
results or the query time for a search phrase. The blog post `Display search
metrics from TYPO3 extension ke_search in Matomo`_ illustrates how to
achieve this within the extension `ke_search`_. The article can also serve as a
blueprint for other search extensions.


.. _custom dimension: https://matomo.org/guide/reporting-tools/custom-dimensions/
.. _Display search metrics from TYPO3 extension ke_search in Matomo: https://brotkrueml.dev/search-metrics-typo3-extension-ke-search-matomo/
.. _EU regulation - Lawfulness of processing: https://www.privacy-regulation.eu/en/article-6-lawfulness-of-processing-GDPR.htm
.. _ke_search: https://extensions.typo3.org/extension/ke_search
.. _setting a custom URL: https://matomo.org/faq/how-to/how-do-i-set-a-custom-url-using-the-matomo-javascript-tracker/
