Used technologies and libraries
============

PHP (Symfony 5.1), Docker

league/tactician-bundle: Tactician is a command bus library. 

The term is mostly used when we combine the Command pattern with a service layer. Its job is to take a Command object (which describes what the user wants to do) and match it to a Handler (which executes it).

----------------------------------

lexik/jwt-authentication-bundle: This bundle provides JWT (Json Web Token) authentication for your Symfony API.

----------------------------------

liip/imagine-bundle: This bundle provides an image manipulation abstraction toolkit


Installation
============

Make sure Docker is installed globally

Applications that use Symfony Flex
----------------------------------

Open a command console, enter your project directory and execute:

```console
$ make init
```

You can login to api with username 'test@mail.com' and password '123456'

### Link to deployed app

http://367514-cv87846.tmweb.ru

### phpunit

To run tests:

```console
$ make app-test
```

