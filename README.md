CONTENTS OF THIS FILE
------------
* Introduction
* Requirements
* Configuration
* Vision

INTRODUCTION
------------
This module uses a slightly modified JQuery Idle Hands script (c) 2020 Brian Dady
to log out users after a period of inactivity from their Drupal session. This requires
that the user has javascript enabled as well as JQuery available from Drupal.
JQuery Idle Hands uses basil.js (c) Wisembly and contributors for managing
user session storage. Licensing information on these scripts can be found in their
respective libraries under this module's /lib folder.

REQUIREMENTS
------------
* Drupal 10

CONFIGURATION
------------
* Permissions configuration: Home >> Administration >> People
(/admin/people/permissions)
* Module configuration: Home >> Administration >> Configuration >> People
(/admin/config/people/idle_hands_logout)
* This module is installed with features disabled. Ensure you enable the module from
the module configuration form before use.
* This module allows for logging out users who have logged into Drupal using the
SimpleSAML Auth module. Select the Use SAML Auth checkbox if you have users who log
into Drupal using the SimpleSAML module. Do not enable this feature unless you have
SimpleSAML Auth installed.

VISION
------------
This module is an early alpha and was developed as an internal challenge based on very
particular requirements. No support or warranty is expressed or implied. It has been
posted in the hopes that it can assist you in your project.
