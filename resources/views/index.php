<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Sing Sing API V2</title>
    <link rel="stylesheet" href="/css/app.css">
  </head>
  <body>
    <div id="app" class="container m-t">
      <div class="row">
        <header-nav title="Sing Sing Song Search"></header-nav>
        <router-view></router-view>
      </div>
    </div>
<script type="text/x-template" id="nav-template">
  <nav class="navbar navbar-dark bg-primary m-t">
    <a class="navbar-brand" href="#">{{title}}</a>
    <ul class="nav navbar-nav">
      <li class="nav-item" v-link="/newest-songs">
        <a class="nav-link" href="#">Newest Songs</a>
      </li>
      <li class="nav-item" v-link="/my-songs">
        <a class="nav-link" href="#">My Songs</a>
      </li>
    </ul>
  </nav>
</script>

<script type="text/x-template" id="newest-songs">
  <div class="form-group m-t col-xs-9">
    <input v-model="searchQuery" type="search" class="form-control form-control-lg" placeholder="Search within new songs...">
  </div>
  <newest-songs-grid
    data="{{gridData}}"
    columns="{{gridColumns}}"
    filter-key="{{searchQuery}}"
  >
  </newest-songs-grid>
</script>

<script type="text/x-template" id="my-songs">
  <div class="form-group m-t col-xs-9">
    <input v-model="searchQuery" type="search" class="form-control form-control-lg" placeholder="Search within my songs...">
  </div>
  <my-songs-grid
    data="{{gridData}}"
    columns="{{gridColumns}}"
    filter-key="{{searchQuery}}"
  >
  </my-songs-grid>
</script>
<script type="text/x-template" id="songs-grid">
  <span class="pull-right bg-info m-t m-b p-a m-r">Total Results: {{data.length}}</span>
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
          <button v-on="click: addSong(entry, $event)" class="btn btn-primary">
          Add
          </button>
        </td>
      </tr>
    </tbody>
  </table>
</script>

    <script src="/js/app.js"></script>
  </body>
</html>
