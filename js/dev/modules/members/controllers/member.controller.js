function MemberController($scope, $stateParams, member, MemberService) {
  $scope.post = member;

  if (member.followership == null)
    $scope.isFollowing = false;
  else
    $scope.isFollowing = member.followership?true:false;

  $scope.getting_activities = false;
  $scope.activity_next_page = null;  
  $scope.getActivities = function(){
    MemberService.getUserActivities($stateParams.id)
    .then(function(response){
      $scope.activities = response.data;
      $scope.activity_next_page = response.page;
    })
    .catch(function(err){
       $scope.activities = null;
        $scope.activity_next_page = false;
    });
  };

  $scope.moreActivities = function(){
    if ($scope.getting_activities == true)
      return;
    $scope.getting_activities = true;

    MemberService.getUserActivities($stateParams.id, $scope.activity_next_page)
    .then(function(response){
      $scope.activities = $scope.activities.concat(response.data);
      $scope.activity_next_page = response.page;
      $scope.getting_activities = false;
    })
    .catch(function(err){
        if(err == "Last")
          $scope.activity_next_page = false;
        $scope.getting_activities = false;
    });
  };

	$scope.getEntries = function(){
    if($scope.entries)
      return;
    MemberService.getUserContent($stateParams.id)
    .then(function(data){
      $scope.entries = data;
    })
    .catch(function(err){
       $scope.entries = null;
    });
  };

  $scope.getContests = function(){
    if($scope.contests)
      return;
    MemberService.getUserContests($stateParams.id)
    .then(function(data){
      $scope.contests = data;
    })
    .catch(function(err){
       $scope.contests = null;
    });
  };

  $scope.getAwards = function(){
    if($scope.awards)
      return;
    MemberService.getUserAwards($stateParams.id)
    .then(function(data){
      $scope.awards = data;
    })
    .catch(function(err){
       $scope.awards = null;
    });
  };

  $scope.getting_followers = false;
  $scope.follower_next_page = null;
  $scope.getFollowers = function(){
    if($scope.followers)
      return;
    MemberService.getUserFollowers($stateParams.id)
    .then(function(response){
      $scope.followers = response.data;
      $scope.follower_next_page = response.page;
    })
    .catch(function(err){
       $scope.followers = null;
       $scope.follower_next_page = false;
    });
  };

  $scope.moreFollowers = function(){
    if ($scope.getting_followers == true)
      return;
    $scope.getting_followers = true;

    MemberService.getUserFollowers($stateParams.id, $scope.follower_next_page)
    .then(function(response){
      $scope.followers = $scope.followers.concat(response.data);
      $scope.follower_next_page = response.page;
      $scope.getting_followers = false;
    })
    .catch(function(err){
        if(err == "Last")
          $scope.follower_next_page = false;
        $scope.getting_followers = false;
    });
  };

  $scope.getting_following = false;
  $scope.following_next_page = null;

  $scope.getFollowing = function(){
    if($scope.following)
      return;
    MemberService.getUserFollowing($stateParams.id)
    .then(function(response){
      $scope.following = response.data;
      $scope.following_next_page = response.page;
    })
    .catch(function(err){
       $scope.following = null;
       $scope.following_next_page = false;
    });
  };

  $scope.moreFollowing = function(){
    if ($scope.getting_following == true)
      return;
    $scope.getting_following = true;

    MemberService.getUserFollowers($stateParams.id, $scope.following_next_page)
    .then(function(response){
      $scope.following = $scope.following.concat(response.data);
      $scope.following_next_page = response.page;
      $scope.getting_following = false;
    })
    .catch(function(err){
        if(err == "Last")
          $scope.following_next_page = false;
        $scope.getting_following = false;
    });
  };

}

angular
  .module('com.module.members')
  .controller('MemberController', MemberController);
