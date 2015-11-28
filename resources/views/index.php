<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sing Sing API V2</title>
    <link rel="stylesheet" href="/css/app.css">
  </head>
  <body>
    <div id="app" class="container">
      <div class="row">
        <header-nav title="Sing Sing Song Search"></header-nav>
        <router-view></router-view>
      </div>
    </div>
<script type="text/x-template" id="nav-template">
  <nav class="navbar navbar-dark bg-primary">
    <button class="navbar-toggler hidden-sm-up pull-right" type="button" data-toggle="collapse" data-target="#header-nav">
      &#9776;
    </button>
    <a class="navbar-brand pull-left" href="#">{{title}}</a>
    <div class="clearfix"></div>
    <div class="collapse navbar-toggleable-xs " id="header-nav">
      <ul class="nav navbar-nav">
        <li class="nav-item" v-link="/search-songs">
          <a class="nav-link" href="#">Search Songs</a>
        </li>
        <li class="nav-item" v-link="/my-songs">
          <a class="nav-link" href="#">My Songs</a>
        </li>
        <li class="nav-item" v-link="/newest-songs">
          <a class="nav-link" href="#">Newest Songs</a>
        </li>
      </ul>
    </div>
  </nav>
</script>

<script type="text/x-template" id="newest-songs">
  <div class="rel">
    <div class="row">
      <div class="form-group m-t col-xs-9">
        <input v-model="searchQuery" type="search" class="form-control form-control-lg" placeholder="Search within new songs...">
      </div>
    </div>
    <newest-songs-grid
      data="{{gridSongs}}"
      columns="{{gridColumns}}"
      filter-key="{{searchQuery}}"
      my-songs="{{mySongs}}"
    >
    </newest-songs-grid>
  </div>
</script>

<script type="text/x-template" id="my-songs">
  <div class="rel">
    <div class="row">
      <div class="form-group m-t col-xs-9">
        <input v-model="searchQuery" type="search" class="form-control form-control-lg" placeholder="Search within my songs...">
      </div>
    </div>
    <my-songs-grid
      data="{{gridSongs}}"
      columns="{{gridColumns}}"
      filter-key="{{searchQuery}}"
      removable="true"
    >
    </my-songs-grid>
  </div>
</script>
<script type="text/x-template" id="search-songs">
  <div class="rel">
    <div class="row">
      <div class="form-group m-t col-xs-9">
        <input v-model="searchQuery" type="search" class="form-control form-control-lg" placeholder="Search song name or artist...">
      </div>
    </div>
    <search-songs-grid
      data="{{gridSongs}}"
      columns="{{gridColumns}}"
      searchQuery="{{searchQuery}}"
      filter-key="{{searchQuery}}"
    >
    </search-songs-grid>
  </div>
</script>
<script type="text/x-template" id="songs-grid">
  <span class="abs t-r-0 bg-info m-t p-a m-r">Total Results: {{data.length}}</span>
  <div class="row">
    <div class="alert alert-success col-xs-12" role="alert" v-class="invisible : data.length">
      <strong>Oh no!</strong> There are no songs here. No droids, my friend.
    </div>
  </div>
  <table class="table m-t" v-class="invisible : !data.length">
    <thead>
      <tr>
        <th v-repeat="key: columns"
          v-on="click:sortBy(key)"
          v-class="active: sortKey == key">
          {{key | capitalize}}
          <span class="arrow"
            v-class="reversed[key] ? 'dsc' : 'asc'">
          </span>
        </th>
        <th>
        Actions
        </th>
      </tr>
    </thead>
    <tbody>
      <tr v-repeat="
        entry: data
        | filterBy filterKey
        | orderBy sortKey reversed[sortKey]">
        <td v-repeat="key: columns" class="column-{{key}}">
          {{entry[key]}}
        </td>
        <td>
          <button v-if="!removable && !entry.selected" v-on="click: addSong(entry, $event)" class="btn btn-primary">
          Add
          </button>
          <button disabled="disabled" v-if="entry.selected" v-on="click: addSong(entry, $event)" class="btn btn-primary">
          Added
          </button>
          <div class="btn-group" v-if="removable" >

            <button v-on="click: getLyrics(entry, $event)" class="btn btn-primary">
            LYRICS
            </button>
            <button v-if="removable" v-on="click: removeSong(entry, $event)" class="btn btn-danger">
            X
            </button>
            <!-- <ul class="dropdown-menu">
              <li><a href="javascript:;">Get Lyrics</a></li>
              <li ><a href="javascript:;" >Delete</a></li>
            </ul> -->
          </div>
        </td>
      </tr>
    </tbody>
  </table>
</script>

<!-- <button type="button" class="btn btn-primary btn-lg" data-toggle="modal" data-target="#myModal">
  Launch demo modal
</button> -->

<!-- Modal -->
<div class="modal fade" id="lyricsModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-body">
        <iframe width="100%" height="500px"></iframe>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
    <script src="/js/app.js"></script>
  </body>
</html>
