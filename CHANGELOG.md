## [2.5.4](https://github.com/Gatoreviews/geonames/compare/v2.5.3...v2.5.4) (2023-11-21)


### Bug Fixes

* **CI:** auto rebase dev ([6ed0fae](https://github.com/Gatoreviews/geonames/commit/6ed0fae6be554b511b9c8661ff27ffba81c70155))
* **Subdvision:** default response content + divers code cleaning ([0a67995](https://github.com/Gatoreviews/geonames/commit/0a67995c66f3eaa310f36a2edd97c86b3bfbdae6))

## [2.5.3](https://github.com/Gatoreviews/geonames/compare/v2.5.2...v2.5.3) (2023-11-21)


### Bug Fixes

* **api:** update UsedLevel in Api ([86d5b52](https://github.com/Gatoreviews/geonames/commit/86d5b528174e8a184c48b7ea93513cc00a0522e5))

## [2.5.2](https://github.com/Gatoreviews/geonames/compare/v2.5.1...v2.5.2) (2023-11-21)


### Bug Fixes

* **search:** oneResultOnly and used level ([ab761ff](https://github.com/Gatoreviews/geonames/commit/ab761ffc6f136ce5a4afcb076b51d1c8151f09f3))

## [2.5.1](https://github.com/Gatoreviews/geonames/compare/v2.5.0...v2.5.1) (2023-11-21)


### Bug Fixes

* **locales:** link, lauc, abbr, wkdt removed from fetching ([f531ad9](https://github.com/Gatoreviews/geonames/commit/f531ad9f5531e3c27f7eaaadf6f6bea1f84311e4))
* **search:** add fcodes in request URL for Places ([60d906c](https://github.com/Gatoreviews/geonames/commit/60d906cf018ae0792e3f2dfe757dc2865ba7e504))
* **search:** update max level in api script ([f36cae9](https://github.com/Gatoreviews/geonames/commit/f36cae9e2517883debc013698e451ffa8309fea1))

## [2.5.0](https://github.com/Gatoreviews/geonames/compare/v2.4.1...v2.5.0) (2023-11-20)


### Features

* **export:** Entities update for specific Id0 fetching ([3717eb5](https://github.com/Gatoreviews/geonames/commit/3717eb5643e2c1cf12b613c02cc3aa7105637341))
* **export:** main script -  recursive function ([d472d83](https://github.com/Gatoreviews/geonames/commit/d472d8332080b900aa6c2fafb877fdae6dbf831e))
* **export:** new command for export function ([0d8ce78](https://github.com/Gatoreviews/geonames/commit/0d8ce789fe2846fbbb78ab80f9c98fca6a1310da))
* **export:** repositories queries ([a0c5ff3](https://github.com/Gatoreviews/geonames/commit/a0c5ff34f6b74db19eb5898abaefc15ee437533a))
* **export:** update entity locales for translation override fetch ([124ceed](https://github.com/Gatoreviews/geonames/commit/124ceed691eb4844b737c43bd6fb424f886c2a9e))
* **export:** WIP new service + new export function ([7a6bdff](https://github.com/Gatoreviews/geonames/commit/7a6bdff222c1192a652badb7376a4c0266455858))


### Bug Fixes

* **export:** update raw query to object ([8367468](https://github.com/Gatoreviews/geonames/commit/8367468f9df74b9bba59f686ee744766aaf8c44e))
* **search:** use MaxLevel instead of UsedLevel ([415bbf0](https://github.com/Gatoreviews/geonames/commit/415bbf0b985ad9a162752c75dd0b3932b5420461))

## [2.4.1](https://github.com/Gatoreviews/geonames/compare/v2.4.0...v2.4.1) (2023-11-17)


### Bug Fixes

* **dbHydration:** wrong array conversion position... ([8adeb9b](https://github.com/Gatoreviews/geonames/commit/8adeb9b448d08c7d0429a2ed0fdd61fdc94f3ca6))

## [2.4.0](https://github.com/Gatoreviews/geonames/compare/v2.3.0...v2.4.0) (2023-11-17)


### Features

* **altCodes:** #[DEV-5166](https://linear.app/gtrsuite/issue/DEV-5166) scénario mi_8 ([d547f9e](https://github.com/Gatoreviews/geonames/commit/d547f9ea27d3a337ac27b229289aa5de8c7d3279))


### Bug Fixes

* **dbHyration:** clean batchGet command + Readme update ([fa56584](https://github.com/Gatoreviews/geonames/commit/fa56584c683eb8fd7db69be24aab75d5819f7fe4))
* **dbHyration:** more accurate API error information. ([abcb92e](https://github.com/Gatoreviews/geonames/commit/abcb92e0523b3387c763fcd29942123b2d65142d))

## [2.3.0](https://github.com/Gatoreviews/geonames/compare/v2.2.1...v2.3.0) (2023-11-16)


### Features

* **altCodes:** add AltCodes fetching during import ([3c03e95](https://github.com/Gatoreviews/geonames/commit/3c03e957fb02892917344a36ffcc9f4e6f18c1f9))

## [2.2.1](https://github.com/Gatoreviews/geonames/compare/v2.2.0...v2.2.1) (2023-11-16)


### Bug Fixes

* **geojson:** quick service fix ([2fff003](https://github.com/Gatoreviews/geonames/commit/2fff003c008832647a168ae15dc91520e077f8e6))
* **levels:** command and service update ([f33e82c](https://github.com/Gatoreviews/geonames/commit/f33e82c86082af1a8fd773552f95e301ca282910))
* **levels:** remove useless class use ([2bc50aa](https://github.com/Gatoreviews/geonames/commit/2bc50aafbf13cf09f4099ae8e3dd21b65dfa07b5))

## [2.2.0](https://github.com/Gatoreviews/geonames/compare/v2.1.6...v2.2.0) (2023-11-14)


### Features

* **commands:** new AltCodes update command ([f5542ad](https://github.com/Gatoreviews/geonames/commit/f5542ad9862350097f4c6df38406e8362e0aadd4))
* **security:** token access on /administrativedivisions/api ([e4c249b](https://github.com/Gatoreviews/geonames/commit/e4c249bff99c6a19aced87f7394cd547c3220519))


### Bug Fixes

* add yarn lock ([ac9b391](https://github.com/Gatoreviews/geonames/commit/ac9b3910d8223fac32fb32eb30c3607fdcdbfea8))
* **altCodes:** api export fix ([915ca8b](https://github.com/Gatoreviews/geonames/commit/915ca8b1ad4a5d61d1371b7e517ff24962eaa16d))
* **altcodes:** fix geonames/search/ ([c6cb52d](https://github.com/Gatoreviews/geonames/commit/c6cb52d934ecc4f4b532a3ea6c3a405a2c5c3d67))
* **localesApi:** fix entityManager flush ([ba8a436](https://github.com/Gatoreviews/geonames/commit/ba8a43633846701c6a04276ee65bbc9a109f80a2))
* **search:** update latLngSearch response to avoid 500 ([74a4376](https://github.com/Gatoreviews/geonames/commit/74a43768c233909ce8ea81c2c31ed183364b0358))
