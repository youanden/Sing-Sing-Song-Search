## Sing Sing Song Search

### Why?

I was bored.


### What?

![Sing Sing Song Search Screenshot](http://i.imgur.com/jGCQ46X.png)

Makes requests to sing-sing media's new karaoke search and newest songs list.
Uses localstorage to save selected songs

### Techologies

- Lumen on PHP
- Vue.js
- ES6
- Webpack

### Setup

1. npm install
2. bower install
3. composer install
4. create `.env` in root directory or copy from `.env.example`
5. php artisan migrate (if using database driver for cache and sessions)
6. webpack (-w if you want to make edits)
7. php artisan serve or run nginx/apache with `./public` as the web root


### Notes

This project is not affiliated with Sing Sing Media.
