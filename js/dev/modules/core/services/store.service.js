angular
    .module('com.module.core')
	.factory('Store',function(){	
		var db = {
			posts: { categories: [], items: [], isolatedItems: [], tags: [], storedPages:[], totalPages:0, totalPosts:0 },
			vote: { terms: [], items: [], storedPages:[], totalPages:0, totalPosts:0 },
			submit: { terms: [], items: [], storedPages:[], totalPages:0, totalPosts:0 },
			archive: { terms: [], items: [], storedPages:[], totalPages:0, totalPosts:0 },
			watch: { key: "", items: [], totalPages:0, totalPosts:0 },
			page: { items: [] },
			entries: { terms: [], items: [] }
		};	
		return db;
	});