import VueRouter from 'vue-router';

const routes = [
  {
  	path: '/',
  	component: require('./pages/VueApplications').default
  },
  {
  	path: '/todo',
  	component: require('./pages/ToDo').default
  },
  {
  	path: '/github',
  	component: require('./pages/VueGitHub').default
  },
  {
    path: '/form',
    component: require('./pages/Form').default
  },
  {
    path: '/input',
    component: require('./pages/Input').default
  },
  {
    path: '/slots',
    component: require('./pages/Slots').default
  },
  {
    path: '/filters',
    component: require('./pages/Filters').default
  }
];

export default new VueRouter({
  routes // short for `routes: routes`
});