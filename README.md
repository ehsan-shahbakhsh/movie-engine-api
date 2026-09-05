<h1 align="center">
🎬 High-Performance Movie Download API
</h1>

<div align="center">
<img src="https://img.shields.io/badge/Laravel-13.x-FF2D20?style=for-the-badge&logo=laravel" alt="Laravel">
<img src="https://img.shields.io/badge/PHP-8.x-777BB4?style=for-the-badge&logo=php" alt="PHP">
<img src="https://img.shields.io/badge/Docker-2CA5E0?style=for-the-badge&logo=docker&logoColor=white" alt="Docker">
<img src="https://img.shields.io/badge/Filament-FDAE4B?style=for-the-badge&logo=filament&logoColor=black" alt="Filament">
<img src="https://img.shields.io/badge/MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL">
<img src="https://img.shields.io/badge/OpenSwoole-00A6DA?style=for-the-badge&logo=openswoole" alt="OpenSwoole">
<img src="https://img.shields.io/badge/Redis-DC382D?style=for-the-badge&logo=redis&logoColor=white" alt="Redis">
</div>

[//]: # ([🇮🇷 نسخه فارسی مستندات]&#40;README-fa.md&#41;)

A blazing fast, headless backend infrastructure designed specifically for managing and delivering direct movie
downloads, powered by Laravel Octane, MySQL, Redis, and Docker.

## ⚙️ Core Features

* **High-Performance Engine:** Powered by Laravel Octane and OpenSwoole to handle concurrent requests with minimal overhead and high throughput.
* **Action/Query Architecture:** Strict separation of business logic (Actions) and data retrieval (Queries) to maintain exceptionally slim controllers.
* **Instant Search:** Integrated with Meilisearch via Laravel Scout for blazing-fast, typo-tolerant media discovery.
* **Robust Admin Panel:** Built seamlessly with Filament PHP for rapid, scalable, and efficient data management.

[//]: # (## 🚀 Getting Started)

[//]: # (The most convenient way to run this project is via Docker. The provided containerized environment ensures the Octane)

[//]: # (server, Redis, and MySQL run seamlessly.)

[//]: # ()
[//]: # (### Installation via Docker)

[//]: # ()
[//]: # (1. Clone the repository and navigate to the root directory:)

[//]: # (```bash)

[//]: # (git clone https://github.com/ehsan-shahbakhsh/movie-engine-api)

[//]: # ()
[//]: # (cd movie-engine-api)

[//]: # (```)

[//]: # ()
[//]: # (2. Copy the environment variables file:)

[//]: # (```bash)

[//]: # (cp .env.example .env)

[//]: # (```)

[//]: # ()
[//]: # (3. Spin up the containers using Docker Compose:)

[//]: # (```bash)

[//]: # (docker compose up -d --build)

[//]: # (```)

[//]: # ()
[//]: # (4. Install PHP dependencies and generate the application key inside the container:)

[//]: # (```bash)

[//]: # (docker compose exec app composer install)

[//]: # (docker compose exec app php artisan key:generate)

[//]: # (```)

[//]: # ()
[//]: # (5. Run database migrations and seed the database with mock movie data:)

[//]: # (```bash)

[//]: # (docker compose exec app php artisan migrate --seed)

[//]: # (```)

[//]: # ()
[//]: # (6. Link the storage directory to make uploaded media files publicly accessible:)

[//]: # (```bash)

[//]: # (docker compose exec app php artisan storage:link)

[//]: # (```)

[//]: # ()
[//]: # (The Octane server will now be listening and serving requests at http://localhost:8000.)

## 📡 API Documentation & Standard Responses
To ensure predictable integration for any client, all API endpoints strictly adhere to a standardized JSON response
structure.

Successful Response Example
When fetching a movie's direct download links:

```JSON
{
    "success": true,
    "code": 200,
    "message": "Success",
    "data": {
        "id": 121,
        "title": "میان‌ستاره‌ای",
        "original_title": "Interstellar",
        "slug": "interstellar-2014",
        "poster": {
            "original": "http://localhost:8000/storage/1/01M1QW9E40W3JQRPB7NAYP22BC.webp",
            "thumb": "http://localhost:8000/storage/1/conversions/01M1QW9E40W3JQRPB7NAYP22BC-thumb.webp",
            "medium": "http://localhost:8000/storage/1/conversions/01M1QW9E40W3JQRPB7NAYP22BC-medium.webp"
        },
        "synopsis": null,
        "release_year": 2014,
        "release_date": "2014-11-07T00:00:00.000000Z",
        "duration_minutes": 169,
        "status": "published",
        "original_language": "English",
        "languages": [
            "English"
        ],
        "age_rating": "PG-13",
        "countries": [
            "United Kingdom of Great Britain and Northern Ireland (the)",
            "United States of America (the)"
        ],
        "genres": [
            {
                "id": 82,
                "name": "علمی تخیلی",
                "slug": "sci-fi"
            },
            {
                "id": 83,
                "name": "ماجراجویی",
                "slug": "adventure"
            },
            {
                "id": 84,
                "name": "درام",
                "slug": "drama"
            }
        ],
        "persons": [],
        "videos": [],
        "backdrop": null,
        "logo": null,
        "gallery": [],
        "download_groups": [
            {
                "id": 5,
                "title": "دوبله فارسی",
                "download_links": [
                    {
                        "id": 5,
                        "quality": "1080p",
                        "encoder": null,
                        "codec": null,
                        "size_in_bytes": 5251990,
                        "human_readable_size": "5.01 MB",
                        "video": "http://localhost:8000/storage/2/01M1QX0AEGX54DG8TXN3BKMD73.mp4"
                    },
                    {
                        "id": 6,
                        "quality": "720p",
                        "encoder": null,
                        "codec": null,
                        "size_in_bytes": 5251990,
                        "human_readable_size": "5.01 MB",
                        "video": "http://localhost:8000/storage/3/01M1QX0AG7K5RT9Z825DVTABCM.mp4"
                    }
                ]
            }
        ],
        "likes_count": 0,
        "dislikes_count": 0,
        "user_reaction": null
    },
    "meta": null,
    "errors": null,
    "error_code": null
}
```

Error Response Example

```JSON
{
    "success": false,
    "code": 401,
    "message": "لطفاً ابتدا وارد حساب کاربری شوید.",
    "data": null,
    "meta": null,
    "errors": null,
    "error_code": null
}
```

[//]: # (&#40;Note: A complete Postman Collection / Swagger documentation is included in the /docs directory&#41;.)

## 🧪 Testing

Quality assurance is maintained through comprehensive automated testing. To run the Feature and Unit tests inside the
container, simply execute:

```bash
docker compose exec app php artisan test
```

## 📜 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
