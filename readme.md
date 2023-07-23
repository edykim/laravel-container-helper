# Laravel Container Helper

[![Latest Stable Version](http://poser.pugx.org/edykim/laravel-container-helper/v)](https://packagist.org/packages/edykim/laravel-container-helper) [![License](http://poser.pugx.org/edykim/laravel-container-helper/license)](https://packagist.org/packages/edykim/laravel-container-helper) [![PHP Version Require](http://poser.pugx.org/edykim/laravel-container-helper/require/php)](https://packagist.org/packages/edykim/laravel-container-helper)

This package provides a simple helper for Laravel Container. It allows you to generate an inline utility class that implements a given interface, making your application's logic more configurable.

## Getting Started

### Installation

```bash
$ composer require edykim/laravel-container-helper
```

### Proxy

This function offers a proxy instance of a given concrete implementation for lazy instantiation.

```php
use function Edykim\LaravelContainerHelper\Support\instance;

// ...
$app->bind(
  CalculatorInterface::class,
  instance(CalculatorInterface::class)
    ->proxy(HeavilyLoadedCalculator::class)
);
```

### Sequence

```php
use function Edykim\LaravelContainerHelper\Support\instance;

// ...
$app->bind(
  CalculatorInterface::class,
  instance(CalculatorInterface::class)
    ->sequence(
      SimpleProductCalculator::class,
      DigitalProductCalculator::class,
      ShippingChargeCalculator::class,
    )
);
```

### Conditional

```php
class HasDigitalProducts {
  public function check(Cart $cart): bool {
    // ...
  }
}
```

```php
use function Edykim\LaravelContainerHelper\Support\instance;

// ...
$app->bind(
  CalculatorInterface::class,
  instance(CalculatorInterface::class)
    ->when(
      HasDigitalProduct::class,
      DigitalProductCalculator::class,
      SimpleProductCalculator::class,
    )
);
```

### Nested

```php
use function Edykim\LaravelContainerHelper\Support\instance;
use Edykim\LaravelContainerHelper\Support\Instance;

// ...
$app->bind(
  CalculatorInterface::class,
  instance(CalculatorInterface::class)
    ->with(fn (Instance $instance) => 
      $instance->sequence(
        $instance->proxy(
          $instance->when(
            HasCouponWithThirdPartyCompany::class,
            ThirdPartyCouponCalculator::class,
            NothingCalculator::class,
          ),
        ),
        SimpleProductCalculator::class,
        DigitalProductCalculator::class,
        $instance->when(
          [IsPlusLevelCustomer::class, 'validate'],
          PlusLevelCustomerDiscountCalculator::class,
          $instance->when(
            [IsMedicalProvider::class, 'validate'],
            MedicalProviderDiscountCalculator::class,
            NothingCalculator::class,
          ),
        ),
      ),
    );
);
```

