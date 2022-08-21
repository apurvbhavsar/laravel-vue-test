function page(path) {
    return () =>
        import(/* webpackChunkName: '' */ `~/pages/${path}`).then(
            (m) => m.default || m
        );
}

export default [
    { path: "/", name: "login", component: page("auth/login.vue") },
    { path: "/posts", name: "posts", component: page("posts.vue") },
    { path: "/users", name: "users", component: page("users/users.vue") },

    {
        path: "/users/edit",
        name: "edit_user",
        component: page("users/edit.vue"),
    },

    {
        path: "/register",
        name: "register",
        component: page("auth/register.vue"),
    },

    { path: "*", component: page("errors/404.vue") },
];
