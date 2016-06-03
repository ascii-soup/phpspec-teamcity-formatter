# PHPSpec TeamCity Formatters Extension
This PHPSpec extension provides a TeamCity formatter for PHPSpec.

## Requirements

This extension requires:

 * PHP 5.3.3 or later.

## Installation
To install this, make sure you are using the latest release of PHPSpec, and then add the following to your
`composer.json`:

```json
"require-dev": {
    ...,
    "ascii-soup/phpspec-teamcity-formatters": "1.*"
}
```

Install or update your dependencies, and then in a `phpspec.yml` file in the root of your project, add the following:

```yaml
extensions:
    - AsciiSoup\PhpSpec\TeamCityFormatter\Extension
```

Then, you can add a `--format` switch to your `phpspec` command as follows:

```sh
$ bin/phpspec run --format teamcity
```