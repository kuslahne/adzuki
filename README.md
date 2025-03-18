## Skeleton application for SimpleMVC

[![Build status](https://github.com/simplemvc/skeleton/workflows/PHP%20test/badge.svg)](https://github.com/simplemvc/skeleton/actions)

This is a skeleton web application for [SimpleMVC](https://github.com/simplemvc/framework) framework.
## Quickstart

You can install the skeleton application using the following command:

```
composer create-project simplemvc/skeleton
```

This will create a `skeleton` folder containing a basic web application.
You can execute the application using the PHP internal web server, as follows:

```
composer run-script start
```

The application will be executed at [http://localhost:8080](http://localhost:8080).

This skeleton uses [PHP-DI](https://php-di.org/) as DI container and [Plates](https://platesphp.com/)
as template engine.

## Documentation

Follow this guide:
[https://github.com/simplemvc/skeleton](https://github.com/simplemvc/skeleton)

## TODOS

- Full theme work on frontend
- Move backend template to Handlebars [x]
- Pagination support [x]
- Post view detail
- Sidebar widget post by Months
- Add category on post [x]
- Add tag on post
- Add meta tag canonical
- Add slug on post [x]
- Add search on frontend


This software is released under the [MIT](/LICENSE) license.
