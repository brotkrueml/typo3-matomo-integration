.. _changelog:

Changelog
=========

All notable changes to this project will be documented in this file.

The format is based on `Keep a Changelog <https://keepachangelog.com/en/1.0.0/>`_\ ,
and this project adheres to `Semantic Versioning <https://semver.org/spec/v2.0.0.html>`_.

`Unreleased <https://github.com/brotkrueml/typo3-matomo-integration/compare/v2.0.0...HEAD>`_
------------------------------------------------------------------------------------------------

Added
^^^^^


* PSR-14 event for modifying site configuration on runtime (#29)

`2.0.0 <https://github.com/brotkrueml/typo3-matomo-integration/compare/v1.8.0...v2.0.0>`_ - 2024-02-09
----------------------------------------------------------------------------------------------------------

Added
^^^^^


* Compatibility with TYPO3 v13

Changed
^^^^^^^


* Site configuration setting ``matomoIntegrationTagManagerContainerId`` to ``matomoIntegrationTagManagerContainerIds`` (#19)

Removed
^^^^^^^


* Compatibility with TYPO3 v10 (#17)
* Compatibility with PHP 7.4 and 8.0 (#18)

`1.8.0 <https://github.com/brotkrueml/typo3-matomo-integration/compare/v1.7.0...v1.8.0>`_ - 2023-11-19
----------------------------------------------------------------------------------------------------------

Added
^^^^^


* Option for file tracking in Matomo 5+ (#25)

`1.7.0 <https://github.com/brotkrueml/typo3-matomo-integration/compare/v1.6.0...v1.7.0>`_ - 2023-07-31
----------------------------------------------------------------------------------------------------------

Added
^^^^^


* Option for requiring consent (#23)

`1.6.0 <https://github.com/brotkrueml/typo3-matomo-integration/compare/v1.5.1...v1.6.0>`_ - 2023-05-21
----------------------------------------------------------------------------------------------------------

Added
^^^^^


* Multiple tag manager container IDs can be used (#14)
* Option for requiring cookie consent (#20)
* CSP nonce attribute to script tag in TYPO3 v12+, if available (#7)

`1.5.1 <https://github.com/brotkrueml/typo3-matomo-integration/compare/v1.5.0...v1.5.1>`_ - 2023-04-01
----------------------------------------------------------------------------------------------------------

Fixed
^^^^^


* Error when opening a site configuration in TYPO3 v12.3 (#15)

`1.5.0 <https://github.com/brotkrueml/typo3-matomo-integration/compare/v1.4.0...v1.5.0>`_ - 2022-11-06
----------------------------------------------------------------------------------------------------------

Added
^^^^^


* PSR-7 request object is available in PSR-14 events via getRequest() method (#11)

Changed
^^^^^^^


* Initialisation of JavaScript variable _paq is now more robust (#13)

`1.4.0 <https://github.com/brotkrueml/typo3-matomo-integration/compare/v1.3.2...v1.4.0>`_ - 2022-10-05
----------------------------------------------------------------------------------------------------------

Added
^^^^^


* Compatibility with TYPO3 v12 (#9)

`1.3.2 <https://github.com/brotkrueml/typo3-matomo-integration/compare/v1.3.1...v1.3.2>`_ - 2022-06-13
----------------------------------------------------------------------------------------------------------

Security
^^^^^^^^


* Properly escape content from PSR-14 events

`1.3.1 <https://github.com/brotkrueml/typo3-matomo-integration/compare/v1.3.0...v1.3.1>`_ - 2022-04-07
----------------------------------------------------------------------------------------------------------

Fixed
^^^^^


* Site ID cannot be configured through an environment variable (#8)

`1.3.0 <https://github.com/brotkrueml/typo3-matomo-integration/compare/v1.2.0...v1.3.0>`_ - 2022-02-15
----------------------------------------------------------------------------------------------------------

Added
^^^^^


* PSR-14 event for site search tracking (#4)
* PSR-14 event for adding attributes to the script tag (#5)

`1.2.0 <https://github.com/brotkrueml/typo3-matomo-integration/compare/v1.1.0...v1.2.0>`_ - 2022-02-02
----------------------------------------------------------------------------------------------------------

Added
^^^^^


* Option to disable browser feature detection (#3)

`1.1.0 <https://github.com/brotkrueml/typo3-matomo-integration/compare/v1.0.1...v1.1.0>`_ - 2021-10-13
----------------------------------------------------------------------------------------------------------

Added
^^^^^


* Option to track error pages (#1)
* Option to track JavaScript errors (#2)

`1.0.1 <https://github.com/brotkrueml/typo3-matomo-integration/compare/v1.0.0...v1.0.1>`_ - 2021-09-28
----------------------------------------------------------------------------------------------------------

Fixed
^^^^^


* Remove suggests to avoid dependency issue with EXT:matomo_widgets v1.1.2

`1.0.0 <https://github.com/brotkrueml/typo3-matomo-integration/releases/tag/v1.0.0>`_ - 2021-08-30
------------------------------------------------------------------------------------------------------

Initial release
