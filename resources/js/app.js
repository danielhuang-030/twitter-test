require('./bootstrap');

window.Echo.channel('new-user-following-uesr-2')
    .listen('UserFollowCreated', (e) => {
        console.log("UserFollowCreated");
        console.log(e);
    });

window.Echo.channel('new-user-unfollow-uesr-2')
    .listen('UserFollowDeleted', (e) => {
        console.log("UserFollowDeleted");
        console.log(e);
    });