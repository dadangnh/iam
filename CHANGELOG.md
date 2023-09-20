# IAM Changelog

## Version 2.5.5 (current stable)
* create request endpoint for rfc sikka-ref 5 (#136)
* add new endpoint kantor luar
* add new endpoint unit luar
* add new endpoint jabatan luar
* add new endpoint jenis jabatan luar
* add new endpoint pegawai luar
* add new relation from role to new endpoint
* add new relation from user to pegawai luar

## Version 2.5.4
  * Fix pipeline to generate image on docker hub (#134)

## Version 2.5.3
  * Update docker config (#132)
  * Fix docker in docker build (#132)
  * Update Symfony version to v6.3.3 (#133)
  * Update dependencies

## Version 2.5.2
  * update gitlab-ci.yml
  * downgrade dependency phpunit (#131)

## Version 2.5.1
  * add new relasi role jabatan + jenis kantor
  * add new relasi role eselon + jenis kantor

## Version 2.5.0
  * Upgrade Symfony to v6.3 (#128)
  * Upgrade symfony recipes

## Version 2.4.2
  * Optimize the code and update dependencies (#127)

## Version 2.4.1
  * Fix bug in commonController (#126)
  * remove unused method in RoleHelper

## Version 2.4.0
  * Add or logic operator on roles (#123)
  * Update Symfony to v6.2.11
  * Updated Symfony Components and dependencies
  * Updated Symfony recipes
  * Updated Api Platform to v.3.1.12
  * Updated infra config
  * Fix docker image failed to build

## Version 2.3.4
  * Remove unused files (#122)
  * Update Symfony to v6.2.8
  * Updated Symfony Components and dependencies
  * Updated Symfony recipes
  * Updated Api Platform to v.3.1.7
  * disable graphql playground
  * Fix docker image failed to build

## Version 2.3.3
  * Expose legacy_kode_kpp, legacy_kode_kanwil, unit_id, kantor_id to JWT Token (#120)
  * Update Symfony to v6.2.6 (#121)
  * Updated Symfony Components and dependencies
  * Updated Symfony recipes
  * Updated Api Platform to v.3.1.2
  * disable graphql playground

## Version 2.3.1
  * Fix GitHub Action failed to run (#119)

## Version 2.3.0
  * Update Symfony to v6.2 (#114)
  * Update Api Platform to v3 (#115)
  * Update PHP to v8.2 (#116)
  * Use PostgreSQL 15 (#117)
  * Remove sensio/framework-extra-bundle packages (#118)
  * Updated Symfony recipes

## Version 2.2.4
  * Update infrastructure configuration from upstream (#113)
  * Updated Symfony Components and dependencies

## Version 2.2.3
  * Use camelCase on Entity Attributes (#111)
  * add ministryOfficeCode to Kantor Entity (#112)
  * Updated Symfony Components and dependencies
  * Updated Symfony recipes

## Version 2.2.2
  * Use Doctrine Types on Entities (#108)
  * Expose Kantor Filter on JabatanPegawais Endpoint (#109)
  * Expose DateFilter on JabatanPegawais Endpoint (#110)

## Version 2.2.1
  * Fix failed build on Caddy (#105)
  * Add location name on office entity (#106)
  * Fix failed test job
  * Updated Symfony Components and dependencies (#104)
  * Updated Symfony recipes
  * Added start and end date on ROLE (#107)
  * Fix user last change data

## Version 2.2.0
  * Display only active attributes (#101)
  * Fix deprecated on JWT Bundle (#102)
  * Update docker and infrastructure configuration (#103)
  * Updated Symfony Components and dependencies
  * Updated Symfony recipes

## Version 2.1.7
  * Fix failed publish to registries job (#99)
  * Add new endpoint for invalidating expired tokens (#100)
  * Updated dependencies

## Version 2.1.6
  * Optimize test job pipeline (#96)
  * Fix invalid role assignment (#97)
  * Fix failed test job (#98)
  * Updated dependencies

## Version 2.1.5
  * Fix role mapping (#93)
  * Change http status response code on custom endpoint (#94)
  * Updated Symfony Components and dependencies (#95)
  * Updated Symfony recipes

## Version 2.1.4
  * Added service account type for IAM users (#92)
  * Updated Symfony Components and Dependencies

## Version 2.1.3
  * Fix query result on JabatanPegawai Repository (#90)
  * Standardize controller method (#91)
  * Updated Symfony Components

## Version 2.1.2
  * Added new endpoint for Entity's parent-child relation (#89)
  * Updated Symfony Components and Dependencies

## Version 2.1.1
  * Added Jabatan <=> Unit relation on admin (#87)
  * Remove JabatanPegawais from Entity Attribute (#88)
  * Updated Symfony Components and Dependencies

## Version 2.1.0
  * Upgrade Major Symfony Components to v6 (v6.1) (#86)
  * Updated Symfony Recipes
  * Updated Dependencies

## Version 2.0.2
  * Updated Symfony Components and Dependencies

## Version 2.0.1
  * Fix ROLES attributes is not consistent (#85)
  * Updated Symfony Components and Dependencies

## Version 2.0.0
  * Upgrade Major Symfony Components to v6 (v6.0.7) (#72)
  * Updated Symfony Recipes
  * Stricter code
  * Fix GitHub Action Test (#83)

## Version 1.21.0
  * Updated Infrastructure config from upstream (#80)
  * Updated Symfony Components and Dependencies to v5.4.7 
  * Updated JWT Bundle (#81)
  * Added new logic on get atasan endpoint 
  * Added new attribute on Pegawai Entity (#82)

## Version 1.20.2
  * Change infrastructure configuration (#80)
  * Updated dependencies

## Version 1.20.1
  * Remove deprecated method (#79)
  * Updated symfony components and dependencies

## Version 1.20.0
  * Add attribute on pegawai endpoint (#78)
  * Updated symfony components and dependencies
  * Updated recipes

## Version 1.19.4
  * Expose more attributes on roles endpoint (#77)
  * Updated symfony components and dependencies
  * Updated recipes

## Version 1.19.3
  * Updated symfony components (v5.4.4) and dependencies
  * Updated recipes

## Version 1.19.2
  * Fix Nginx Image (#75)
  * Upgrade Backend to PHP 8.1 (#76)

## Version 1.19.1
  * Updated core dependencies (JWT Bundle)

## Version 1.18.6
  * Updated symfony components (v5.3.13) and dependencies
  * Updated recipes

## Version 1.19.0
  * Upgrade Symfony Components to Major Version v5.4 (#71)
  * Updated components and dependencies
  * Added GitHub Action Workflow (#73)
  * Fix Gitlab CI to push all images to Docker Hub (#74)

## Version 1.18.5
  * Fix Dockerfile and Gitlab CI (#70)
  * Updated components and dependencies

## Version 1.18.4
  * Add more attribute on entity (#68)
  * Updated components and dependencies

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
