# Ancora - just a PHP way to construct web apps.

This is a tool built by [Maurício Fauth](https://github.com/mauriciofauth) and [Thiago Nardi](https://github.com/thnardi) to develop web apps. We have working on this since 2017 in [Farol 360](https://farol360.com.br) jobs.

## Dependences

 - php 7+;
 - Composer;
 - Jquery;
 - Bootstrap 3;
 - [Tim Creative Material Kit](https://github.com/timcreative).


## Key Concepts and references.

This section's objective is to guide anyone ho wants to work with ancora, explain his basic usage.

The core of application are built with [Slim 3 Framework](https://www.slimframework.com). To understand the code you must focus on slim's *routes* and *dependecies* management and learn about his [middleware](https://www.slimframework.com/docs/v3/concepts/middleware.html) concept.

All the solution is implemented on *app/* folder. The core files are **dependencies.php**, **middleware.php**, **routes.php** and **settings.php**. All this files contains declarations of elements we work on. The code in it's default have declarations like the basic permissions controllers, flash messages, loggers with monolog and Twig engine for render the view layer. You can use this implementations as template to adding new declarations as it needs.

The MVC concept are strongly implemented on this solution. Let's take an overview by MVC layers.

### Model layer

The model is implemented on *src/model/* folder. It work with 2 classes: a plain one refering exactly the table in db and a same name ended by **Model** pair class that extends *src/Model.php* with have all sql functions.

The way to add a new class is:
1) Supose you have an example table in you db. Create the **Example.php** in *src/model* (you can take any other class already implemented as template);
2) Create a **ExampleModel.php** in same directory;
3) Insert a function on *src/model/EntityFactory.php* with an array $data as parameter and return a new Example instance. (this is a Factory design pattern implementation);
4) Add the **exampleModel** class as atribute in controller you want to work. You will need to pass it as parameter in contructor and update *src/dependencies* file.

### View

In View we use [Twig](https://twig.symfony.com/) engine.

- Functions used in layout.twig like "get_name()" or "get_email()" are defined in src/twig/AuthExtension.php file;


### Controller

All controller classes extends *src/Controller.php* and is stored on *src/controller* folder. You can pass dependencies (e.g. models) in class constructor declaration, setting his attributes.

Functions that render views need a strict parameter signature. You can check out default controllers as a guide and learn on [Slim 3 Docs](https://www.slimframework.com/docs/). All you need to know by now is the Request, and Response parameters are obrigatory. You can use an third optional array parameter called $args to get variable URL parts.
