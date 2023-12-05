## [2.7.1](https://github.com/Gatoreviews/geonames/compare/v2.7.0...v2.7.1) (2023-12-05)


### Bug Fixes

* **migration:** update 2 files missing from prod... ([08cf506](https://github.com/Gatoreviews/geonames/commit/08cf5069e9f827d924ab478b1e360a8e654501a0))

## [2.7.0](https://github.com/Gatoreviews/geonames/compare/v2.6.0...v2.7.0) (2023-12-05)


### Features

* **commands:** new commands and update existing one to fit new functions ([8a45933](https://github.com/Gatoreviews/geonames/commit/8a45933dd54e9327b43c72a794fa68ccf2829136))
* **dbcaching:** add method for country in dbcaching service & adapter ([8609499](https://github.com/Gatoreviews/geonames/commit/86094999e5640dde008e7d05c6f93499bc413c6c))
* **export:** new command - hydrate database by children ([afaac6a](https://github.com/Gatoreviews/geonames/commit/afaac6a09cd6fd6f44fc0e974268fccd7f68194d))
* **export:** new methods to fetch geonames endpoints ([9ce7a50](https://github.com/Gatoreviews/geonames/commit/9ce7a502f02293cc45ecb8f33edf22c1d888f31e))
* **export:** oneToOne relation country<->level ([f334021](https://github.com/Gatoreviews/geonames/commit/f3340212996dd6bbaec2d8dfdb8eeea11e06bdb6))
* **geoApi:** new childrenJSON endpoint ([24be008](https://github.com/Gatoreviews/geonames/commit/24be00805515c6e92ebf17b6618d64b8449a9365))
* **logs:** monolog config to get logger info in console ([5bf930d](https://github.com/Gatoreviews/geonames/commit/5bf930dbe2e560a43c663b8ab897cce93d70c033))
* **services:** update AdminDivLocale service, loggers etc ([3a771be](https://github.com/Gatoreviews/geonames/commit/3a771be0540e48b6ac8b4df8c9d1b977bed5e9e1))


### Bug Fixes

* **controller:** wrong variable names in routes ([4f97ab9](https://github.com/Gatoreviews/geonames/commit/4f97ab9852f6e9562bf0eb4fb9b18c2838ec5e8d))
* **export:** findOne bug ([65dc38f](https://github.com/Gatoreviews/geonames/commit/65dc38fd78c6a9084e38150d8bc5adf4a7859017))
* **export:** Translations fallback + main Service refact ([2ab0a3d](https://github.com/Gatoreviews/geonames/commit/2ab0a3db505682f79e46219dffa38e0c7c413a1c))
* **misc:** remove older unavailable migrations ([9173199](https://github.com/Gatoreviews/geonames/commit/9173199036b4a36a356ab7ba90f7a18f22406177))
* **Typo:** #[DEV-5267](https://linear.app/gtrsuite/issue/DEV-5267) always upper case ([c39de8b](https://github.com/Gatoreviews/geonames/commit/c39de8b7ddeaf52bb8bf8d69dac9c2803ffc5e53))

## [2.6.1](https://github.com/Gatoreviews/geonames/compare/v2.6.0...v2.6.1) (2023-11-29)


### Bug Fixes

* **Typo:** #[DEV-5267](https://linear.app/gtrsuite/issue/DEV-5267) always upper case ([ebf6590](https://github.com/Gatoreviews/geonames/commit/ebf659019f3223173f60333076cd3c2da81baf01))

## [2.6.0](https://github.com/Gatoreviews/geonames/compare/v2.5.4...v2.6.0) (2023-11-27)


### Features

* **countries:** new countryList endpoint ([19b7c60](https://github.com/Gatoreviews/geonames/commit/19b7c60ad3073e78793838a0ef8487dfbc66d27a))
* **export:** update command + export service ([f04763d](https://github.com/Gatoreviews/geonames/commit/f04763dcfc0d72ca610b1a04f961bc0ca3b54388))


### Bug Fixes

* **api:** set cacheResponse before function ([d13b757](https://github.com/Gatoreviews/geonames/commit/d13b7577908a49fa9e0563d303a88ccd72180140))
* **tests:** accurate assertions ([ea34aa9](https://github.com/Gatoreviews/geonames/commit/ea34aa9810cbc19879d87492cf1ea451732be0ea))
* **tests:** rename folders + update config ([ca751d2](https://github.com/Gatoreviews/geonames/commit/ca751d258b6ef7c3c7d70dd64247ee49c5455c24))
* **translations:** BIGINT case now logs an error ([97c43b2](https://github.com/Gatoreviews/geonames/commit/97c43b2c4003616e700bdd54535cd065f26676c1))
* **Typo:** clearer error ([49a11d1](https://github.com/Gatoreviews/geonames/commit/49a11d1f1b2ea62dccc92a861dc3ae03caa85670))

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
