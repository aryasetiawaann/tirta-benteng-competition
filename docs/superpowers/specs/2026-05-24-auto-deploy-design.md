# Auto-Deploy via GitHub Actions + SSH

**Date:** 2026-05-24  
**Status:** Approved

## Problem

Every code update requires manually SSHing into the Hostinger server, entering credentials, running `git pull`, `composer install`, and `php artisan` commands. This is slow and error-prone.

## Goal

Automatically deploy to the Hostinger server on every push to `main`, without manual SSH intervention. Migrations remain manual.

## Architecture

GitHub Actions workflow triggers on push to `main`. It SSHes into the server using a private key stored as a GitHub secret, then runs deploy commands on the server.

```
push to main
    → GitHub Actions runner
        → SSH into u919481528@153.92.10.172 -p 65002
            → git pull origin main
            → composer install (if composer.lock changed)
            → php artisan config:cache
            → php artisan view:cache
            → php artisan route:cache
```

No passwords stored anywhere. GitHub holds the private key; Hostinger holds the matching public key in `~/.ssh/authorized_keys`.

## Server Details

| Field | Value |
|-------|-------|
| Host | 153.92.10.172 |
| Port | 65002 |
| User | u919481528 |
| App path | ~/domains/speedzone.id |
| Composer | /usr/local/bin/composer (global) |

## GitHub Secrets Required

| Secret | Value |
|--------|-------|
| `SSH_PRIVATE_KEY` | Private key (generated during setup) |
| `SSH_HOST` | 153.92.10.172 |
| `SSH_USER` | u919481528 |
| `SSH_PORT` | 65002 |

## Deploy Script (runs on server)

```bash
cd ~/domains/speedzone.id
git pull origin main
if git diff HEAD@{1} HEAD --name-only 2>/dev/null | grep -q composer.lock; then
  composer install --no-dev --optimize-autoloader --no-interaction
fi
php artisan config:cache
php artisan view:cache
php artisan route:cache
```

`composer install` only runs when `composer.lock` changed in the pull — skipped otherwise.

## Migrations

Migrations are **not** run automatically. When a deployment includes new migrations, SSH in manually and run:

```bash
cd ~/domains/speedzone.id
php artisan migrate --force
```

This is intentional — it prevents accidental migration of bad or incomplete migrations.

## Error Handling

- If `git pull` fails (merge conflict, network error), the workflow stops and GitHub marks the run as failed. You receive an email notification. The live site is unaffected.
- No rollback mechanism needed — `git pull` is the first step and the site stays on the previous commit if it fails.

## One-Time Setup Steps

1. Generate SSH key pair locally (ed25519)
2. Add public key to `~/.ssh/authorized_keys` on Hostinger server
3. Add 4 secrets to GitHub repo settings
4. Create `.github/workflows/deploy.yml` in the project

## Out of Scope

- Zero-downtime deploys (maintenance mode during deploy)
- Automatic migrations
- Slack/Discord deploy notifications
- Build step (Vite) — assets are pre-built and committed
