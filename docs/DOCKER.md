# Running phpIP with Docker

The stack contains PHP 8.4 with Apache, MySQL 8.4, and an optional Laravel
scheduler. Composer dependencies and Vite assets are built into the image;
PHP, Composer, Node, and MySQL do not need to be installed on the host.
Use Docker Engine/Desktop with Compose v2 and BuildKit.
The bundled MySQL defaults are tuned for a small test Droplet (one CPU and about
512 MiB RAM); larger installations can increase its buffer pool and connections.

## DigitalOcean App Platform

App Platform builds the `Dockerfile` but does not start services from this
repository's `compose.yaml`. The `DB_HOST=db` setting is supplied by Compose's
app service; it cannot resolve in an App Platform container by itself. For App
Platform, attach a MySQL database component or another reachable MySQL service
and set `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, and `DB_PASSWORD` in
the app component from that database's connection details. Use its private host
when both components are in the same DigitalOcean network. Do not set
`DB_HOST=127.0.0.1` unless MySQL actually runs inside the app container.

After setting the database connection, run `php artisan migrate --force` in an
App Platform console or one-off job. Run `php artisan db:seed --force` only for
a new test database; it creates the initial test user. App Platform runs the
web container only, so schedule tasks separately or use the Compose setup on a
Droplet, which starts both the app and database services together.

## Ubuntu Droplet setup

Install Docker Engine, Compose v2, and Git. On Ubuntu 24.04 the Compose package
is named `docker-compose-v2`:

```sh
sudo apt update
sudo apt install -y docker.io docker-compose-v2 git
sudo systemctl enable --now docker
docker compose version
```

Clone the repository (use the PR branch instead of `master` if testing before
the Docker changes are merged):

```sh
git clone https://github.com/srdco/phpip.git ~/phpip
cd ~/phpip
cp .env.docker.example .env.docker
chmod 600 .env.docker
```

Set `APP_KEY` in `.env.docker` to `base64:` followed by the output of
`openssl rand -base64 32`. Set distinct `DB_PASSWORD` and `MYSQL_ROOT_PASSWORD`
values, for example with `openssl rand -hex 24` twice. Set your company name,
locale, timezone, and `APP_URL` for your domain. Keep the key and database
passwords stable. Leave `APP_BIND_ADDRESS=127.0.0.1` when an HTTPS reverse proxy
will forward requests to the Droplet's port 8000. Compose does not configure
DNS or HTTPS. For TLS termination at that proxy, set `TRUSTED_PROXIES` to its
Docker-facing IP (or CIDR) so Laravel accepts its forwarded HTTPS header. Leave
this unset when accessing the app directly without a reverse proxy. To inspect
the Compose bridge gateway, run `docker network inspect phpip_default` and use
the gateway shown there only if that is the address your proxy uses to reach
the app.

On a 512 MiB Droplet, check memory and swap using `free -h` and `swapon --show`
before building. If swap is empty, add 2 GiB of swap for the Node asset build
and PHP extension compilation:

```sh
sudo fallocate -l 2G /swapfile
sudo chmod 600 /swapfile
sudo mkswap /swapfile
sudo swapon /swapfile
echo '/swapfile none swap sw 0 0' | sudo tee -a /etc/fstab
```

Build the application, initialize the database, and start the web service:

```sh
docker compose --env-file .env.docker build app
docker compose --env-file .env.docker up -d --wait db
docker compose --env-file .env.docker run --rm app php artisan migrate --force
docker compose --env-file .env.docker run --rm app php artisan db:seed --force
docker compose --env-file .env.docker up -d --wait app
docker compose --env-file .env.docker ps
```

If MySQL reports `--initialize specified but the data directory has files in
it` on first boot, an interrupted initialization may have left partial files
in the database volume. Inspect its logs:

```sh
docker compose --env-file .env.docker logs --tail=100 db
```

Only for a brand-new test setup with no migrations or needed data, remove the
failed database volume and retry:

```sh
docker compose --env-file .env.docker down
docker volume ls --filter name=phpip_db-data
docker volume rm phpip_db-data
docker compose --env-file .env.docker up -d --wait db
```

This removes only the database volume. `docker compose down -v` also deletes
application storage; never remove volumes containing data you need.

After the app is healthy, configure your domain's HTTPS reverse proxy to forward
to `http://127.0.0.1:8000`, then open the domain set in `APP_URL`. A fresh seed
creates `phpipuser` / `changeme`; change that password immediately. The
scheduler remains disabled until explicitly enabled.

## Local Docker setup

From the repository root:

```sh
cp .env.docker.example .env.docker
chmod 600 .env.docker
```

Edit `.env.docker`. Set distinct random `DB_PASSWORD` and `MYSQL_ROOT_PASSWORD`
values, and set `APP_KEY` to `base64:` followed by the output of
`openssl rand -base64 32`. Keep this key across updates and restores. Set your
company name, URL, locale, and timezone. The Docker environment file is ignored
by Git and excluded from image builds; your existing `.env` is not used by the
containers.

Always pass `--env-file .env.docker`: Compose uses it both for database settings
and container configuration. Omitting it may select settings from a host `.env`.

```sh
docker compose --env-file .env.docker build app
docker compose --env-file .env.docker up -d --wait db
docker compose --env-file .env.docker run --rm app php artisan migrate --force
docker compose --env-file .env.docker run --rm app php artisan db:seed --force
docker compose --env-file .env.docker up -d --wait app
```

Open <http://localhost:8000>. Apache listens on container port 8080 for
DigitalOcean App Platform compatibility; Compose maps it to host port 8000 by
default. A fresh seed creates the upstream initial login `phpipuser` / `changeme`;
change its password immediately. Seeding is an explicit
first-install action, never an automatic container startup action. Do not seed
an existing installation or import sample data into a real portfolio.

The web port binds to localhost by default. MySQL has no published host port.
`APP_BIND_ADDRESS` and `APP_PORT` control the web binding. For a public deployment,
configure HTTPS and the correct application URL and proxy handling before
exposing the application.

## Database details

Use MySQL 8.4 for this stack. Current migrations require `utf8mb4_0900_ai_ci` and
rebuild stored functions, procedures, views, and triggers. Old installation
documentation describing MySQL 5.7 or older MariaDB versions is not applicable.

The image removes explicit `DEFINER` clauses from its copy of the baseline
schema. Fresh database objects therefore belong to the configured importing
account, instead of the dump's `phpip@localhost` account. The checked-in schema
is unchanged. The application account has privileges on its database only;
`log_bin_trust_function_creators=1` is set on the bundled MySQL server to permit
routine/trigger creation without granting global administration privileges.

For an existing installation, restore a backup into an isolated database first,
review imported object definers, then run `migrate --force`. Do not point this
stack at the live database as a first test. Containers never automatically
migrate or reset the database.

Changing database credentials in `.env.docker` does not change accounts in an
already initialized MySQL volume. Update those accounts explicitly and recreate
the application containers when rotating credentials.

## Scheduler and integrations

Mail defaults to the application log. Configure SMTP, recipients, OPS, Renewr,
and any SharePoint settings in `.env.docker` as needed; see `.env.example` and
the integration guides for available options. Then enable the scheduler:

```sh
docker compose --env-file .env.docker --profile scheduler up -d scheduler
docker compose --env-file .env.docker exec scheduler php artisan schedule:list
```

The existing schedule syncs Renewr on Mondays at 03:00 and sends task reminders
at 06:00, using `APP_TIMEZONE`. The scheduler runs as `www-data` and shares the
application storage volume, including file cache locks and Renewr sync state.
Run one scheduler service per installation. Queues remain synchronous; a queue
worker or Redis service is not required for this configuration.

Stop it with `docker compose --env-file .env.docker stop scheduler`. Recreate
containers after environment changes; `restart` alone does not reload values.

## Updates and checks

Back up the database, storage, and environment file first. Stop the scheduler
while applying updates, rebuild the image, run migrations once, then recreate
the services:

```sh
docker compose --env-file .env.docker stop scheduler
docker compose --env-file .env.docker build app
docker compose --env-file .env.docker run --rm app php artisan migrate --force
docker compose --env-file .env.docker up -d --wait app
# If scheduling was enabled:
docker compose --env-file .env.docker --profile scheduler up -d scheduler
```

For a production update, take the app out of service during migrations and plan
rollback using the pre-update backup; rebuilding an older image does not undo
database changes.

```sh
docker compose --env-file .env.docker ps
docker compose --env-file .env.docker logs --tail=100 app
docker compose --env-file .env.docker exec app php artisan migrate:status
curl --fail http://localhost:8000/up
```

The image's `/up` health check establishes that Laravel boots; it does not prove
database or third-party integration health. Verify login, matter/event workflows,
renewal calculations, and integrations against an isolated copy before rollout.
The runtime image excludes development dependencies, including PHPUnit.

The Docker GitHub Actions workflow builds a fresh stack, migrates and seeds it,
checks HTTP login and compiled assets, and replaces the app container to check
database and storage persistence. The same HTTP check can be run against a
disposable seeded stack with `python3 tests/docker-smoke.py`. This is a container
smoke check, not the full application test suite.

The image creates active `config/renewal.php` and `config/renewr.php` from their
tracked example files. This gives renewal pages usable defaults and lets the
Renewr URL and key use the documented environment variables. Private host copies
of either active config file are excluded from the image build. To supply a
custom `config/renewal.php`, copy and edit the example on the host, then create
`compose.override.yaml` (ignored by Git) to mount it into both services:

```sh
cp config/renewal.example.php config/renewal.php
```

```yaml
services:
  app:
    volumes:
      - ./config/renewal.php:/var/www/html/config/renewal.php:ro
  scheduler:
    volumes:
      - ./config/renewal.php:/var/www/html/config/renewal.php:ro
```

The host `config/renewal.php` is ignored by Git; keep installation-specific
billing credentials out of the image.

## Persistence and backups

The `db-data` volume stores MySQL data. The `app-storage` volume stores application
files, file sessions/cache, and Renewr sync state. `docker compose down` preserves
them; **`down -v` deletes them**. Keep `.env.docker` in your secure backup as well.

Create a logical database backup without putting the password in shell history:

```sh
docker compose --env-file .env.docker exec -T db sh -c \
  'MYSQL_PWD="$MYSQL_PASSWORD" exec mysqldump -u"$MYSQL_USER" --no-tablespaces --single-transaction --routines --triggers "$MYSQL_DATABASE"' \
  > phpip-backup.sql
```

Keep backups outside the repository/build context. To back up storage, stop the
app and scheduler briefly so their files do not change while archiving:

```sh
docker compose --env-file .env.docker stop app scheduler
docker compose --env-file .env.docker run --rm --no-deps --entrypoint tar app \
  -czf - -C /var/www/html storage > phpip-storage.tar.gz
docker compose --env-file .env.docker up -d app
# Restart the scheduler too if it was enabled.
```

Practice restoring both backups into a separate Compose project before relying
on them. Restore the original application key with the data.

## Builds behind a managed HTTPS proxy

Build steps accept an optional BuildKit `proxy_ca` secret. Pass a combined CA
bundle when the environment requires its own CA, keeping TLS verification on:

```sh
docker build --secret id=proxy_ca,src=/etc/ssl/certs/ca-certificates.crt -t phpip:local .
```

This secret is used for Yarn and Composer and is not stored in image layers.
Use the normal Compose startup commands afterwards. Runtime API calls behind
such a proxy also need that environment's proxy and read-only CA configuration;
build-time trust does not configure runtime trust.
