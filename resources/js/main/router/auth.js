import Login from "../views/auth/Login.vue";
import ForgotPassword from "../views/auth/ForgotPassword.vue";
import Verify from "../views/auth/Verify.vue";

export default [
    {
        path: "/admin/login",
        component: Login,
        name: "admin.login",
        meta: {
            requireUnauth: true,
            menuKey: (route) => "login",
        },
    },

    {
        path: "/admin/reset-password",
        name: "admin.reset_password",
        component: () => import("../views/auth/ResetPassword.vue"),
        meta: {
            requireUnauth: true,
            menuKey: (route) => "reset_password",
        },
    },
    {
        path: "/admin/forgot-password",
        component: ForgotPassword,
        name: "admin.forgot_password",
        meta: {
            requireUnauth: true,
            menuKey: (route) => "forgot_password",
        },
    },
    {
        path: "/admin/verify",
        component: Verify,
        name: "verify.main",
        meta: {
            requireUnauth: true,
            menuKey: (route) => "verify_product",
        },
    },
];
