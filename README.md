# Sistema de Eventos ISEPE RONDON

**Client**:         Isepe Rondon;

## Features list

- [ ] Manage Users:
    - Users;
    - Roles;

- [ ] Manage Events:
    - [x] Editable Agree terms - with template;
    - [x] Subscription limit;
    - [x] Img default;
    - [x] CRUD in Admin;

- [x] Manage Events Type:
    - [x] Agree Terms template;
    - [x] CRUD in Admin;

- [ ] Manage Trash;
    - [x] Event list;
    - [x] Event Recover;
    - [ ] Event Remove;


# Ancora - just a PHP way to construct web apps.

This is a tool built by [Maurício Fauth](https://github.com/mauriciofauth) and [Thiago Nardi](https://github.com/thnardi) to develop web apps. We have working on this since 2017 in [Farol 360](https://farol360.com.br) jobs.

## Getting Started

The core of application are built with [Slim 3 Framework](https://www.slimframework.com). To understand the code you must focus on slim's *routes* and *dependecies* management and learn about his [middleware](https://www.slimframework.com/docs/v3/concepts/middleware.html) concept.

We create a [guide](https://github.com/thnardi/ancora/blob/master/GUIDE.md) to help anyone who may be interested.

### Prerequisites

 - php 7+;
 - Composer;
 - Jquery 3;
 - Jquery Validator;
 - Jquery Mask;
 - Bootstrap 3;
 - [Tim Creative Material Kit](https://github.com/creativetimofficial/material-dashboard).


### Installing

1) Clone or download the repo files
```
git clone https://github.com/thnardi/ancora.git
```
2) run composer on project folder to install dependencies
```
composer install
```
3) copy `.env.edit` file to `.env` to edit database infos
```
cp .env.edit .env
```
4) create your local database and modify `.env` to insert your db infos

5) run migrate command on project folder to run `db/migrations` initial database configurations
```
vendor/bin/phinx migrate
```
6) run your php local server. apoint public_html as the root of your server
```
php -S localhost:8080 -t public_html/
```
## Authors

- **Maurício Fauth** - *Initial work*
- **Thiago Nardi**

## License

This project is licensed under the MIT License - see the [LICENSE](https://github.com/thnardi/ancora/blob/master/LICENSE) file for details.


