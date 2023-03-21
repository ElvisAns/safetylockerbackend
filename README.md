# Safety Locker

The Safety Locker project is a digital plateform that will help
help Electrical companies maintains their technician safety.

It's main philosophy is the IoT industry in mind.

We are consuming the api in the following vuejs app : https://safety-locker.vercel.app

The api service is deployed to fly and is running at https://safetylocker.fly.dev/ 


## Getting started

This api itself doesnt really makes sens.

To make the full project work we need a web application and an electronics hardware.

Remember at the end, the full project aims is to ensure safety during electrical maintenance at a given site.

The scenario is as follow :

- A technician needs to work in a given area.

- He gather to the switch box and using the application he switch to maintance by using a security Code

- Next he is safe to go, no one will switch on a given line unless he declared to be done with the maintance (a that happen again in the web app)


If you are good with Arduino programming, go ahead and look at the following [Embeded System Code](https://github.com/ElvisAns/SafetyLockerEmbedded) and then next you can run
the web application whose repository is also at this [link](https://github.com/ElvisAns/safetyLockerFrontend)


## Run Locally ( the api service)

Clone the project

```bash
  git clone https://link-to-project
```

Go to the project directory

```bash
  cd my-project
```

Install dependencies

```bash
  composer install
```

Sync your database

Before going ahead. rename the `.env.example` file to `.env` and change your database credentials. Then
run the following command to generate your application key (as recommanded by laravel). The application key is used as secret key for any process that requires key like encrypting etc...

```bash
    php artisan key:generate
```

Then sync DB tables

```bash
    php artisan migrate
```


Start the server

```bash
  php artisan serve
```


## API Reference

Please go explore [this documentation](https://documenter.getpostman.com/view/14572798/2s8YzXvffz) for more informations about individual requests
## 🔗 Links
[![portfolio](https://img.shields.io/badge/my_portfolio-000?style=for-the-badge&logo=ko-fi&logoColor=white)](https://elvisansima.netlify.app/)
[![linkedin](https://img.shields.io/badge/linkedin-0A66C2?style=for-the-badge&logo=linkedin&logoColor=white)](https://www.linkedin.com/in/cibalinda-elvis)

