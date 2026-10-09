# UAAR Portal

Admin backend and API for the public website.

The portal is a decoupled system: a **Laravel 12** application manages all content and exposes it through a REST API, and a **WordPress (Elementor)** site consumes that API through custom plugins to render the public pages.

| Part | URL | Role |
|------|-----|------|
| Laravel admin + REST API | https://uaarportal.uaar.edu.pk | Content management, authentication, API |
| WordPress frontend | https://www.uaar.edu.pk (staged earlier at new.uaar.edu.pk) | Public website |

---

## Tech Stack

- **Backend:** Laravel 12, PHP, MySQL
- **Admin UI:** Blade, Bootstrap 5, laravel/breeze
- **Auth & permissions:** spatie/laravel-permission
- **Images:** intervention/image
- **Sanitising input:** mews/purifier
- **URL obfuscation:** vinkla/hashids (v13.0.0), salt set via `HASHIDS_SALT`
- **Frontend:** WordPress + Elementor, custom plugins
- **Local environment:** XAMPP, Cursor IDE, GitHub
- **Hosting:** cPanel shared hosting

---

## Architecture

```
 Admin users ──► Laravel admin panel (Blade / Bootstrap 5)
                        │
                        ▼
                 MySQL database
                        │
                 REST API  /api/v1/...
                        │
                        ▼
        WordPress plugins (wp_remote_get, server-side)
                        │
                        ▼
              Public pages (Elementor + shortcodes)
```

- The API is versioned under `/api/v1/`; CORS is configured for the WordPress origin.
- WordPress plugins fetch data **server-side** with `wp_remote_get` (better for SEO than client-side JS) and cache responses with transients.

---

## Roadmap / Modules

The admin panel is built in four phases.

| Phase | Scope |
|-------|-------|
| 1. Foundation | Authentication, roles, departments, faculties |
| 2. Core Content | Faculty, staff, news, gallery |
| 3. Academic | Datesheets, merit lists, tenders |
| 4. Application Forms | Jobs, internet passwords, alumni |

## Maintainer

Software Developer, UAAR.
