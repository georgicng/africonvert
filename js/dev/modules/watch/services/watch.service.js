function WatchService($resource, $cacheFactory, lodash, Store) {
    var EntryService = {};
    var apiCache = $cacheFactory('Watch');
    var API = $resource(local_env.api_url + '/:path/:id', {
        id: '@id',
        path: '@path'
    },
        {
            'get': { method: 'GET', cache: apiCache },
            'query': { method: 'GET', cache: apiCache, isArray: true }

        });

    EntryService.getEntries = function (param = {}) {
        if (param === null) return Promise.resolve(new Error("argument must be an object"));
        if (typeof param !== 'object') return Promise.resolve(new Error("argument must be an object"));
        param.per_page = param.per_page ? param.per_page : 10; //make this a config in backend and inject as constant
        param.page = param.page ? param.page : 1;
        param.path = 'submissions';
        console.log('service params:', param, param.per_page);
        return new Promise(function (resolve, reject) {
            //calcuate hash
            var hash = calculateHash(param);
            if (Store.watch.key != hash || (Store.watch.key === hash && Store.watch.items[param.page] === undefined)) {
                API.query(
                    param,
                    function (data, header) {
                        if (!data) {
                            resolve([]);
                        } else {
                            if (Store.watch.key == hash) {
                                Store.watch.items[param.page] = data;
                                //Store.watch.items = lodash.concat(Store.watch.items, data);
                                //Store.watch.storedPages = lodash.concat(Store.watch.storedPages, param.page);
                            } else {
                                Store.watch.items=[];
                                Store.watch.key = hash;
                                Store.watch.items[param.page] = data;
                                //Store.watch.storedPages = lodash.concat([], param.page);
                            }
                            Store.watch.totalPages = header('X-WP-TotalPages');
                            Store.watch.totalPosts = header('X-WP-Total');
                            resolve(data);

                        }
                    },
                    function (err) {
                        reject(err);
                    }
                );

            } else {
                //assumption is that pages will be accessed serially so slicing will be consecutive
                //resolve(lodash.slice(Store.watch.items, (param.page - 1) * param.per_page, param.per_page));
                resolve(Store.watch.items[param.page]);
            }
        });

    };

    EntryService.getEntry = function (id) {
        path = 'submissions';
        return new Promise(function (resolve, reject) {
            console.log(Store.watch.items);

            var entry = lodash.flatMap(
                Store.watch.items,
                function (val) {
                    return val;
                })
                .find(function (item) {
                    if (item === undefined) {return false;}
                    return item.id == id;
                });

            console.log( entry);

            if (entry !== undefined) {
                resolve(entry);
            } else {
                API.get(
                    { path: path, id: id, _embed: true },
                    function (data) {
                        if (!data) {
                            resove({});
                        } else {
                            resolve(data);
                        }
                    },
                    function (err) {
                        reject(err);
                    }
                );
            }
        });
    };

    EntryService.getCategories = function (id) {
        return new Promise(function (resolve, reject) {
            API.query(
                { path: 'submission_category' },
                function (data) {
                    if (!data) {
                        resolve([]);
                    } else {
                        resolve(data);
                    }
                },
                function (err) {
                    reject(err);
                }
            );
        });
    };

    EntryService.getTotalPost = function () {
        if (Store.watch.totalPosts)
            return parseInt(Store.watch.totalPosts);
        else
            return false;
    };

    var calculateHash = function (param) {
        var hash = param.contest + '-' + param.order + '-' + param.orderby;
        return hash;
    };



    return EntryService;
}

angular
    .module('com.module.watch')
    .factory('WatchService', WatchService);