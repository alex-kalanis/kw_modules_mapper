# kw_modules_mapper

![Build Status](https://github.com/alex-kalanis/kw_modules_mapper/actions/workflows/code_checks.yml/badge.svg)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/alex-kalanis/kw_modules_mapper/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/alex-kalanis/kw_modules_mapper/?branch=master)
[![Latest Stable Version](https://poser.pugx.org/alex-kalanis/kw_modules_mapper/v/stable.svg?v=1)](https://packagist.org/packages/alex-kalanis/kw_modules_mapper)
[![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%207.4-8892BF.svg)](https://php.net/)
[![Downloads](https://img.shields.io/packagist/dt/alex-kalanis/kw_modules_mapper.svg?v1)](https://packagist.org/packages/alex-kalanis/kw_modules_mapper)
[![License](https://poser.pugx.org/alex-kalanis/kw_modules_mapper/license.svg?v=1)](https://packagist.org/packages/alex-kalanis/kw_modules_mapper)
[![Code Coverage](https://scrutinizer-ci.com/g/alex-kalanis/kw_modules_mapper/badges/coverage.png?b=master&v=1)](https://scrutinizer-ci.com/g/alex-kalanis/kw_modules_mapper/?branch=master)

## Modules with Mapper

Use mapper as source of information about modules. And then the usual stuff as with the
normal kw_mapper. 

### PHP Installation

```bash
composer.phar require alex-kalanis/kw_modules_mapper
```

(Refer to [Composer Documentation](https://github.com/composer/composer/blob/master/doc/00-intro.md#introduction) if you are not
familiar with composer)


### PHP Usage

1.) Use your autoloader (if not already done via Composer autoloader)

2.) Add some external packages with connection to the local or remote services.

3.) Connect the "\kalanis\kw_modules_mapper\ModulesLists\Mapper" into your app and set necessary params.

4.) Extend your libraries by interfaces inside the package.

5.) Just include it into your bootstrap od DI - use instead of usual kw_mapper

### Caveats

This package needs some storage, usually DB or file. So you need to set it to conform
your use case.
