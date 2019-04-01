# FastNetMon WebUI

Fastnetmon describes itself as follows:

> FastNetMon is a very high performance DDoS detector built on top of multiple packet capture engines: NetFlow, IPFIX, sFlow and SPAN/port mirror. 
> It could detect malicious traffic in your network and immediately block it with BGP blackhole or BGP flow spec rules. 

The [Fastnetmon Advanced](https://fastnetmon.com/fastnetmon-advanced/) offers a number of additional features over the community edition, one of which is an API that can be used to pull data from your running FNM instances, and update their settings.

This project builds on top of the [Fastnetmon API](https://fastnetmon.com/fastnetmon-advanced-configuration-options/) to give a single-pane-of-glass interface for monitoring and managing your running FNM instances.

Thr WebUI is written in PHP 7, using the Laravel framework.

## Demo

We don't have a demo hosted of this application, but some screenshots are available on the wiki:

https://github.com/ukfast/fnm-webui/wiki/Screenshots

## Requirements

To run the WebUI, you'll need either a [LEMP](https://www.howtoforge.com/tutorial/ubuntu-laravel-php-nginx/) or [LAMP](https://medium.com/@lazycoding/how-to-install-lamp-php-7-and-laravel-5-5-from-scratch-on-ubuntu-16-04-lts-c99949e4319c) stack server with appropriate Laravel rewrite rules in place.

- Apache or NGINX
- PHP 7.1 / PHP 7.2
- MySQL 5.6+ / MariaDB 10.0+
- Redis 3.2+
- Postfix / Sendmail

This could also probably be run within a shared hosting environment if you so wish.

As the nature of this project is to provide centralised management to your blackhole system, it makes sense to host this in a separate location so you always have access to the dashboard.

Naturally, you will also need an installed Fastnetmon Advanced server with the API enabled to add into the interface.

## Installation

1. Clone down the files to your document root. Your server will need to reference the `public/` directory for it's root.
2. Create a database in MySQL.
3. Create a `GRANT` in MySQL for the new database.
4. Copy the `.env.example` file to `.env` and update the following items:
 - `APP_KEY` - Generate a random key and update this. This is used for encryption.
 - `APP_URL` - Update this to match the installed location of the WebUI.
 - `DB_*` - Set the database connection string with the details created above.
 - `REDIS_*` - If you're hosting Redis separately, update this here.
 - `MAIL_*` - Update the mail configuration for notifications from the dashboard.
 - `ACTION_CC` - *(optional)* Set this if you want a static list of emails to be CC'ed into all ban / unban action notifications.
5. Migrate the blank database schema into MySQL: `# php artisan migrate --seed`
 - This should also "seed" the database with two demo users to get logged in for the first time: https://github.com/ukfast/fnm-webui/blob/master/database/seeds/UsersTableSeeder.php

If you'd like to have the FNM WebUI log attack history, and send out notification emails when ban/unban actions are performed, you'll need to configure Fastnetmon to use a webhook to:

`https://fnm.domain.com/webhook`

...with your domain for the installation of this project swapped in.
