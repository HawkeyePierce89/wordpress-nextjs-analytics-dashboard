# Headless WordPress Content Analytics Dashboard

> A demo project showcasing a headless WordPress setup where WordPress acts as a content backend and a custom Next.js dashboard provides internal teams with a better interface for content discovery, filtering, and analytics.

## Why This Project

Content teams using WordPress need better tools for reviewing content performance, filtering articles, and analyzing publishing patterns. This dashboard demonstrates building a custom internal tool that connects to WordPress via a custom REST API rather than the default WordPress admin — giving teams a faster, purpose-built interface tailored to their workflow.

## Features

- **Overview Dashboard** — KPI cards, publishing trends chart, category distribution, top performing posts, recent activity feed
- **Posts List** — Filterable, sortable data table with search, status/category/author filters, URL-synced state, column visibility toggle, pagination
- **Post Details** — Two-column layout with metadata, performance metrics, SEO preview (Google snippet mock), activity timeline, related posts
- **Edit Metadata** — Form with Zod validation, character counters, dirty state tracking, optimistic updates
- **Analytics** — Publishing trends, draft vs published donut chart, category/author breakdowns, reading time analysis, content health summary

## Tech Stack

| Layer | Technology |
|-------|-----------|
| CMS Backend | WordPress 6.7, PHP 8.3, MySQL 8.0 |
| Custom Fields | ACF (Advanced Custom Fields) Free |
| API | Custom REST endpoints (`/wp-json/dashboard/v1/*`) |
| Infrastructure | Docker, Docker Compose, WP-CLI |
| Frontend | Next.js 15 (App Router), React 19 |
| Language | TypeScript |
| Styling | Tailwind CSS v4 |
| Data Fetching | TanStack Query v5 |
| Tables | TanStack Table v8 |
| Forms | React Hook Form + Zod |
| Charts | Recharts |
| Icons | Lucide React |

## Architecture

The project is a two-app monorepo. All services run in Docker.

```
wordpress-nextjs-analytics-dashboard/
├── docker-compose.yml         # Local dev: db, wordpress, frontend
├── docker-compose.prod.yml   # Production: + nginx with SSL, certbot
├── wordpress/            # WordPress backend
│   ├── Dockerfile        # WordPress 6.7 + PHP 8.3 + WP-CLI
│   ├── plugins/
│   │   └── analytics-dashboard/   # Custom plugin: REST API + ACF fields + activity log
│   └── theme/
│       └── suspended-starter/     # Minimal headless theme (no frontend output)
└── frontend/             # Next.js 15 dashboard
    ├── Dockerfile        # Node.js 20 Alpine
    └── src/
        ├── app/          # App Router pages (dashboard, posts, analytics)
        ├── components/   # Shared UI components
        ├── features/     # Feature-scoped components (posts, analytics)
        ├── hooks/        # Custom React hooks
        ├── lib/          # API client + TanStack Query definitions
        └── types/        # Shared TypeScript types
```

**Data flow:** WordPress stores content → the `analytics-dashboard` plugin exposes custom REST endpoints → the Next.js frontend fetches data via TanStack Query and renders it in the dashboard. WordPress is never accessed directly by end users; the Next.js app is the only interface.

The custom plugin also maintains a `wp_dashboard_activity` table that records post lifecycle events (created, published, edited, etc.), powering the activity timeline on the post detail page.

## API Endpoints

All endpoints are under `/wp-json/dashboard/v1/` and return JSON.

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/dashboard/v1/posts` | Paginated posts with filtering (status, category, author) and sorting |
| GET | `/dashboard/v1/posts/:id` | Post details with activity timeline and related posts |
| GET | `/dashboard/v1/overview` | Aggregated dashboard statistics (KPIs, trends, top posts) |
| GET | `/dashboard/v1/analytics` | Analytics data and content health metrics |
| PATCH | `/dashboard/v1/posts/:id/metadata` | Update post SEO title, meta description, and focus keyword |
| GET | `/dashboard/v1/authors` | List of content authors |
| GET | `/dashboard/v1/categories` | List of categories |

## Run Locally

### Prerequisites

- Docker and Docker Compose

### Quick Start

1. Start all services:
   ```bash
   docker compose up -d
   ```
   Wait ~15 seconds for MySQL to initialise on first run.

2. Install WordPress via WP-CLI (one-time setup):
   ```bash
   docker compose exec wordpress wp core install \
     --url="http://localhost:8088" \
     --title="Analytics Dashboard" \
     --admin_user=admin \
     --admin_password=admin \
     --admin_email=admin@example.com

   docker compose exec wordpress wp rewrite structure '/%postname%/'
   docker compose exec wordpress wp rewrite flush
   ```

3. Install and activate plugins:
   ```bash
   docker compose exec wordpress wp plugin install advanced-custom-fields --activate
   docker compose exec wordpress wp plugin activate analytics-dashboard
   ```

4. Activate the headless theme:
   ```bash
   docker compose exec wordpress wp theme activate suspended-starter
   ```

5. Seed demo data (40 posts, 5 authors, 6 categories, 18 activity events):
   ```bash
   docker compose exec wordpress wp seed generate
   ```

The dashboard will be available at `http://localhost:3000`.
The WordPress REST API is at `http://localhost:8088/wp-json/dashboard/v1/`.

## Production Deployment (VPS)

### Prerequisites

- A VPS with Docker and Docker Compose
- A domain with an A record pointing to the VPS IP

### Setup

1. Clone the repo and create `.env` from the example:
   ```bash
   cp .env.example .env
   # Edit .env — set strong passwords for MYSQL_ROOT_PASSWORD and MYSQL_PASSWORD
   ```

2. Bootstrap SSL certificate. Temporarily replace `nginx/nginx.conf` with an HTTP-only config for the ACME challenge, then:
   ```bash
   docker compose -f docker-compose.prod.yml up -d nginx
   docker compose -f docker-compose.prod.yml run --rm certbot certonly \
     --webroot -w /var/www/certbot \
     -d wordpress-nextjs-analytics-dashboard.karmanov.ws
   ```
   Restore the full `nginx/nginx.conf` and restart nginx.

3. Start all services:
   ```bash
   docker compose -f docker-compose.prod.yml up -d --build
   ```

4. Install WordPress (one-time):
   ```bash
   docker compose -f docker-compose.prod.yml exec wordpress wp core install \
     --url="https://wordpress-nextjs-analytics-dashboard.karmanov.ws" \
     --title="Analytics Dashboard" \
     --admin_user=admin \
     --admin_password=<strong-password> \
     --admin_email=admin@example.com

   docker compose -f docker-compose.prod.yml exec wordpress wp rewrite structure '/%postname%/'
   docker compose -f docker-compose.prod.yml exec wordpress wp rewrite flush
   docker compose -f docker-compose.prod.yml exec wordpress wp plugin install advanced-custom-fields --activate
   docker compose -f docker-compose.prod.yml exec wordpress wp plugin activate analytics-dashboard
   docker compose -f docker-compose.prod.yml exec wordpress wp theme activate suspended-starter
   docker compose -f docker-compose.prod.yml exec wordpress wp seed generate
   ```

### How it works

Nginx acts as a reverse proxy on ports 80/443:
- `/` → Next.js frontend (production build via `standalone` output)
- `/wp-json/*` → WordPress REST API
- `/wp-content/uploads/*` → WordPress media

All API calls from the browser go to the same origin, so no CORS is needed. The frontend is built with `NEXT_PUBLIC_WP_API_URL=/wp-json/dashboard/v1` (relative path). Certbot handles automatic SSL renewal.

## What This Project Demonstrates

- **Headless WordPress architecture** — WordPress as a pure content API with a fully decoupled frontend
- **Custom WordPress REST API design** — purpose-built endpoints with filtering, sorting, and aggregation rather than relying on the default WP REST API
- **TypeScript-first frontend development** — strict types shared across API responses, component props, and form validation schemas
- **Complex data table with TanStack Table** — column visibility toggles, multi-column sorting, server-side pagination, and URL-synced filter state using `useSearchParams`
- **Form handling with React Hook Form + Zod validation** — character counters, dirty state detection, and optimistic updates with rollback on error
- **Data visualization with Recharts** — area charts for publishing trends, donut chart for post status distribution, bar charts for category and author breakdowns
- **URL-synced filter state** — all Posts List filters (search, status, category, author, page) are reflected in the URL so links are shareable and the browser back button works correctly
- **Clean component architecture** — shared UI primitives in `components/`, feature-scoped components in `features/`, and page composition in `app/`
- **Fully Dockerised** — single `docker compose up` starts all three services (MySQL, WordPress, Next.js)
