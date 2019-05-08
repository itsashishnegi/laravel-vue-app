import VueRouter from 'vue-router';

const routes = [
  {
  	path: '/',
  	component: require('./pages/VueApplicationsComponent').default
  },
  {
  	path: '/todo',
  	component: require('./pages/ToDoComponent').default
  },
  {
  	path: '/github',
  	component: require('./pages/VueGitHubComponent').default
  },
  {
    path: '/form',
    component: require('./pages/FormComponent').default
  },
  {
    path: '/input',
    component: require('./pages/InputComponent').default
  },
  {
    path: '/slots',
    component: require('./pages/SlotsComponent').default
  }
];

export default new VueRouter({
  routes // short for `routes: routes`
});