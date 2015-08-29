var Vue = require('vue');
var xr = require('xr');
var _ = require('lodash');
var store = require('store');
var VueRouter = require('vue-router');
// debugger;
const MY_SONGS_KEY = 'my-songs';

require('../sass/app.scss');
require('jquery');
require('bootstrap');


function debounce(func, wait, immediate) {
	var timeout;
	return function() {
		var context = this, args = arguments;
		var later = function() {
			timeout = null;
			if (!immediate) func.apply(context, args);
		};
		var callNow = immediate && !timeout;
		clearTimeout(timeout);
		timeout = setTimeout(later, wait);
		if (callNow) func.apply(context, args);
	};
};

// Initialize Store to previous value
store.set(
  MY_SONGS_KEY,
  store.get(MY_SONGS_KEY) ? store.get(MY_SONGS_KEY) : []
);
var pushSong = function(song) {
  var songs = store.get(MY_SONGS_KEY);
  song = {
    'id': song.id,
    'name': song.name,
    'artist': song.artist,
    'language': song.language
  };
  console.log('Song Pushed', song);
  songs.push(song);
  store.set(MY_SONGS_KEY, songs);
};
var removeSong = function(song) {
  var songs = store.get(MY_SONGS_KEY);
  var newSongs = _.remove(songs, function(cachedSong) {
    // console.log(cachedSong, song);
    var removeSong = cachedSong.id !== song.id;
    if(removeSong) { console.log(song); }
    return removeSong;
  });
  store.set(MY_SONGS_KEY, newSongs);
};

Vue.use(VueRouter);

Vue.component('header-nav', {
  template: '#nav-template',
  replace: false,
  props: ['title']
});
Vue.component('search-songs-grid', {
  template: '#songs-grid',
  replace: false,
  props: ['data', 'columns', 'filter-key'],
  data: function () {
    return {
      data: null,
      columns: null,
      sortKey: '',
      filterKey: '',
      reversed: {}
    }
  },
  compiled: function () {
    // initialize reverse state
    var self = this
    this.columns.forEach(function (key) {
      self.reversed.$add(key, false)
    })
  },
  methods: {
    addSong: function(song, e) {
      e.target.setAttribute('disabled', 'disabled');
      e.target.innerText = 'Added';
      pushSong(song);
    },
    sortBy: function (key) {
      this.sortKey = key
      this.reversed[key] = !this.reversed[key]
    }
  }
});

Vue.component('my-songs-grid', {
  template: '#songs-grid',
  replace: false,
  props: ['data', 'columns', 'filter-key', 'removable'],
  data: function () {
    return {
      data: null,
      columns: null,
      sortKey: '',
      filterKey: '',
      reversed: {}
    }
  },
  compiled: function () {
    // initialize reverse state
    var self = this
    this.columns.forEach(function (key) {
      self.reversed.$add(key, false)
    })
  },
  methods: {
    sortBy: function (key) {
      this.sortKey = key
      this.reversed[key] = !this.reversed[key]
    },
    removeSong: function(song, e) {
      // debugger;
      e.target.setAttribute('disabled', 'disabled');
      e.target.parentElement.parentElement.remove();
      removeSong(song);
    }
  }
});
Vue.component('newest-songs-grid', {
  template: '#songs-grid',
  replace: false,
  props: ['data', 'columns', 'filter-key', 'my-songs'],
  data: function () {
    return {
      data: null,
      columns: null,
      sortKey: '',
      filterKey: '',
      reversed: {}
    }
  },
  compiled: function () {
    // initialize reverse state
    var self = this
    this.columns.forEach(function (key) {
      self.reversed.$add(key, false)
    })
  },
  methods: {
    sortBy: function (key) {
      this.sortKey = key
      this.reversed[key] = !this.reversed[key]
    },
    addSong: function(song, e) {
      e.target.setAttribute('disabled', 'disabled');
      e.target.innerText = 'Added';
      pushSong(song);
    }
  }
});

var Home = Vue.extend({
  template: '#newest-songs',
  data: function() {
    return {
      searchQuery: '',
      gridColumns: ['id', 'name', 'artist', 'language'],
      gridSongs: [],
      mySongs: store.get(MY_SONGS_KEY)
    };
  },
  compiled: function() {
    var self = this;
    xr.get('/api/v1/songs/latest').then(function(res) {
      res.songs = _.map(res.songs, function(song) {
        // debugger;
        song.selected = !! _.findWhere(self.mySongs, {
          'id': song.id
        });
        // debugger;
        return song;
      });
      self.gridSongs = res.songs;
      // self.$emit('data-loaded');
      // debugger
    });
  }
});
var MySongs = Vue.extend({
  template: '#my-songs',
  data: function() {
    return {
      searchQuery: '',
      gridColumns: ['id', 'name', 'artist', 'language'],
      gridSongs: store.get(MY_SONGS_KEY)
    };
  }
});

var SearchSongs = Vue.extend({
  template: '#search-songs',
  data: function() {
    return {
      searchQuery: '',
      gridColumns: ['id', 'name', 'artist', 'language'],
      gridSongs: [],
      mySongs: store.get(MY_SONGS_KEY)
    };
  }
  ,
  ready: function() {
    this.$watch('searchQuery', function(inputValue) {
      this.searchSongs(inputValue);
    });
  },
  compiled: function() {
    var self = this;
    this.searchSongs = debounce(function(inputValue) {
      if(!inputValue) { return; }
      xr.get('/api/v1/songs/search/' + inputValue)
        .then(function(res) {
          // debugger;
          res.songs = _.map(res.songs, function(song) {
            // debugger;
            song.selected = !! _.findWhere(self.mySongs, {
              'id': song.id
            });
            // debugger;
            return song;
          });
          // debugger;
          self.gridSongs = res.songs;
        });
    }, 500);
    // debugger;
  },
  methods: {
    searchSongs: function() {}
  }
});

var App = Vue.extend({});

var router = new VueRouter({
  linkActiveClass: 'active'
});
router.map({
  '/newest-songs': { component: Home },
  '/my-songs': { component: MySongs },
  '/search-songs': { component: SearchSongs }
});
router.start(App, '#app');
// Vue.component('home', {
//   template: ''
// });
// Vue.component('page1', {
//
// });
// var app = new Vue({
//   el: '#app',
//   data: {
//     currentView: 'home'
//   }
// });
// Switching pages in your route handler:
// app.currentView = 'page1'
// xr.get('/api/v1/songs/latest').then(res => console.log('sup'));
// xr.get('/api/v1/songs/search/Linkin Park').then(res => console.log('sup'));
// alert('SUP');
