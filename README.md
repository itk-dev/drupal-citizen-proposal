# Hoeringsportal citizen proposal module

Code related to citizen proposal functionality.

## Cron jobs to run

For the functionality of the module to work properly, certain cron jobs need
to run.

These jobs may need to be modified to match server environment and server
directory naming.

### Cronjob for finishing overdue proposals

```sh
*/5 * * * * (cd /data/www/deltag_aarhus_dk/htdocs && /usr/local/bin/itkdev-docker-compose-server exec --user deploy phpfpm vendor/bin/drush hoeringsportal-citizen-proposal:finish-overdue-proposals) > /dev/null 2>&1; /usr/local/bin/cron-exit-status -c 'deltag.aarhus.dk' -v $?
```

## Settings

The module supports certain settings in settings.php

```php
// The duration of a proposal voting period.
$settings['proposal_period_length'] = '+180 days';

// The required votes for a proposal to pass.
$settings['proposal_support_required'] = 1500;
```

## Mails

We use [Drupal Symfony Mailer](https://www.drupal.org/project/symfony_mailer)
and a custom mail builder,
[CitizenEmailBuilder](src/Plugin/EmailBuilder/CitizenEmailBuilder.php), to get
the recipient email address from the proposal (node).

### SMTP

Edit `settings.local.php` and define SMTP host and port, e.g.

```php
# web/sites/default/settings.local.php
# For server deployment
$config['symfony_mailer.mailer_transport.smtp']['configuration']['host'] = 'host.docker.internal';
$config['symfony_mailer.mailer_transport.smtp']['configuration']['port'] = '25';
```

An confirmation email is sent to the citizen when a new proposal has been added
and an editor gets a mail notification as well.

When a proposal is published an email is sent to the citizen.

### Templates

Mail subjects and contents are edited on `/admin/citizen_proposal#edit-emails`.

Example templates:

```text
# ------------------------------------------------------------------------------
# proposal_created_citizen
# ------------------------------------------------------------------------------

Subject

Tak for dit borgerforslag på [site:url-brief]

Content

<p><strong>Hej [node:field_author_name]</strong></p>

<p>Tak for dit borgerforslag <em>[node:title]</em>.</p>

<p>Venlig hilsen<br>
Deltag aarhus</p>

# ------------------------------------------------------------------------------
# proposal_created_editor
# ------------------------------------------------------------------------------

Subject

Nyt borgerforslag på [site:url-brief]

Content

<p>Der er kommet et nyt borgerforslag: <br>
<strong><a href="[node:url]">[node:title]</a></strong> <a href="[node:edit-url]"> [rediger forslaget]</a></p>

# ------------------------------------------------------------------------------
# proposal_published_citizen
# ------------------------------------------------------------------------------

Subject

Dit borgerforslag på [site:url-brief] er blevet offentliggjort

Content

<p><strong>Hej [node:field_author_name]</strong></p>

<p>Dit borgerforslag <a href="[node:url]"><em>[node:title]</em></a> er nu offentliggjort.</p>

<p>Venlig hilsen<br>
Deltag aarhus</p>
```

The notification mails use templates in
`../../../themes/custom/hoeringsportal/templates/email/citizen-proposal/`

### Testing and debugging email

The Drush command `hoeringsportal-citizen-proposal:test-mail:send` can be used
to debug emails:

```sh
docker compose exec phpfpm vendor/bin/drush hoeringsportal-citizen-proposal:test-mail:send --help
```

After [loading fixtures](../../../../documentation/localDevelopment.md), run
something like

```sh
# Get a list of citizen proposal ids
docker compose exec phpfpm vendor/bin/drush sql:query "SELECT nid, title FROM node_field_data WHERE type = 'citizen_proposal'"
docker compose exec phpfpm vendor/bin/drush hoeringsportal-citizen-proposal:test-mail:send 87 create test@example.com
```

## Surveys

We use the [Webform module](https://www.drupal.org/project/webform) to render
surveys when creating a citizen proposal, and create webform submission to store
the survey responses.

To keep things simple we should allow only very few element types in webforms
(cf. `/admin/structure/webform/config/elements#edit-types`).

When rendering a webform survey, we skip rendering “Entity autocomplete”
elements and all actions (e.g. “Submit”). However, if a survey webform contains
an “Entity autocomplete” element allowing references to “Citizen proposal”
nodes, we set a reference to the proposal on the survey response when saving the
response (creating a submission).

## Restricting access to proposals

```php
# settings.local.php
$settings['citizen_proposal']['cpr_helper'] = [
  'azure_tenant_id' => '…,
  'azure_application_id' => '…',
  'azure_client_secret' => '…,

  'azure_key_vault_name' => '…',
  'azure_key_vault_secret' => '…',
  'azure_key_vault_secret_version' => '…',

  // Use a path for local testing of certificates.
  // 'certificate_path' => '…',

  'serviceplatformen_service_agreement_uuid' => '…',
  'serviceplatformen_user_system_uuid' => '…',
  'serviceplatformen_user_uuid' => '…',

  'serviceplatformen_service_uuid' => '…',

  // Production
  'serviceplatformen_service_endpoint' => 'https://prod.serviceplatformen.dk/service/CPR/PersonBaseDataExtended/5',
  'serviceplatformen_service_contract' => dirname(DRUPAL_ROOT).'/digitaliseringskataloget.dk/sf1520_4.0/PersonBaseDataExtendedService/wsdl/context/PersonBaseDataExtendedService.wsdl',

  // Test
  'serviceplatformen_service_endpoint' => 'https://exttest.serviceplatformen.dk/service/CPR/PersonBaseDataExtended/5',
  'serviceplatformen_service_contract' => dirname(DRUPAL_ROOT).'/digitaliseringskataloget.dk/sf1520_4.0/PersonBaseDataExtendedService/wsdl/context/PersonBaseDataExtendedService.wsdl',
];

$settings['citizen_proposal']['access_check'] = [
  // the value must match `drush config:get citizen_proposal.settings user_uuid_claim`
  'cpr_user_claim' => 'dk_ssn',

  // If one of these match, access is granted.

  // Property accessor path => value(s)
  'cpr_access_checks' => [
    // https://danmarksadresser.dk/adressedata/kodelister/kommunekodeliste
    '[adresse][aktuelAdresse][kommunekode]' => 751,
  ],
];
```

For testing purposes (cf. [Testing](../../../../documentation/Testing.md)), use

```php
# settings.local.php
$settings['citizen_proposal']['access_check']['cpr_result_checks'] = [
  // https://danmarksadresser.dk/adressedata/kodelister/kommunekodeliste
  '[adresse][aktuelAdresse][kommunekode]' => 955,
];
```

and sign in (in the local IdP) with username `aarhusianer` and password
`aarhusianer` to get access. Sign in with `ikke-aarhusianer` and
`ikke-aarhusianer` to be denied access (cf.
[docker-compose.override.yml](../../../../docker-compose.override.yml)).
