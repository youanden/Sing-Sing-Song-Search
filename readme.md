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
3. create `.env` in root directory or copy from `.env.example`
4. php artisan migrate (if using database driver for cache and sessions)
5. webpack (-w if you want to make edits)
6. php artisan serve or run nginx/apache with `./public` as the web root
