# USPTO ODP integration guide

This guide explains how phpIP uses USPTO Open Data Portal (ODP) data together with EPO OPS for patent family import.

## 1) How provider switching works

phpIP now uses a provider orchestrator (`FamilyDataService`) for family retrieval:

1. Try EPO OPS first (existing behavior).
2. If OPS succeeds, optionally enrich US members with USPTO ODP data (title/applicants/inventors/procedure).
3. If OPS fails and the input document number looks US, try building a synthetic single-member US record from USPTO ODP.

This means **you still use the same UI action**:

`Matters -> Create family from OPS`

No new UI menu is required.

## 2) Configuration

Set the following variables in `.env`:

```dotenv
# Enable/disable USPTO enrichment/fallback
USPTO_ODP_ENABLED=true

# Optional API key (if your ODP dataset requires one)
USPTO_ODP_API_KEY=

# Optional override: USPTO API base URL (default already works for ODP)
# USPTO_ODP_BASE_URL=https://api.uspto.gov

# Optional overrides (advanced only)
# USPTO_ODP_APPLICATION_ENDPOINT=/api/v1/patent/applications/{applicationNumber}
# USPTO_ODP_SEARCH_ENDPOINT=/api/v1/patent/applications/search
USPTO_ODP_SEARCH_FIELD=applicationNumberText
```

Then clear Laravel config cache:

```bash
php artisan optimize:clear
```

## 3) Required existing OPS settings

USPTO support does **not** replace OPS setup. Keep OPS credentials configured:

```dotenv
OPS_APP_KEY=...
OPS_SECRET=...
```

The orchestrator depends on OPS as the primary family source.

## 4) Validation checklist

1. Log in to phpIP.
2. Open `Matters -> Create family from OPS`.
3. Enter a document number from a family containing US members.
4. Run import.
5. Confirm that import no longer fails when OPS has sparse US party data.

If you have API access to USPTO ODP configured, US party/title/procedure fields may be enriched when missing in OPS.

## 5) Troubleshooting (USPTO ODP only)

### OPS import works but US enrichment does not

Check:

- `USPTO_ODP_ENABLED=true`
- valid API key (if required by your account/product)
- API key requirements for your ODP dataset
- network egress to the endpoint host from your phpIP server

## 6) Security notes

- Keep API keys in `.env`, never in source files.
- Restrict outbound network access from the server to approved API hosts only.
- Consider request logging/redaction policy for external API errors.
