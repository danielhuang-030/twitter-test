import { createStore } from 'vuex';
import apiService from '../apiService';
import router from '../router';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';
import { ElNotification } from 'element-plus';

export default createStore({
  state() {
    return {
      token: null,
      userData: null,
      echo: null,
    };
  },
  getters: {
    isLoggedIn: state => !!state.userData
  },
  mutations: {
    SET_TOKEN(state, token) {
      state.token = token;
    },
    SET_USER_DATA(state, userData) {
      state.userData = userData;
    },
    SET_ECHO(state, echo) {
      state.echo = echo;
    }
  },
  actions: {
    checkLogin({ commit, dispatch }) {
      return new Promise((resolve, reject) => {
        const token = localStorage.getItem('user-token');
        if (token) {
          apiService.getUserProfile()
            .then(response => {
              commit('SET_USER_DATA', response.data.data.user);
              dispatch('setupWebSocket', response.data.data.user.id);
              resolve(true);
            })
            .catch(error => {
              // console.error(error);
              localStorage.removeItem('user-token');
              commit('SET_TOKEN', null);
              commit('SET_USER_DATA', null);
              reject(false);
            });
        } else {
          resolve(false);
        }
      });
    },
    setToken({ commit }, token) {
      commit('SET_TOKEN', token);
    },
    setUserData({ commit }, userData) {
      commit('SET_USER_DATA', userData);
    },
    async setupWebSocket({ commit }, userId) {
      const echo = new Echo({
        broadcaster: 'pusher',
        key: process.env.MIX_PUSHER_APP_KEY,
        wsHost: window.location.hostname,
        wsPort: window.location.port,
        wssPort: window.location.port,
        wsPath: '/ws',
        forceTLS: false,
        encrypted: true,
        disableStats: true,
        enabledTransports: ['ws', 'wss']
      });

      echo.channel(`new-user-following-user-${userId}`)
        .listen('UserFollowCreated', (event) => {
          ElNotification({
            title: 'New Follower',
            message: `${event.name} has just started following you.`,
            type: 'success',
          });
        });

      echo.channel(`new-user-unfollow-user-${userId}`)
        .listen('UserFollowDeleted', (event) => {
          ElNotification({
            title: 'Unfollowed',
            message: `${event.name} has unfollowed you.`,
            type: 'warning',
          });
        });


      apiService.getFollowingUsers(userId)
        .then(response => {
          response.data.data.following.forEach(user => {
            echo.channel(`new-post-from-user-${user.id}`)
              .listen('PostCreated', (event) => {
                ElNotification({
                    title: 'New Post',
                    message: `${event.user.name} has a new post. Check it out!`,
                    type: 'info',
                    onClick: () => {
                        router.push(`/user/${event.user_id}/posts`);
                    }
                });
              });
          });
        })
        .catch(error => {
          console.error(error);
        });

      commit('SET_ECHO', echo);
    },
    logout({ commit, dispatch }) {
      return new Promise((resolve, reject) => {
        apiService.logout()
          .then(() => {
            localStorage.removeItem('user-token');
            commit('SET_TOKEN', null);

            dispatch('disconnectWebSocket').then(() => {
              commit('SET_USER_DATA', null);

              resolve();
            });
          })
          .catch(error => {
            console.error('Logout error:', error);
            reject(error);
          });
      });
    },
    disconnectWebSocket({ state, commit }) {
      return new Promise(resolve => {
        if (state.echo) {
          if (state.echo.connector.channels) {
            const channels = Object.keys(state.echo.connector.channels);
            channels.forEach(channelName => {
                const channel = state.echo.connector.channels[channelName];
                state.echo.leave(channelName);
            });
          }

          // state.echo.disconnect();

          commit('SET_ECHO', null);

          resolve();
        } else {
          resolve();
        }
      });
    },
  }
});
