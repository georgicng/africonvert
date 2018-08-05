function SubmitService($resource, $cacheFactory, lodash, Store) {
  var ContestService = {};
  var apiCache = $cacheFactory('Submit');
  var API = $resource(local_env.api_url + '/:posttype/:id', {
    id: '@id',
    posttype: '@posttype'
  }, {
    save: {
      method: 'POST'
    },
    'get': {
      method: 'GET'
    },
    'query': {
      method: 'GET',
      cache: apiCache,
      isArray: true
    }

  });

  ContestService.getContests = function (param = {}) {

    return new Promise(function (resolve, reject) {
      if (param === null) reject(new Error("argument must be an object"));
      if (typeof param !== 'object') reject(new Error("argument must be an object"));
      var arg = {};
      arg.per_page = param.per_page ? param.per_page : 12; //make this a config in backend and inject as constant
      arg.page = param.page ? param.page : 1;
      arg.type = param.type ? param.type : '';
      arg._embed = true;
      arg.posttype = "contests";
      //add type
      if (Store.submit.totalPosts == 0 || !lodash.includes(Store.submit.storedPages, arg.page)) {
        API.query(arg, function (data, header) {
          if (!data) {
            resolve(data);
          } else {
            if (lodash.every(data, {
                stage: 'submission'
              })) {
              Store.submit.items = lodash.concat(Store.submit.items, data);
              Store.submit.storedPages = lodash.concat(Store.submit.storedPages, arg.page);
              Store.submit.totalPages = header('X-WP-TotalPages');
              Store.submit.totalPosts = header('X-WP-Total');
              resolve(data);
            } else {
              reject({
                message: "Not allowed"
              });
            }

          }
        }, function (e) {
          reject(e);
        });

      } else {
        resolve(lodash.slice(Store.submit.items, (arg.page - 1) * arg.per_page, arg.per_page));
      }
    });

  };

  ContestService.getContest = function (id, type = null) {
    return new Promise(function (resolve, reject) {
      API.get({
        posttype: "contests",
        id: id,
        type: type,
        _embed: true
      }, function (data) {
        if (!data) {
          resolve({});
        }

        if (data.stage != "submission") {
          reject({
            message: "No response"
          });
        }

        resolve(data);
      }, function (e) {
        reject(e);
      });
    });
  };

  ContestService.getTotalPost = function () {
    if (Store.posts.totalPosts)
      return Store.submit.totalPosts;
    else
      return false;
  };

  ContestService.getEntries = function (id, page = 1) {
    if (page === false)
      return;
    return new Promise(function (resolve, reject) {
      API.query({
        posttype: "submissions",
        page: page,
        contest: id,
        _embed: true
      }, function (data, headerFn) {
        if (!data) {
          resolve([]);
        }
        var pages = parseInt(headerFn('X-WP-TotalPages'));
        if (pages === page || pages < page)
          var next_page = false;
        else if (pages > page)
          var next_page = page + 1;
        resolve({
          entries: data,
          next_page: next_page
        });
      }, function (e) {
        reject(e);
      });
    });
  };

  ContestService.saveEntry = function (post) {
    post.posttype = "upload";
    return new Promise(function (resolve, reject) {
      API.save(post, function (data) {
          if (!data) {
            resolve({});
          } else {
            resolve(data);
          }
        },
        function (e) {
          reject(e);
        });
    });
  }

  return ContestService;
}

angular
  .module('com.module.submit')
  .factory('SubmitService', SubmitService);