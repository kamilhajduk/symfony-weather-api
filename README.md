# Symfony WeatherAPI Proxy ⛅
- Framework: **Symfony 7.1**
- Webserver: **Nginx 1.27**
- PHP: **PHP-FPM 8.2**

## Install

1. Place your API key into ```.env.local``` file. (for security reasons)
```
API_KEY=...
```

2. Run your local server using Docker environment.
```
docker-compose up --build -d
```

## Usage

Use **Postman** and import ```WeatherAPI.postman_collection.json``` collection files.

Example:

```
POST /average_temp

{"date":"2024-07-01","cities":["Poznan","Gdansk","Warszawa"]}
```

*Response*

```
{"average_temp":18.7}
```