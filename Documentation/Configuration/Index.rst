.. include:: /Includes.rst.txt

.. index:: Configuration

.. _configuration:

=============
Configuration
=============

Target group: **Developers, Integrators**


.. _site-configuration:

Site configuration
==================

To configure the extension, go to :guilabel:`Site Management` > :guilabel:`Sites`
and select the appropriate site configuration. Click on the :guilabel:`Matomo
Integration` tab:

.. figure:: /Images/SiteConfiguration.png
   :alt: Options in the site configuration

   Options in the site configuration

.. attention::
   After adjustings the settings in the site configuration you have to flush the
   cache.

.. note::
   The Matomo integration is only active if a URL and a site id is given.

URL
   Enter the URL of your Matomo instance (without `matomo.php` at the end).

Site ID
   Enter the site ID for the website.

Track users with JavaScript disabled
   If this option is enabled, users with JavaScript disabled are tracked.
   Technically, a :html:`<noscript>` tag is embedded into the web page.

   Default: *disabled*

Link tracking
   This option enables the `download and outlink tracking`_.

   Default: *enabled*

Performance tracking
   This option enables the `tracking of performance data`_.

   Default: *enabled*

Heart beat timer
   Enable this option when you want to `accurately measure the time spent`_ in
   the visit.

   Default: *disabled*

Track all content impressions
   If you use `content tracking`_ you can enable this option when you want to
   track **all** content impressions on a page.

   Default: *disabled*

Track visible content impressions
   If you use `content tracking`_ you can enable this option when you want to track
   **visible** content impressions on a page.

   Default: *disabled*

Heart beat timer: Active time in seconds
   If the option "Heart beat timer" is enabled, you can adjust how long a tab
   needs to be active to be counted as viewed (in seconds).

   Default: *15*

Tag Manager: Container ID
   If using the `Matomo Tag Manager`_ enter in this field the container ID.
   When no value is given the tag manager code is not embedded into the web
   page.

   Default: *empty string*


.. note::
   If you miss an option please file a `feature request`_. For more complex
   actions you can use :ref:`psr14-events` to add the necessary calls to the
   JavaScript tracking code.

.. _accurately measure the time spent: https://matomo.org/faq/how-to/faq_21824/
.. _content tracking: https://matomo.org/docs/content-tracking/
.. _download and outlink tracking: https://matomo.org/faq/new-to-piwik/faq_71/
.. _feature request: https://github.com/brotkrueml/typo3-matomo-integration/issues/new
.. _Matomo Tag Manager: https://matomo.org/docs/tag-manager/
.. _tracking of performance data: https://matomo.org/faq/how-to/how-do-i-see-page-performance-reports/
