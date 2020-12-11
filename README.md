# IC-Daily (codename: Sunday) 
Monolith app using laravel 8, jetstream, intertia and vue.

## Installation

### Prerequisites

| Prerequisite                                          | Version |
| ------------------------------------------------------| ------- |
| [Docker*]()                                           |    --   |
| [Node.js](http://nodejs.org)                          | `~ ^8`  |
| npm (comes with Node) or yarn (used)                  | `~ ^5`  |
| [Cloud Platform Project (with Gmail API)*](https://developers.google.com/gmail/api/quickstart/js)                                |    --   |

```shell
node -v
```

#### Cloning the repo

1. Open a Terminal in your projects directory 
2. Clone this repo

```shell
$ git clone https://github.com/jesusantguerrero/sunday.git

```
### Setup
```bash
# Install NPM dependencies
npm install 
# or If you like yarn
yarn

```

copy .env.example to .env:

```bash
# Remember place your laravel-app info there:
cp .env.example .env

```

Setup With Docker
```bash
#build dockerfile for webapp
docker-compose build web

#start docker enviroment
npm run serve

```

Setup Without Docker
```bash
# composer install
composer install
# generate key
php artisan key:generate
# run migrations
php artisan migrate
```

Frontend development
```bash
# development
npm run watch
```

## License
The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
