# Citizen proposal OpenID Connect

Define settings in `settings.local.php`:

```php
$settings['citizen_proposal_openid_connect']['openid_connect'] = [
  'clientId'                 => 'client-id',
  'clientSecret'             => 'client-secret',
  'openIDConnectMetadataUrl' => 'http://idp-citizen.hoeringsportal.local.itkdev.dk/.well-known/openid-configuration',
];
```

Start authentication on `/citizen-proposal/openid-connect/authenticate` (route
name: `citizen_proposal_openid_connect.openid_connect_authenticate`). Set
`target-destination` (`OpenIDConnectController::QUERY_STRING_DESTINATION`) in
the query string to set the destination after authentication.

Example:

```php
Link::createFromRoute(
  'Authenticate',
  'citizen_proposal_openid_connect.openid_connect_authenticate',
  [
    OpenIDConnectController::QUERY_STRING_DESTINATION => Url::fromRoute('<current>')->toString(TRUE)->getGeneratedUrl(),
  ]
);
```

## Getting user data

Calling `getUserData` on the `Drupal\citizen_proposal_openid_connect\Helper`
service will return the current user data if any. `
