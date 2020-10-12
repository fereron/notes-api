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

Make sure Docker is installed globally.

Open a command console, enter your project directory and execute:

```console
$ make init
```

#####Generate the SSH keys:
```console
$ mkdir -p config/jwt
$ openssl genpkey -out config/jwt/private.pem -aes256 -algorithm rsa -pkeyopt rsa_keygen_bits:4096
$ openssl pkey -in config/jwt/private.pem -out config/jwt/public.pem -pubout
```

And write secret key to JWT_PASSPHRASE in .env file

Now you can login to api sending post request to "http://localhost:8080/api/login_check" with credentials:

```json
{
  "username": "test@test.com",
  "password": "123456"
}
```

### Link to deployed app

http://367514-cv87846.tmweb.ru

### phpunit

To run tests:

```console
$ make app-test
```

