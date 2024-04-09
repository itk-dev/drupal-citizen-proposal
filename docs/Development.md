# Development

```shell
mkdir -p web/sites/default/modules
git clone https://github.com/itk-dev/drupal-citizen-proposal web/sites/default/modules/citizen_proposal
cd web/sites/default/modules/citizen_proposal
```

## Fixtures

```shell
composer require --dev drupal/content_fixtures
drush --yes pm:enable citizen_proposal_fixtures
drush --yes content-fixtures:load --groups=citizen_proposal
```

## Coding standards

Our coding are checked by GitHub Actions (cf.
[.github/workflows/pr.yml](.github/workflows/pr.yml)). Use the commands below to
run the checks locally.

### PHP

```sh
docker run --rm --volume ${PWD}:/app --workdir /app itkdev/php8.3-fpm composer install
 Fix (some) coding standards issues
docker run --rm --volume ${PWD}:/app --workdir /app itkdev/php8.3-fpm composer coding-standards-apply
 Check that code adheres to the coding standards
docker run --rm --volume ${PWD}:/app --workdir /app itkdev/php8.3-fpm composer coding-standards-check
```

### Markdown

```sh
docker run --rm --volume ${PWD}:/app --workdir /app node:20 yarn install
 Fix (some) coding standards issues.
docker run --rm --volume ${PWD}:/app --workdir /app node:20 yarn coding-standards-apply/markdownlint
 Check that code adheres to the coding standards
docker run --rm --volume ${PWD}:/app --workdir /app node:20 yarn coding-standards-check/markdownlint
```

## Code analysis

We use [PHPStan](https://phpstan.org/) for static code analysis.

Running statis code analysis on a standalone Drupal module is a bit tricky, so we use a helper script to run the
analysis:

```sh
docker run --rm --volume ${PWD}:/app --workdir /app itkdev/php8.3-fpm scripts/code-analysis
```
