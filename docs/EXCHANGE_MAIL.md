# Sending mail through Microsoft 365 / Exchange Online

phpIP's `graph` mailer submits email to Microsoft Graph over HTTPS using
application-only OAuth. It integrates with Laravel's `Mail` facade, mailables,
notifications, and queued mail. Task reminders, renewal notices, sync reports,
password resets, and verification emails use the selected default mailer.
There is no EWS dependency, SMTP authentication, interactive user login, or new
email-sending web endpoint. No additional Composer packages are needed.

## 1. Grant send-only access to one mailbox

Use a **dedicated single-tenant Entra app registration** and a dedicated Exchange
Online sending mailbox. Keep its credentials separate from Authelia, SharePoint,
user sign-in, and other applications. Record the directory (tenant) ID,
application (client) ID, and the enterprise application's service principal object
ID. The last value is **not** the app registration's object ID.

Use **Exchange Online RBAC for Applications** to assign only `Application Mail.Send`
and scope it to the sending mailbox. The Microsoft 365 administrator can adapt
the following commands in Exchange Online PowerShell:

```powershell
Connect-ExchangeOnline

# Entra enterprise application's Object ID, not the app registration's Object ID.
New-ServicePrincipal -AppId <client-id> -ObjectId <enterprise-app-object-id> -DisplayName "phpIP mail"

New-ManagementScope -Name "phpIP sending mailbox" -RecipientRestrictionFilter "PrimarySmtpAddress -eq 'phpip@example.com'"

New-ManagementRoleAssignment -Name "phpIP send only" -App <enterprise-app-object-id> -Role "Application Mail.Send" -CustomResourceScope "phpIP sending mailbox"

Test-ServicePrincipalAuthorization -Identity <enterprise-app-object-id> -Resource phpip@example.com
Test-ServicePrincipalAuthorization -Identity <enterprise-app-object-id> -Resource unrelated-mailbox@example.com
```

Confirm that `Application Mail.Send` is in scope only for the intended mailbox and
that no read, write, full-access, or EWS roles have been assigned. Allow time for
permission changes to propagate.

**Do not also grant tenant-wide Microsoft Graph application `Mail.Send` in Entra
for this RBAC configuration.** Entra application permissions and Exchange RBAC
permissions are additive: a broad Entra grant can defeat the mailbox restriction.
Do not grant `Mail.Read`, `Mail.ReadWrite`, `full_access_as_app`, SMTP permissions,
or delegated user permissions. Remove unused permissions such as the default
delegated `User.Read` from this dedicated registration. The RBAC test above checks
Exchange assignments; audit Entra enterprise-application grants separately.

The OAuth scope is `https://graph.microsoft.com/.default`. This requests the
application's configured permissions; it does not itself narrow a broadly
authorized application to sending only. The administrator's permission assignments
are the security boundary, even if a credential is used outside phpIP.

## 2. Set Exchange Online as the default mailer in `.env`

Edit the deployed phpIP application's `.env` file, in the same directory as
`artisan`. `.env.example` documents the available settings; changing that example
file alone does not change the running application. If your deployment injects
environment variables instead of using a `.env` file, set the same variables there.
Replace existing entries rather than appending duplicate definitions.

**`MAIL_MAILER=graph` is the setting that makes Exchange Online the default.**
Laravel reads it through `config/mail.php`, builds the Graph transport, and routes
emails from the existing `Mail` facade, mailables, and mail notifications through
it. You do not need to change each reminder, template, or controller. Scheduled
jobs and queue workers also use this default when they load the updated
configuration. An explicit choice of another named mailer in custom code would
override the default for that call.

The settings have separate purposes:

| Setting | What to enter |
| --- | --- |
| `MAIL_MAILER` | `graph` to select Microsoft Graph as the default transport. |
| `GRAPH_MAIL_TENANT_ID` | Directory (tenant) ID from the Entra app registration's Overview page. Use the UUID, not a tenant name or `common`. |
| `GRAPH_MAIL_CLIENT_ID` | Application (client) ID from that same Overview page. Use the UUID, not either object ID. |
| `GRAPH_MAIL_MAILBOX` | Primary email address of the Exchange mailbox scoped in step 1, such as `phpip@example.com`. |
| `MAIL_FROM_ADDRESS` | The same address as `GRAPH_MAIL_MAILBOX`; this is Laravel's default From address. |
| `MAIL_FROM_NAME` | Display name recipients should see, such as `"My Firm — phpIP"`. |
| `GRAPH_MAIL_CERTIFICATE_PATH` / `GRAPH_MAIL_PRIVATE_KEY_PATH` | Local PEM file paths when using certificate authentication. |
| `GRAPH_MAIL_PRIVATE_KEY_PASSPHRASE` | Passphrase for the private key, if it is encrypted; otherwise leave empty. |
| `GRAPH_MAIL_CLIENT_SECRET` | Entra client-secret **value** when using the alternative secret-based authentication. Leave empty when using a certificate. |

The enterprise application's object ID is used for the PowerShell permission
assignment in step 1. It is not needed in phpIP's `.env`.

Choose **one** of the following complete mail configurations. Replace the example
UUIDs, addresses, paths, and credentials with your actual values. Quote values
containing spaces or `#`, including secrets and passphrases when necessary.

### Option A: Certificate authentication (preferred)

Upload the public RSA certificate to the Entra app registration under
**Certificates & secrets → Certificates**. Store its matching private key and the
certificate as local PEM files outside the repository and web-accessible
directories. Restrict the key to the phpIP service account (for example,
owner-readable only), and ensure both the web process and scheduled/queue workers
can read the files. The certificate must be currently valid and use an RSA key of
at least 2048 bits. PHP's OpenSSL extension must be available.

```dotenv
MAIL_MAILER=graph
MAIL_FROM_ADDRESS=phpip@example.com
MAIL_FROM_NAME="phpIP"

GRAPH_MAIL_TENANT_ID=11111111-1111-1111-1111-111111111111
GRAPH_MAIL_CLIENT_ID=22222222-2222-2222-2222-222222222222
GRAPH_MAIL_MAILBOX=phpip@example.com

GRAPH_MAIL_CERTIFICATE_PATH=/run/secrets/phpip-mail-cert.pem
GRAPH_MAIL_PRIVATE_KEY_PATH=/run/secrets/phpip-mail-key.pem
GRAPH_MAIL_PRIVATE_KEY_PASSPHRASE=
GRAPH_MAIL_CLIENT_SECRET=
```

This authenticates the application with a signed assertion. phpIP obtains
short-lived access tokens automatically; you do not need to copy tokens into
`.env` or sign in as a mailbox user. Assertions use RS256, expire after five
minutes, and are bound to this tenant's token endpoint.

### Option B: Client-secret authentication

Create a client secret under the Entra app registration's
**Certificates & secrets → Client secrets**. Copy its **Value**, which is shown
when it is created. The secret's ID is not a usable credential. Set an expiry and
arrange rotation before that date.

```dotenv
MAIL_MAILER=graph
MAIL_FROM_ADDRESS=phpip@example.com
MAIL_FROM_NAME="phpIP"

GRAPH_MAIL_TENANT_ID=11111111-1111-1111-1111-111111111111
GRAPH_MAIL_CLIENT_ID=22222222-2222-2222-2222-222222222222
GRAPH_MAIL_MAILBOX=phpip@example.com

GRAPH_MAIL_CLIENT_SECRET="replace-with-the-client-secret-value"
GRAPH_MAIL_CERTIFICATE_PATH=
GRAPH_MAIL_PRIVATE_KEY_PATH=
GRAPH_MAIL_PRIVATE_KEY_PASSPHRASE=
```

The certificate paths must be empty for this option. Setting both a secret and
certificate paths is rejected, so there is no ambiguity about which credential
the server uses. The same mailbox-scoped send-only permission from step 1 is
required for either authentication method.

### Sender, recipients, and existing SMTP settings

`GRAPH_MAIL_MAILBOX` chooses the mailbox in Graph's send request;
`MAIL_FROM_ADDRESS` supplies the default sender to Laravel-generated emails.
They must match. phpIP also rejects an individual email that sets a different
From, Sender, Return-Path, or envelope sender. Use Reply-To for staff replies.
Sending as aliases or on behalf of other mailboxes is intentionally unsupported.

Recipients remain controlled by the existing phpIP workflows. For example,
`MAIL_TO` and `MAIL_BCC` still configure task-reminder recipients; they do not
select the sending account. Renewal notices still use the recipients selected by
the renewal workflow.

When `MAIL_MAILER=graph`, `MAIL_HOST`, `MAIL_PORT`, `MAIL_USERNAME`, `MAIL_PASSWORD`,
and `MAIL_ENCRYPTION` are unused by the default mailer. Existing SMTP entries can
be left in place during migration, but they do not authenticate Graph or provide
a fallback. The application obtains OAuth tokens from Entra and sends over HTTPS.

Keep secrets in the deployment's secret store or protected environment file.
Never commit credentials, put tokens in URLs, expose debug pages in production,
or enable HTTP request/body logging for OAuth and Graph requests. Protect
Laravel's cached configuration as it can contain client secrets and key
passphrases. Use `APP_DEBUG=false` in production.

Allow outbound HTTPS to `login.microsoftonline.com` and `graph.microsoft.com`.
The implementation fixes both endpoint hosts, refuses redirects, and verifies TLS.
This version targets the public Microsoft cloud; sovereign-cloud endpoints and
on-premises Exchange are not supported.

## 3. Apply the configuration

From the deployed phpIP directory, run:

```sh
php artisan config:cache
php artisan queue:restart
```

`config:cache` rebuilds Laravel's cached configuration using the current
environment. With configuration cached, editing `.env` alone will not change the
selected mailer until the cache is rebuilt. In local development, use
`php artisan config:clear` instead if you do not want cached configuration.

`queue:restart` tells Laravel queue workers to restart after their current job;
their process manager must restart them so they load the new configuration. If
the deployment uses other persistent PHP or scheduler workers, reload them through
the deployment's usual process manager as well. Normal cron invocations of
`php artisan schedule:run` load configuration when they start. Repeat the relevant
reload steps after rotating certificates, keys, or secrets.

The `.env` settings select the transport and application identity. They do not
grant mailbox permissions. Complete step 1 before sending: the Exchange-side
role and resource scope determine what this application is authorized to do.

## 4. Verify before enabling production sends

1. Verify the positive and negative RBAC scope tests above and separately review
   Entra grants. Do not use test emails to unrelated people to validate access.
2. Send through an existing phpIP workflow to a controlled test recipient. Verify
   HTML/plain text, reply addresses, attachments when used, CC/BCC delivery, and that
   BCC addresses are absent from recipient-visible headers.
3. Verify a client or read-only staff account cannot trigger renewal notices.
   Those POST actions require authentication and the existing `readwrite` gate;
   Laravel's web CSRF middleware remains in place.
4. Check Exchange message trace and the sending mailbox. A Graph `202 Accepted`
   means accepted for processing, not confirmed delivery. Existing read-receipt
   headers are retained but cannot guarantee receipts.

The automated tests use mocked HTTP and exercise token acquisition and renewal,
certificate signatures, sender/envelope restrictions, BCC, attachments, redirects,
provider failures, and application authorization. They do not prove tenant
configuration, Exchange permission enforcement, deliverability, or recipient-side
BCC behavior; complete the tenant checks before production use.

### Configuration troubleshooting

| Symptom | Check |
| --- | --- |
| phpIP still attempts SMTP | Confirm `MAIL_MAILER=graph` in the deployed environment, rebuild the configuration cache, and restart persistent workers. Check that deployment-level variables are not overriding `.env`. |
| Tenant/application UUID configuration error | Use Directory (tenant) ID and Application (client) ID, not the enterprise-app object ID, app-registration object ID, or tenant domain name. |
| Credential configuration error | Configure exactly one method: both certificate/key paths, or a client-secret value. |
| Certificate unavailable or invalid | Check local PEM paths, service-account read permissions, certificate validity, RSA key size, and that the private key matches the uploaded certificate. |
| Token request rejected | Check the tenant/client IDs and the selected credential. For secrets, check the value and expiry; for certificates, check the public certificate registered in Entra. |
| Sender must match the configured mailbox | Make `MAIL_FROM_ADDRESS` and `GRAPH_MAIL_MAILBOX` identical, and check whether the sending workflow overrides the From or envelope sender. |
| Graph returns HTTP 403 | Review the scoped `Application Mail.Send` assignment for this app and mailbox, allow permission propagation time, and rerun the scope checks from step 1. |
| Graph accepts the message but it does not arrive | Check Exchange message trace, mailbox sending limits, and recipient filtering; acceptance is not proof of delivery. |

Use the sanitized phpIP errors and Microsoft administration tools for diagnosis.
Do not dump the process environment, cached configuration, OAuth requests, or
access tokens into logs or support messages.

## Failure behavior and limits

- Short-lived access tokens are reused only in the transport's process memory and
  renewed before expiry. No refresh tokens or access tokens are persisted.
- The only mail operation is `POST /v1.0/users/{configured-mailbox}/sendMail`.
  MIME preserves Laravel-generated bodies, alternatives, attachments, recipient
  fields, reply addresses, and headers without draft creation or mailbox reads.
- MIME content is limited to 3,000,000 bytes before base64 encoding to stay below
  the simple-send request limit. Larger messages fail before requesting a token.
  Upload sessions/draft workflows that would require broader access are excluded.
- A failed token request, invalid configuration, non-202 Graph response, or timeout
  raises a mail transport exception with no provider body or credentials. A 401
  clears the token so a later send can acquire a new one.
- There are no automatic retries or SMTP/log fallbacks. A timeout can occur after
  acceptance, so blindly retrying could send duplicate client notices. For a 429,
  an integer `Retry-After` value is included in the sanitized error. A queue retry
  policy is still controlled by the deployment; verify message status before
  replaying an ambiguous submission and respect Exchange sending limits.
- Graph saves sent mail to Sent Items by default. This does not grant the app
  read access to that folder. Use Exchange administration tools for delivery audits.
- This transport does not grant new application users permission to send mail. Its
  credentials authorize the server's established workflows; the server and mailbox
  credentials must remain protected. Operators should monitor sending volume and
  investigate unexpected use of the dedicated mailbox.

## Microsoft references

- [Graph sendMail](https://learn.microsoft.com/graph/api/user-sendmail?view=graph-rest-1.0)
- [Graph application-only authentication](https://learn.microsoft.com/graph/auth-v2-service)
- [RBAC for Applications in Exchange Online](https://learn.microsoft.com/Exchange/permissions-exo/application-rbac)
- [New-ServicePrincipal](https://learn.microsoft.com/powershell/module/exchangepowershell/new-serviceprincipal)
- [New-ManagementRoleAssignment](https://learn.microsoft.com/powershell/module/exchangepowershell/new-managementroleassignment)
- [Test-ServicePrincipalAuthorization](https://learn.microsoft.com/powershell/module/exchangepowershell/test-serviceprincipalauthorization)
