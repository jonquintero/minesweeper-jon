
import NotFound from "./components/NotFound";

import Grid from "./components/Grid";
import CreateGame from "./components/CreateGame";

export default {

    mode: 'history',

    linkActiveClass: 'font-bold',

    routes: [
        {
            path: '*',
            component: NotFound
        },

        {
            name: 'home',
            path: '/',
            component: CreateGame,

        },
        {
            name: 'game',
            path: '/play-game/:id',
            component: Grid,

        },

    ]
}
