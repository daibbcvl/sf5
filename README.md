
## Testing

* Check coding standard:

```sh 
$ ./vendor/bin/php-cs-fixer fix --diff --dry-run -v
```
> You can remove _--dry-run_ option to automatically standardize the code

* Check phpstan

```shell
$ ./vendor/bin/phpstan analyse -l 1 src
```
  

* Unit & Functional Test:
```sh
$ ./bin/phpunit
```

* Test whole project:
```sh
$ composer test
```
