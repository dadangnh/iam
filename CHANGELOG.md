# DJPConnect - IAM Changelog

## Version 1.18.3
  * Fix broken css on admin area (#65)
  * Add more information on composer.json (#66)
  * Change on Gitlab CI to auto publish package and container image (#67)
  * Updated dependencies

## Version 1.18.2
  * Fix Bug The "groups" field cannot be autocompleted because it doesn't define the related CRUD controller FQCN with the "setCrudController()" method. (#63)
  * Fix Bug JabatanPegawai Endpoint (#64)
  * Modify framework configuration
  * Update gitlab CI
  * Add trusted proxy on example env file
  * Upgraded Dependencies and Components

## Version 1.18.1
  * Upgrade Symfony to v5.3.10 (#57)
  * Add Filter By ID on Entity (#58)
  * Use PHP 8 Attributes on Doctrine Annotations (#59)
  * Use Autocomplete on AssociationField (#61)
  * Allow client app to configure item per page (#62)
  * Upgraded Dependencies and Components

## Version 1.18.0
  * Add new endpoint to get atasan and kepala kantor (#52)
  * Add new endpoint to get data current in bulk data (#53)
  * Rename attribute names on token (#54)
  * Unable to fetch data if provide more than 3 chars as parameters (#55)
  * JWT refresh token not working (#56)

## Version 1.17.4
  * Added new endpoint (#51)
  * Updated components and dependencies

## Version 1.17.3
  * Added new endpoint (#50)
  * Updated components and dependencies
  * Upgrade PostgreSQL to v.14

## Version 1.17.2
  * Updated infrastructure configuration
  * Refactor endpoint

## Version 1.17.1
  * Deprecation Free Release (#49)

## Version 1.17.0
  * Add new endpoints (#43)
  * Fixes deprecated user class (#45)
  * Add endpoint to check whether a username is valid or not (#46)
  * Standardize the endpoint name - path and response status code (#47)
  * Fix failed job on CI (#48)

## Version 1.16.0
  * Symfony update to v.5.3

## Version 1.15.5
  * The last minor version for 1.15.x The next version will be 1.16.x based on Symfony 5.3 Contains a dependency and components update, and new endpoints.

## Version 1.15.4
  * Updated dependencies and symfony components

## Version 1.15.3
  * Upgrade the memory limit and added more CI Testing

## Version 1.15.2
  * Upgrade recipes and reduce endpoint output (#30)

## Version 1.15.1
  * Update symfony components to v.5.2.10 and some dependencies update

## Version 1.15.0
  * Add new container config (added nginx) as an alternative for caddy

## Version 1.14.2
  * Fixes User change password show request data (#25)

## Version 1.14.1
  * Update symfony component to v5.2.8 and dependencies update

## Version 1.14.0
  * Add new endpoint Application By ROLE or Application By TOKEN for unreleased Application/Modul (#21)
  * Upgrade symfony components to v5.2.7, and upgrade some dependencies

## Version 1.13.3
  * Expose PHP Service via port instead of socket (#19)

## Version 1.13.2
  * Fix caddy server cannot start from upstream (#18)

## Version 1.13.1
  * Update dependencies and components

## Version 1.13.0
  * Fixes Caddy web server is not started (#18)

## Version 1.12.1
  * Enhancement of version 1.12.0

## Version 1.12.0
  * New endpoint to get list of permissions from users token (#16)

## Version 1.11.0
  * Added new endpoint to check application access by role name. (#15)
  * Update symfony components and dependency

## Version 1.10.0
  * Added parent-child relation to unit entity (#14)

## Version 1.9.0
  * Added pembina on kantor and unit entity

## Version 1.8.2
  * Upgrade the symfony components and api platform dependencies

## Version 1.8.1
  * Fixes refresh token problem on #11

## Version 1.8
  * Embraces the use of PHP 8 Attributes instead of PHP Docs Annotation

## Version 1.7
  * Added support for decoding JWT Token on client
  * Remove Agama and Jenis Kelamin Entity
  * Update symfony components to version 5.2.3 and upgrade some dependency

## Version 1.6
  * Upgrade dependency and add more endpoint

## Version 1.5
  * Resolve some issues and feature request

## Version 1.4
  * Bug fixes

## Version 1.3
  * Use PHP 8

## Version 1.2
  * Implement Redis as Cache Backend for session, query, etc.

## Version 1.1
  * Move the service inside all docker

## Version 1.0
First release. Contains all working code for initial IAM workload
