Intelligence Query Engine API

Overview

This project is a backend API built for Insighta Labs to provide demographic intelligence through advanced filtering, sorting, pagination, and natural language querying.

The system allows clients (marketing teams, product teams, analysts) to efficiently query profile data using structured query parameters or plain English.


Base URL

```
https://your-app-url.com/api
```

Features

1. Advanced filtering (multiple conditions)
2. Sorting (age, created_at, gender_probability)
3. Pagination (page & limit)
4. Natural language query parsing
5. Idempotent data seeding (no duplicates)

Database Structure

The `profiles` table includes:

* id (UUID)
* name (unique)
* gender
* gender_probability
* age
* age_group
* country_id
* country_name
* country_probability
* created_at (UTC timestamp)



Endpoint 1: Get All Profiles

GET `/profiles`

Query Parameters

| Parameter               | Description                             |
| ----------------------- | --------------------------------------- |
| gender                  | male / female                           |
| age_group               | child / teenager / adult / senior       |
| country_id              | ISO country code (e.g., NG)             |
| min_age                 | Minimum age                             |
| max_age                 | Maximum age                             |
| min_gender_probability  | Minimum gender confidence               |
| min_country_probability | Minimum country confidence              |
| sort_by                 | age / created_at / gender_probability   |
| order                   | asc / desc                              |
| page                    | Page number (default: 1)                |
| limit                   | Results per page (default: 10, max: 50) |

Example

```
/api/profiles?gender=male&country_id=NG&min_age=25&sort_by=age&order=desc
```

---

Endpoint 2: Natural Language Search

GET `/profiles/search`

Query Parameter

| Parameter | Description            |
| --------- | ---------------------- |
| q         | Natural language query |

Example

```
/api/profiles/search?q=young males from nigeria
```


Natural Language Parsing Approach

This system uses a **rule-based parser** (no AI or LLM).

The query string is converted to lowercase and scanned for predefined keywords using string matching and regular expressions.

Supported Keywords and Mappings

| Keyword  | Mapping               |
| -------- | --------------------- |
| male     | gender = male         |
| female   | gender = female       |
| young    | age between 16 and 24 |
| above X  | age >= X              |
| adult    | age_group = adult     |
| teenager | age_group = teenager  |
| nigeria  | country_id = NG       |
| kenya    | country_id = KE       |
| angola   | country_id = AO       |

Parsing Logic

1. Multiple conditions are combined using AND logic
2. If both "male" and "female" are present, no gender filter is applied
3. Regular expressions are used to extract numeric values (e.g., "above 30")
4. A flag (`interpreted`) ensures that only meaningful queries are processed

If no valid keyword is found:

```
{
  "status": "error",
  "message": "Unable to interpret query"
}
```

Limitations

* Only supports predefined keywords
* Limited country recognition (NG, KE, AO)
* Cannot handle spelling mistakes
* Does not support complex sentence structures
* No synonym recognition (e.g., "guys", "ladies")
* Only supports "above X" for numeric filtering (not "below" or ranges)


Data Seeding

Data is loaded from:

```
database/data/profiles.json
```

Seeding uses `updateOrInsert` to prevent duplicate records.

Run:

```
php artisan db:seed
```


Error Handling

All errors follow this structure:

```
{
  "status": "error",
  "message": "<error message>"
}
```

Error Types

* 400: Missing parameter
* 422: Invalid query parameters
* 404: Profile not found


CORS

CORS is enabled to allow access from any origin:

```
Access-Control-Allow-Origin: *
```


Tech Stack

* Laravel (PHP)
* MySQL
* RESTful API design


Notes

* All timestamps are in UTC (ISO 8601)
* Pagination is optimized to avoid full-table scans
* Queries are dynamically built using Eloquent


Author

Fiyinfoluwa
