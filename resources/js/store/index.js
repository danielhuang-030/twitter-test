import { createStore } from 'vuex';
import apiService from '../apiService';

export default createStore({
  state() {
    return {
      token: null,
      userData: null
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
    }
  },
  actions: {
    checkLogin({ commit }) {
      return new Promise((resolve, reject) => {
        const token = localStorage.getItem('user-token');
        if (token) {
          apiService.getUserProfile()
            .then(response => {
              commit('SET_USER_DATA', response.data.data.user);
              resolve(true);
            })
            .catch(error => {
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
    }
  }
});
