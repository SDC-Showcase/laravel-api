# Laravel Read-Only REST API

This is a Laravel-based application designed to provide a read-only REST API. Below is an overview of the application's structure and key components.

### Overview

This API provides read-only access to plant data, field values, and references. All endpoints return JSON responses and support filtering and pagination where applicable. No authentication is required.

Please refer to https://documenter.getpostman.com/view/3795325/2sAYkGLzF2
for a full description and demonstration of the endpoints using the live API (https://ehaloph.uc.pt/)


### Rate Limiting

The API implements rate limiting to ensure fair usage and prevent abuse. By default, the API allows up to **120 requests per minute** per client. If the limit is exceeded, the client will receive a `429 Too Many Requests` response.

#### Key Details:
- **Limit**: 120 requests per minute.
- **Response on Exceeding Limit**: HTTP status code `429 Too Many Requests`.
- **Retry-After Header**: The response includes a `Retry-After` header indicating the time (in seconds) the client should wait before making further requests.

This rate limiting is applied globally to all API routes using the `throttle` middleware.

---

### Base URL

```
https://yourdomain.com/api/v1/

A live example is available (without registration) at https://ehaloph.uc.pt/api/v1/

```

---


### Usage Tips

- Use query parameters to filter and paginate results.
- All endpoints are read-only; no data modification is possible.
- Refer to the [Postman documentation](https://documenter.getpostman.com/view/3795325/2sAYkGLzF2) for detailed examples and schema.

---

## Table of Contents

- [Project Structure](#project-structure)
- [Key Directories and Files](#key-directories-and-files)
- [API Endpoints](#api-endpoints)
- [Models](#models)
- [Controllers](#controllers)
- [Resources](#resources)
- [Filters](#filters)
- [Configuration](#configuration)

---

## Project Structure

The project follows the standard Laravel directory structure with some additional organization for API-specific functionality. Below is a high-level overview of the directory structure:

```
.   ├── app/ │
        ├── Http/ │
            ├── Controllers/
            │ └── Api/
                │ └── V1/
            ├── Filters/
            ├── Resources/
            │ └── V1/
        ├── Models/
        │ └── Api/
            │ └── V1/
        │── Traits/
    ├── bootstrap/
    ├── config/
    ├── database/
    ├── public/
    ├── resources/
    ├── routes/
    ├── storage/
    ├── tests/
    ├── vendor/
```

---

## Key Directories and Files

### `app/Http/Controllers/Api/V1/`
This directory contains the API controllers for version 1 of the API. Each controller handles specific resources:
- **`PlantController.php`**: Handles requests related to plants.
- **`FieldValuesController.php`**: Handles requests for field values.
- **`ReferenceController.php`**: Handles requests for references.

### `app/Models/Api/V1/`
This directory contains the Eloquent models for the API. These models represent the database tables and define relationships:
- **`ApiPlant.php`**: Represents plant records.
- **`ApiField.php`**: Represents fields associated with plants.
- **`ApiValue.php`**: Represents values associated with fields.
- **`ApiReference.php`**: Represents references linked to plants.
- **`ApiImage.php`**: Represents images associated with plants.

### `app/Http/Resources/V1/`
This directory contains API resources that transform models into JSON responses:
- **`PlantResource.php`**: Formats plant data for API responses.
- **`ReferenceResource.php`**: Formats reference data for API responses.

### `app/Http/Filters/V1/`
This directory contains query filters for handling API request parameters:
- **`PlantFilter.php`**: Filters plant data based on query parameters.
- **`QueryFilter.php`**: Base class for reusable query filtering logic.
- **`ReferenceFilter.php`**: Filters reference data based on query parameters.

### `routes/api_v1.php`
Defines the API routes for version 1 of the application. Key routes include:
- `/plants`: Endpoints for managing plants.
- `/fieldvalues/{fieldName}`: Endpoint for retrieving field values.
- `/references`: Endpoints for managing references.

### `config/`
Contains configuration files for the application. Notable files include:
- **`app.php`**: General application configuration.
- **`database.php`**: Database connection settings.

### `tests/Feature/`
Contains feature tests for the API:
- **`PlantApiTest.php`**: Tests the functionality of the plant-related API endpoints.

---

## API Endpoints

Please refer to https://documenter.getpostman.com/view/3795325/2sAYkGLzF2 for detailed API documentation and examples.

---

## Models

The application uses Eloquent models to interact with the database. Key models include:
- **`ApiPlant`**: Represents plant records and defines relationships with fields, values, and images.
- **`ApiField`**: Represents fields associated with plants.
- **`ApiValue`**: Represents values associated with fields.
- **`ApiReference`**: Represents references linked to plants.
- **`ApiImage`**: Represents images associated with plants.

---

## Controllers

Controllers handle the logic for processing API requests and returning responses. Key controllers include:
- **`PlantController`**: Handles plant-related requests.
- **`FieldValuesController`**: Handles requests for field values.
- **`ReferenceController`**: Handles reference-related requests.

---

## Resources

Resources are used to transform models into JSON responses. Key resources include:
- **`PlantResource`**: Formats plant data for API responses.
- **`ReferenceResource`**: Formats reference data for API responses.

---

## Filters

Filters are used to apply query parameters to database queries. Key filters include:
- **`PlantFilter`**: Filters plant data based on query parameters.
- **`ReferenceFilter`**: Filters reference data based on query parameters.


---

## Configuration

The application uses environment variables and configuration files to manage settings. Key configuration files include:
- **`.env`**: Contains environment-specific settings such as database credentials and API keys.
- **`config/app.php`**: General application settings.
- **`config/database.php`**: Database connection settings.

---

## Testing

Feature tests are located in the `tests/Feature/` directory. These tests ensure the API endpoints function as expected.

   php artisan test --parallel
   (leverages multiple processes to run tests in parallel - brianium/paratest)

---

## Notes

- This application is designed as a **read-only API**. No endpoints modify the database.
- The application uses Laravel's built-in features for routing, middleware, and Eloquent