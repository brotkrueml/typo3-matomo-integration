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

To configure the extension, go to :guilabel:`Site Management > Sites`
and select the appropriate site configuration. Click on the :guilabel:`Matomo
Integration` tab:

.. figure:: /Images/site-configuration.png
   :alt: Options in the site configuration

   Options in the site configuration

.. attention::
   After you have adjusted the settings in the site configuration,
   you must flush the cache.

.. note::
   The Matomo integration is only active, if a URL and a site ID are specified.

Installation
------------

URL
   Enter the URL of your Matomo instance (without `matomo.php` at the end).

   .. versionadded:: 2.3
      Relative URLs can be used, like :sample:`//matomo.example.com/`.

Site ID
   Enter the site ID for the website.

Options
-------

.. note::
   The initial status of the options corresponds to the default JavaScript
   tracking code specified via the menu :guilabel:`Administration > Measurables`
   in Matomo.

Track users with JavaScript disabled
   If this option is enabled, users with JavaScript disabled will be tracked.
   Technically, a :html:`<noscript>` tag is embedded in the web page.

   Default: *disabled*

Require consent
   |shield-check| Activating this option has a **positive** impact on privacy.

   Enable this option when a consent manager is used which sets up consent
   tracking: no tracking request will be sent to Matomo and no cookies will be
   set. For more information have a look into
   `Implementing tracking or cookie consent with the Matomo JavaScript Tracking Client`_.

   Default: *disabled*

Require cookie consent
   |shield-check| Activating this option has a **positive** impact on privacy.

   Enable this option when a consent manager is used which sets up consent
   tracking: tracking requests will still be sent but no cookies will be set.
   For more information have a look into
   `Implementing tracking or cookie consent with the Matomo JavaScript Tracking Client`_.

   Default: *disabled*

   .. important::
      When enabling this option *and* the option "Require consent", "Require
      cookie consent" will be ignored in favour of the more restrictive one.

Cookie tracking
   |shield-alert| Activating this option has a **negative** impact on privacy.

   Activating this option `enables cookies tracking`_.

   Default: *enabled*

File tracking
   |shield-alert| Activating this option has a **negative** impact on privacy.

   This option has been introduced with Matomo 5 and enables the
   `file:// protocol tracking`_.

   Default: *disabled*

   .. attention::
      Enabling this option may track personal data: Someone downloads a page
      from your website and stores it locally. Then the user opens it and the
      user will be tracked. The URL might look like
      :samp:`file:///C:/Users/myname/AppData/Local/...`

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

Disable browser feature detection
   |shield-check| Activating this option has a **positive** impact on privacy.

   This option `disables the browser feature detection`_.

   Default: *disabled* (browser features are detected)

Disable campaign parameters
   .. versionadded:: 2.2

   |shield-check| Activating this option has a **positive** impact on privacy.

   By default, Matomo will send campaign parameters (mtm, utm, etc.) to the
   tracker and record that information. Some privacy regulations may not allow
   for this information to be collected. If this applies to you, activate this
   method to prevent campaign parameters from being sent to the tracker.

   Default: *disabled* (campaign parameters are sent)

Respect "Do not track" browser setting
   This option prevents requests and cookies when people
   `don't want to be tracked`_.

   Default: *disabled*

Track all content impressions
   If you use `content tracking`_ you can enable this option when you want to
   track **all** content impressions on a page.

   Default: *disabled*

Track visible content impressions
   If you use `content tracking`_ you can enable this option when you want to track
   **visible** content impressions on a page.

   Default: *disabled*

.. _site-configuration-track-error-pages:

Track error pages
   If you have configured the :ref:`error handling
   <t3coreapi:sitehandling-errorHandling>` and enable this option, the document
   title will be set as described in the Matomo FAQ:
   `How to track error pages`_. You can customise the document title in the
   field :ref:`"Document title template for tracking of error pages"
   <site-configuration-error-pages-template>`

   Default: *disabled*

   .. note::
      If you have configured multiple error status codes with the same page ID,
      the first defined error code will be used.

Track JavaScript errors
   With this option enabled `JavaScript errors`_ are tracked.

   Default: *disabled*

   .. note::
      Only JavaScript errors that occur after the asynchronous loading and
      execution of the Matomo script will be tracked.

.. _site-configuration-error-pages-template:

Document title template for tracking of error pages
   Adapt the template for the document title to your needs with the option
   :ref:`"Track error pages" <site-configuration-track-error-pages>`
   enabled. You can use the following variables:

   :code:`{statusCode}`
      The status code of the error (for example, 404, 500).

   :code:`{path}`
      The path of the URL where the error occurred.

   :code:`{referrer}`
      The referrer.

   Default: *{statusCode}/URL = {path}/From = {referrer}*

Tag Manager
-----------

.. _site-configuration-tag-manager-container-ids:

Container IDs
   If you use the `Matomo Tag Manager`_, enter the container IDs in this field.
   If no value is given, the tag manager code will not be embedded in the web
   page.

   Default: *empty string*

   Examples for possible values:

   - Live: `l2UO6eVk`
   - Dev: `l2UO6eVk_dev_600e4be39a8bfd98bf71f295`
   - Staging: `l2UO6eVk_staging_6584c7e4727fcb9180530d6d`

   Multiple container IDs can be configured, separated by a comma, for example:
   `l2UO6eVk,gu8CuJ6Q`.

.. hint::
   It is possible to define different container IDs for different environments
   (such as Live, Dev, Staging) using :ref:`environment variables
   <t3coreapi:sitehandling-using-env-vars>`.

Debug Mode
   When `debug mode`_ is enabled, various debug messages are logged on the
   developer console.

   Default: *disabled*


.. _accurately measure the time spent: https://matomo.org/faq/how-to/faq_21824/
.. _content tracking: https://matomo.org/docs/content-tracking/
.. _debug mode: https://developer.matomo.org/guides/tagmanager/debugging#developer-console-log-messages
.. _disables the browser feature detection: https://matomo.org/faq/how-do-i-disable-browser-feature-detection-completely/
.. _don't want to be tracked: https://matomo.org/docs/privacy-how-to/#step-4-respect-donottrack-preference#step-4-respect-donottrack-preference
.. _download and outlink tracking: https://matomo.org/faq/new-to-piwik/faq_71/
.. _enables cookies tracking: https://matomo.org/faq/general/faq_157/
.. _feature request: https://github.com/brotkrueml/typo3-matomo-integration/issues/new
.. _file:// protocol tracking: https://matomo.org/faq/how-to/enable-file-protocol-tracking/
.. _How to track error pages: https://matomo.org/faq/how-to/faq_60/
.. _Implementing tracking or cookie consent with the Matomo JavaScript Tracking Client: https://developer.matomo.org/guides/tracking-consent
.. _JavaScript errors: https://matomo.org/faq/how-to/how-do-i-enable-basic-javascript-error-tracking-and-reporting-in-matomo-browser-console-error-messages/
.. _Matomo Tag Manager: https://matomo.org/docs/tag-manager/
.. _tracking of performance data: https://matomo.org/faq/how-to/how-do-i-see-page-performance-reports/

.. |shield-alert| image:: /Images/shield-alert.svg
   :width: 24

.. |shield-check| image:: /Images/shield-check.svg
   :width: 24
