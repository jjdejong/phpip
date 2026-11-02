# Authelia login

phpIP supports optional Authelia sign-in alongside username/password login. It uses
the authorization code flow with S256 PKCE, `client_secret_basic` authentication,
and Authelia's authenticated UserInfo endpoint. Tokens are used only during the
callback and are not stored. Both services must be reachable over HTTPS, including
from the phpIP server to Authelia.

## Configure Authelia

Enable Authelia's OpenID Connect provider and register a confidential client. For
current Authelia releases, the client entry under `identity_providers.oidc.clients`
is:

```yaml
- client_id: phpip
  client_name: phpIP
  client_secret: '$pbkdf2-sha512$...'
  public: false
  authorization_policy: two_factor
  require_pkce: true
  pkce_challenge_method: S256
  redirect_uris:
    - https://phpip.example.com/login/authelia/callback
  scopes:
    - openid
    - profile
  response_types:
    - code
  grant_types:
    - authorization_code
  token_endpoint_auth_method: client_secret_basic
```

Generate a random client secret and its hash using Authelia's secret-generation
tools. Store the hash in Authelia and the corresponding plaintext secret in phpIP's
environment. This example assumes the OIDC provider (including its signing keys)
is already configured; consult the documentation for your installed Authelia version.

## Configure phpIP

```dotenv
AUTHELIA_ENABLED=true
AUTHELIA_ISSUER=https://auth.example.com
AUTHELIA_CLIENT_ID=phpip
AUTHELIA_CLIENT_SECRET=the-plaintext-client-secret
AUTHELIA_REDIRECT_URI=https://phpip.example.com/login/authelia/callback
```

The issuer is Authelia's public base URL, without `/api/oidc`. The redirect URI
must exactly match the registered URI and the public phpIP callback URL. Configure
`APP_URL` for the public HTTPS origin. Keep session cookies secure in production
(`SESSION_SECURE_COOKIE=true`) with SameSite `lax` so the browser sends the session
cookie when returning from Authelia. Run `php artisan config:cache` after changing
configuration (or `config:clear` during development).

## Accounts and permissions

An administrator must first create the phpIP account with a `login` exactly
matching Authelia's `preferred_username`, including case. Authelia's standard
`profile` scope supplies this claim. Emails and group names are not used to link
accounts. Users are never automatically created or assigned roles. The existing
phpIP account's role and company determine its access as usual. Treat changes to
Authelia usernames as account-linking changes; do not reassign a username to a
different person while the corresponding phpIP login remains active.

Login attempts expire after ten minutes and callbacks can be used only once.
Denied consent, expired attempts, missing accounts, and provider failures return
the user to the login page with a generic error. A second sign-in attempt in another
tab replaces the first pending attempt in that session.

phpIP logout ends the local phpIP session. The Authelia session remains active, so
signing in again can reuse it; global single logout is not implemented. Set
`AUTHELIA_ENABLED=false` and refresh cached configuration to remove the option.

Validate a deployment by signing in with an existing staff account and a client
account, confirming each retains its expected access, testing an unknown account,
and confirming local password login and logout still work.
