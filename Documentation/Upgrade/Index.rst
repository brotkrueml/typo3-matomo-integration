.. include:: /Includes.rst.txt

.. _upgrade:

=======
Upgrade
=======

From version 1.x to version 2.0
===============================

The setting :yaml:`matomoIntegrationTagManagerContainerId` in the
:ref:`site configuration <site-configuration-tag-manager-container-ids>` has
been changed to :yaml:`matomoIntegrationTagManagerContainerIds` to reflect that
it can hold multiple container IDs since version 1.6.0.

Search the YAML files of your site configuration(s) for this string and adjust
it accordingly.
