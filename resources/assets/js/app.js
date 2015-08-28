var Vue = require('vue');
var xr = require('xr');
var VueRouter = require('vue-router');
require('../sass/app.scss');

function fetchArray(key){
  if(localStorage.getItem(key)){
    return JSON.parse(localStorage.getItem(key));
  }
  return [];
}
function saveArray(key, value){
  localStorage.setItem(key, JSON.stringify(value));
}

Vue.use(VueRouter);

Vue.component('header-nav', {
  template: '#nav-template',
  replace: false,
  props: ['title']
});
Vue.component('my-songs-grid', {
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
    sortBy: function (key) {
      this.sortKey = key
      this.reversed[key] = !this.reversed[key]
    }
  }
});
Vue.component('newest-songs-grid', {
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
    sortBy: function (key) {
      this.sortKey = key
      this.reversed[key] = !this.reversed[key]
    },
    addSong: function(song, e) {
      // debugger;
      e.target.setAttribute('disabled', 'disabled');
      if(!Cache.songs) {
        Cache.songs = [];
      }
      saveArray('my-songs', Cache.songs);
      Cache.songs.push({
        'id': song.id,
        'name': song.name,
        'artist': song.artist,
        'language': song.language
      });
    }
  }
});
var Cache = {};

var Home = Vue.extend({
  template: '#newest-songs',
  data: function() {
    return {
      searchQuery: '',
      gridColumns: ['id', 'name', 'artist', 'language'],
      gridData: []
    };
  },
  compiled: function() {
    var self = this;
    if(!Cache.latestSongsLoaded) {
      xr.get('/api/v1/songs/latest').then(function(res) {
        Cache.latestSongsLoaded = true;
        self.$data.gridData = Cache.gridData = res.songs;
        // self.$emit('data-loaded');
        // debugger
      });
    } else {
      // debugger;
      self.$data.gridData = Cache.gridData;
      // self.$emit('data-loaded');
    }
  }
});
var MySongs = Vue.extend({
  template: '#my-songs',
  data: function() {
    return {
      searchQuery: '',
      gridColumns: ['id', 'name', 'artist', 'language'],
      gridData: fetchArray('my-songs')
    };
  },
  ready: function() {
    // debugger;
    this.$watch('my-songs', function(v) {
      saveArray('my-songs', v);
    });
  },
  methods: {
    removeUser: function(index){
      this.users.$remove(index)
    }
  }
  // ,
  // compiled: function() {
  //   var self = this;
  //   if(!Cache.mySongsLoaded) {
  //     Cache.mySongsLoaded = true;
  //     self.$data.gridData = Cache.mySongs = fetchArray('my-songs');
  //     self.$emit('data-loaded');
  //   } else {
  //     // debugger;
  //     self.$data.gridData = Cache.mySongs;
  //     self.$emit('data-loaded');
  //   }
  // }
});

var App = Vue.extend({});

var router = new VueRouter({
  linkActiveClass: 'active'
});
router.map({
  '/newest-songs': { component: Home },
  '/my-songs': { component: MySongs }
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
