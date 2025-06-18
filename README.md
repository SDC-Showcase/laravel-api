# Laravel Read-Only REST API

This is a Laravel-based application designed to provide a read-only REST API. Below is an overview of the application's structure and key components.

Please refer to https://documenter.getpostman.com/view/3795325/2sAYkGLzF2
for a full description and demonstration of using the live API (https://ehaloph.uc.pt/)


---

## Table of Contents

- [Project Structure](#project-structure)
- [Key Directories and Files](#key-directories-and-files)
- [API Endpoints](#api-endpoints)
- [Models](#models)
- [Controllers](#controllers)
- [Resources](#resources)
- [Filters](#filters)
- [Policies](#policies)
- [Configuration](#configuration)

---

## Project Structure

The project follows the standard Laravel directory structure with some additional organization for API-specific functionality. Below is a high-level overview of the directory structure:


Filtering to most relevant information

. ├── app/ │ ├── Http/ │ │ ├── Controllers/ │ │ │ └── Api/ │ │ │ └── V1/ │ │ ├── Filters/ │ │ ├── Resources/ │ │ └── Middleware/ │ ├── Models/ │ │ └── Api/ │ │ └── V1/ │ ├── Policies/ │ └── Traits/ ├── bootstrap/ ├── config/ ├── database/ ├── public/ ├── resources/ ├── routes/ ├── storage/ ├── tests/ └── vendor/

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

The API provides the following endpoints:

### Plants
- `GET /api/v1/plants`: Retrieve a paginated list of plants.
- `GET /api/v1/plants/{id}`: Retrieve details of a specific plant.

### Field Values
- `GET /api/v1/fieldvalues/{fieldName}`: Retrieve values for a specific field.

### References
- `GET /api/v1/references`: Retrieve a paginated list of references.
- `GET /api/v1/references/{id}`: Retrieve details of a specific reference.

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

## Policies

Policies define authorization logic for API resources. Key policies include:
- **`PlantPolicy`**: Authorization logic for plant-related actions.

---

## Configuration

The application uses environment variables and configuration files to manage settings. Key configuration files include:
- **`.env`**: Contains environment-specific settings such as database credentials and API keys.
- **`config/app.php`**: General application settings.
- **`config/database.php`**: Database connection settings.

---

## Testing

Feature tests are located in the `tests/Feature/` directory. These tests ensure the API endpoints function as expected.

---

## Notes

- This application is designed as a **read-only API**. No endpoints modify the database.
- The application uses Laravel's built-in features for routing, middleware, and Eloquent