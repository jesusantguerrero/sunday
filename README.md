# IC-Daily (codename: Sunday) 
Monolith app using laravel 8, jetstream, intertia and vue.

ICDaily is a productivity web app to help you keep your tasks, track your time, using pomodoro inspired on Monday.


![IC Daily](/resources/js/documentation/assets/images/img1.PNG)

## The goals

* **EASY and Fast** Provide an easy way to keep your taks assing priorities and track your time using promodoro.

* **One to rule them all** Promodoro timer, Toggl tracker, Heisenhower matrix and Monday Automations

> This will be like Monday but more chulo
 
## Features

* Agenda
* Time Tracker
* Pomodoro
* Task Board
* Automations 
* Gmail and Calendar integration
* Developer focused
* List / Board layout
* And more...

![ICNOTE](./resources/js/documentation/assets/images/img3.PNG)
> You'll use real checkboxes not images

## Captures
![ICNOTE](./resources/js/documentation/assets/images/img2.PNG)

## Installation

### Prerequisites

| Prerequisite                                          | Version     |
| ------------------------------------------------------| ----------  |
| [Docker*]()                                           |    --       |
| [Node.js](http://nodejs.org)                          | `~ ^14.18.0`|
| npm (comes with Node) or yarn (used)                  | `~ ^5`      |
| [PHP]                                                 | `~ ^8.1.2`  |
| [Cloud Platform Project (with Gmail API)**](https://developers.google.com/gmail/api/quickstart/js)                                |    --                                                 |             |
| PHP extension ext-mailparse**                         |      --     |

`* Docker is optional and have all the dependencies you need ext-mailparse included`

`** those requirements are optional if you want to run de project without docker and use gmail and calendar integration`

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
npm run setup

# the setup is just needed the first time run 
npm run serve
```



Setup Without Docker
```bash
# composer install
composer install --ignore-platform-reqs
# generate key
php artisan key:generate
# run migrations
php artisan migrate
# run seeds
php artisan db:seed
```

Frontend development
```bash
# install npm packages
npm install
# development
npm run watch
```

## Related projects
- [Zen](https://zenboard.app/)

## License
[MIT license](https://opensource.org/licenses/MIT).
