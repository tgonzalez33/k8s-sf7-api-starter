# sf7-rest-api-starter

Rest API starter with Symfony 7 witch include four dockers containers :

- MySQL
- PHPMyAdmin
- Redis
- SF7 API

## Start

Launch containers

```bash
make start
```

Attach a shell to sf7-api and do the migrations

```bash
php bin/console doctrine:migrations:migrate
```

Import project collection in POSTMAN (docs/SF7.postman_collection.json)

Open your web browser and go to [SF7 default homepage](http://localhost:8182) and [PHPMyAdmin](http://localhost:8080)

## [!CAUTION]

Please note this is a mini API demo, the code is neither **optimized** nor **too secure** but it will still do the job!

In order to make it as easy as possible to get started, I versioned the .env file and thus make it "turnkey": **never do this in production** of course :D

## Improvements

- Add services instead of coding into Controller
- Add more validation and security check on requested datas
- Automatic migrations on container launch
- Add redis cache
- Add tests & CI tests
- Do some cleaning
