"""HTTP smoke check for an isolated, freshly seeded Docker installation.

Usage: python3 tests/docker-smoke.py [http://localhost:8000]
Uses the seed account; never run against a live portfolio.
"""

import http.cookiejar
from html.parser import HTMLParser
import sys
import urllib.parse
import urllib.request


class LoginPage(HTMLParser):
    def __init__(self):
        super().__init__()
        self.token = None
        self.assets = set()

    def handle_starttag(self, tag, attrs):
        attrs = dict(attrs)
        if tag == "input" and attrs.get("name") == "_token":
            self.token = attrs.get("value")
        for name in ("src", "href"):
            value = attrs.get(name, "")
            if "/build/assets/" in value:
                self.assets.add(value)


base = (sys.argv[1] if len(sys.argv) > 1 else "http://localhost:8000").rstrip("/")
# Ignore host proxy settings for this local-only smoke check.
opener = urllib.request.build_opener(
    urllib.request.ProxyHandler({}),
    urllib.request.HTTPCookieProcessor(http.cookiejar.CookieJar()),
)


def get(path):
    return opener.open(urllib.parse.urljoin(base + "/", path), timeout=15)


with get("/up") as response:
    assert response.status == 200, "Laravel health check failed"
with get("/login") as response:
    page = LoginPage()
    page.feed(response.read().decode())
assert page.token, "Login page has no CSRF token"
assert page.assets, "Login page has no compiled Vite assets"
for asset in page.assets:
    # APP_URL may differ from the address used by this check.
    path = urllib.parse.urlsplit(asset).path
    with get(path) as response:
        assert response.status == 200, f"Compiled asset unavailable: {path}"

payload = urllib.parse.urlencode(
    {"_token": page.token, "login": "phpipuser", "password": "changeme"}
).encode()
with opener.open(base + "/login", data=payload, timeout=15) as response:
    assert "/login" not in response.url, "Seeded account could not log in"
with get("/home") as response:
    assert urllib.parse.urlsplit(response.url).path == "/home", "Session was not retained"
    assert response.status == 200, "Authenticated dashboard failed"
print("Passed: Laravel health, login page, compiled assets, database-backed login, persisted session, dashboard.")
