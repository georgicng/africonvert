function VoteService($resource, $cacheFactory, lodash, Store, env) {
  var ContestService = {};
  var apiCache = $cacheFactory('Vote');
  var API = $resource(env.api_url + '/:cpt/:id', {
    id: '@id',
    cpt: '@cpt'
  }, {
    save: {
      method: 'POST'
    },
    'get': {
      method: 'GET',
      cache: apiCache
    },
    'query': {
      method: 'GET',
      cache: apiCache,
      isArray: true
    }

  });

  var WISTIA_API = $resource(env.wistia_api_base + '/:path/:item/:id', {
    id: '@id',
    path: '@path',
    item: '@item'
  }, {
    save: {
      method: 'POST'
    },
    'get': {
      method: 'GET',
      cache: apiCache
    },
    'query': {
      method: 'GET',
      cache: apiCache,
      isArray: true
    }

  });

  ContestService.getContests = function (param = {}) {
    if (param === null) return new Error("argument must be an object");
    if (typeof param !== 'object') return new Error("argument must be an object");
    var arg = {};
    arg.per_page = param.per_page ? param.per_page : 12; //make this a config in backend and inject as constant
    arg.page = param.page ? param.page : 1;
    arg.type = param.type ? param.type : 'vote';
    arg._embed = true;
    arg.cpt = 'contests';
    //add type
    return new Promise(function (resolve, reject) {
      if (Store.vote.totalPosts == 0 || !lodash.includes(Store.vote.storedPages, arg.page)) {
        API.query(
          arg,
          function (data, header) {
            if (!data) {
              resolve([]);
            } else if (lodash.every(data, {
                stage: 'voting'
              })) {
              Store.vote.items = lodash.concat(Store.vote.items, data);
              Store.vote.storedPages = lodash.concat(Store.vote.storedPages, arg.page);
              Store.vote.totalPages = header('X-WP-TotalPages');
              Store.vote.totalPosts = header('X-WP-Total');
              resolve(data);

            } else {
              reject({
                message: "Not allowed"
              });
            }
          },
          function (err) {
            reject(err);
          }
        );

      } else {
        resolve(lodash.slice(Store.vote.items, (arg.page - 1) * arg.per_page, arg.per_page));
      }
    });

  };

  ContestService.getContest = function (id) {
    return new Promise(function (resolve, reject) {
      API.get({
          cpt: 'contests',
          id: id,
          _embed: true,
          type: "vote"
        },
        function (data) {
          if (!data) {
            resolve({});
          }
          if (data.stage != "voting") {
            reject({
              message: "No response"
            });
          }
          resolve(data);
        },
        function (err) {
          reject(err);
        }
      );
    });
  };


  //store in store and fetch entry with lodash
  ContestService.getEntries = function (id, page = 1) {
    if (page === false)
      return;
    return new Promise(function (resolve, reject) {
      API.query({
          cpt: "submissions",
          contest: id,
          page: page,
          _embed: true
        },
        function (data, headerFn) {
          if (!data) {
            resolve([]);
          } else {
            var pages = parseInt(headerFn('X-WP-Totalpages'));
            if (pages === page || pages < page)
              var next_page = false;
            else if (pages > page)
              var next_page = page + 1;
            resolve({
              data: data,
              page: next_page,
              count: headerFn('X-WP-Total')
            });
          }
        },
        function (e) {
          reject({
            data: null,
            page: false
          });
        }
      );
    });
  };

  ContestService.getProjectStats = function (id) {
    return new Promise(function (resolve, reject) {
      WISTIA_API.get({
          path: 'stats',
          item: "projects",
          id: id + '.json',
          api_password: local_env.wistia_api_password
        },
        function (data) {
          if (!data) {
            resolve([]);
          } else {
            resolve(data);
          }
        },
        function (e) {
          reject(e);
        }
      );
    });
  };

  ContestService.getMediaStats = function (id) {
    return new Promise(function (resolve, reject) {
      WISTIA_API.get({
          path: 'stats',
          item: "medias",
          id: id + '.json',
          api_password: local_env.wistia_api_password
        },
        function (data) {
          if (!data) {
            resolve([]);
          } else {
            resolve(data);
          }
        },
        function (e) {
          reject(e);
        }
      );
    });
  };

  ContestService.getMedia = function (id) {
    return new Promise(function (resolve, reject) {
      WISTIA_API.get({
          path: "medias",
          item: id + '.json',
          api_password: local_env.wistia_api_password
        },
        function (data) {
          resolve(data);
        },
        function (error) {
          reject(error);
        }
      );
    });
  };

  ContestService.getEntry = function (id, contest) {
    return new Promise(function (resolve, reject) {
      var entry;
      if (entry = lodash.find(Store.entries.items, function (item) {
          return item.id == id;
        })) {
        resolve(entry);
      } else {
        API.get({
            cpt: "submissions",
            id: id,
            canVote: contest,
            _embed: true
          },
          function (data) {
            if (!data) {
              resolve([]);
            } else {
              resolve(data);
            }
          },
          function (e) {
            reject(e);
          }
        );
      }
    });
  };

  ContestService.getTotalPost = function () {
    if (Store.posts.totalPosts)
      return Store.vote.totalPosts;
    else
      return false;
  };
  return ContestService;
}

angular
  .module('com.module.vote')
  .factory('VoteService', VoteService);