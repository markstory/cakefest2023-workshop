# CakeFest 2023 workshop

The source code for the workshop given at Cakefest 2023

## Branches

This repository has several branches for the various stages of my workshop:

- `master` Contains a basic CakePHP 5 demo.
- `authentication-authorization` Contains a simple application with
  authentication and authorization setup.
- `sudomode-complete` Contains the completed sudo-mode application.
- `webauthn-complete` Contains the completed webauthn/passkeys application.

## Installation

1. Download [Composer](https://getcomposer.org/doc/00-intro.md) or update `composer self-update`.
2. Run `php composer.phar install`.

If Composer is installed globally, run

```bash
composer install
```

You can now start the CakePHP development server with:

```bash
bin/cake server -p 8765
```
Then visit `http://localhost:8765` to see the welcome page.

### mkcert & stunnel

For the webauthn example, the application needs to be behind a TLS webserver.
The cakephp dev server can't do TLS, so I'm using a pair of CLI utilities you
can generate an HTTPs proxy for the cakephp dev server. I found this pretty
simple to use on linux.

Generate certificates for your local machine using `mkcert`

```bash
mkcert localhost
cat localhost.pem localhost-key.pem > localhost-bundle.pem
chmod 0666 *.pem
```

This will generate certificate & key file. Create the bundled certificate
for `stunnel`

Then in one terminal, run: `bin/cake server` and then in another run 

```bash
sudo stunnel3 -f -d 443 -r 8765 -p ./localhost-bundle.pem
```

## Configuration

Read and edit the environment specific `config/app_local.php` and set up the
`'Datasources'` and any other configuration relevant for your application.
Other environment agnostic settings can be changed in `config/app.php`.
