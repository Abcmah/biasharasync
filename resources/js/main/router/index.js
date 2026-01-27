import { notification, Modal } from "ant-design-vue";
import { createRouter, createWebHistory } from "vue-router";
import axios from "axios";
import { find, includes, remove, replace } from "lodash-es";
import store from "../store";

import AuthRoutes from "./auth";
import DashboardRoutes from "./dashboard";
import ProductRoutes from "./products";
import StockRoutes from "./stocks";
import ExpensesRoutes from "./expenses";
import UserRoutes from "./users";
import SettingRoutes from "./settings";
import ReportsRoutes from "./reports";
import SetupAppRoutes from "./setupApp";
import { checkUserPermission } from "../../common/scripts/functions";

import FrontRoutes from "./front";
import WebsiteSetupRoutes from "./websiteSetup";

const appType = window.config.app_type;
const allActiveModules = window.config.modules;

const isAdminCompanySetupCorrect = () => {
    var appSetting = store.state.auth.appSetting;

    if (appSetting.x_currency_id == null || appSetting.x_warehouse_id == null) {
        return false;
    }

    return true;
};

const isSuperAdminCompanySetupCorrect = () => {
    var appSetting = store.state.auth.appSetting;

    if (
        appSetting.x_currency_id == null ||
        appSetting.white_label_completed == false
    ) {
        return false;
    }

    return true;
};

const router = createRouter({
    history: createWebHistory(),
    routes: [
        ...FrontRoutes,
        {
            path: "",
            redirect: "/admin/login",
        },
        ...WebsiteSetupRoutes,
        ...ProductRoutes,
        ...StockRoutes,
        ...ExpensesRoutes,
        ...AuthRoutes,
        ...DashboardRoutes,
        ...UserRoutes,
        ...ReportsRoutes,
        ...SettingRoutes,
    ],
    scrollBehavior: () => ({ left: 0, top: 0 }),
});

// Including SuperAdmin Routes
// Create a variable to hold the loading promise
let superAdminRoutesPromise = null;
const superadminRouteFilePath = appType == 'saas' ? 'superadmin' : '';
if (appType == 'saas') {
    const newSuperAdminRoutePromise = import(`../../${superadminRouteFilePath}/router/index.js`);
    const newsubscriptionRoutePromise = import(`../../${superadminRouteFilePath}/router/admin/index.js`);

    superAdminRoutesPromise = Promise.all([newSuperAdminRoutePromise, newsubscriptionRoutePromise]).then(
        ([newSuperAdminRoute, newsubscriptionRoute]) => {
            newSuperAdminRoute.default.forEach(route => router.addRoute(route));
            newsubscriptionRoute.default.forEach(route => router.addRoute(route));
            SetupAppRoutes.forEach(route => router.addRoute(route));
            return true;
        }
    );
} else {
    SetupAppRoutes.forEach(route => router.addRoute(route));
    superAdminRoutesPromise = Promise.resolve(true);
}
const checkLogFogsa = (to, from, next) => {
    const appType = window.config.app_type;
    const currentPrefix = appType === 'saas' ? 'admin' : 'superadmin';
    const routeParts = to.name ? to.name.split('.') : [];

    const isAuthenticated = store.getters['auth/isLoggedIn'];
    const user = store.state.auth.user;

    // 1. Redirect SuperAdmin away from standard Admin pages
    if (routeParts[0] === 'admin' && user?.is_superadmin) {
        return next({ name: 'superadmin.dashboard.index' });
    }

    // 2. Main Logic for current App Type
    if (routeParts.length > 0 && routeParts[0] === currentPrefix) {

        // Login check
        if (to.meta.requireAuth && !isAuthenticated) {
            return next({ name: 'admin.login' });
        }

        // Setup Check (SaaS / SuperAdmin context)
        if (to.meta.requireAuth && routeParts[1] !== 'setup_app') {
            if (appType === 'saas' && !isSuperAdminCompanySetupCorrect()) {
                return next({ name: 'superadmin.setup_app.index' });
            } else if (appType !== 'saas' && !isAdminCompanySetupCorrect()) {
                 return next({ name: 'admin.setup_app.index' });
            }
        }

        // Permission Logic Fix
        if (to.meta.permission) {
            let pName = typeof to.meta.permission === 'function'
                        ? to.meta.permission(to)
                        : to.meta.permission;

            if (typeof pName === 'string') {
                if (routeParts[1] === 'stock') pName = pName.replace('-', '_');

                if (!checkUserPermission(pName, user)) {
                    return next({ name: 'admin.dashboard.index' });
                }
            }
        }
    }

    next();
};
const checkLogFog = (to, from, next) => {
    // Determine the expected prefix based on app config (saas -> admin, non-saas -> superadmin)
    const appType = window.config.app_type;
    const expectedPrefix = appType === 'saas' ? 'admin' : 'superadmin';
    const routeParts = to.name ? to.name.split('.') : [];

    const isAuthenticated = store.getters['auth/isLoggedIn'];
    const user = store.state.auth.user;

    // 1. SUPERADMIN CONTEXT (Routes starting with 'superadmin')
    if (routeParts.length > 0 && routeParts[0] === 'superadmin') {

        // If it's a SuperAdmin route but the user is a standard Admin, log them out
        if (to.meta.requireAuth && isAuthenticated && user && !user.is_superadmin) {
            store.dispatch('auth/logout');
            return next({ name: 'admin.login' });
        }

        // Check SuperAdmin Setup Completion
        if (to.meta.requireAuth && !isSuperAdminCompanySetupCorrect() && routeParts[1] !== 'setup_app') {
            return next({ name: 'superadmin.setup_app.index' });
        }

        // Standard Auth Check for SuperAdmin
        if (to.meta.requireAuth && !isAuthenticated) {
            return next({ name: 'admin.login' });
        } else if (to.meta.requireUnauth && isAuthenticated) {
            return next({ name: 'superadmin.dashboard.index' });
        }

        return next();
    }

    // 2. ADMIN / COMPANY CONTEXT (Routes starting with 'admin')
    else if (routeParts.length > 0 && routeParts[0] === 'admin') {

        // If a SuperAdmin tries to access a standard Admin route, redirect them to their own dashboard
        if (user?.is_superadmin) {
            return next({ name: 'superadmin.dashboard.index' });
        }

        // Standard Admin Auth Check
        if (to.meta.requireAuth && !isAuthenticated) {
            store.commit('auth/updateAppChecking', false);
            return next({ name: 'admin.login' });
        }

        // Check Company Admin Setup Completion (Warehouse, Currency, etc.)
        if (to.meta.requireAuth && !isAdminCompanySetupCorrect() && routeParts[1] !== 'setup_app') {
            return next({ name: 'admin.setup_app.index' });
        }

         if (to.meta.permission) {
            let pName = typeof to.meta.permission === 'function'
                        ? to.meta.permission(to)
                        : to.meta.permission;

            if (typeof pName === 'string') {
                if (routeParts[1] === 'stock') pName = pName.replace('-', '_');

                if (!checkUserPermission(pName, user)) {
                    return next({ name: 'admin.dashboard.index' });
                }
            }
        }
        // Handle Permissions (Checks for specific modules like 'stock')
        // if (to.meta.permission) {
        //     let permissionName = to.meta.permission;
        //     if (routeParts[1] === 'stock') {
        //         // Original logic replaced '-' with '_' for stock permissions
        //         console.log(to.meta.permission);
        //         permissionName = permissionName.replace('-', '_');
        //     }

        //     if (!checkUserPermission(permissionName, user)) {
        //         return next({ name: 'admin.dashboard.index' });
        //     }
        // }

        return next();
    }

    // 3. FRONT / PUBLIC CONTEXT
    else if (routeParts.length > 0 && routeParts[0] === 'front') {
        if (to.meta.requireAuth && !store.getters['front/isLoggedIn']) {
            store.commit('auth/updateAppChecking', false);
            return next({ name: 'front.homepage' });
        }
        return next();
    }

    // Default Fallback
    next();
};
router.beforeEach(async (to, from, next) => {
    // 1. Wait for dynamic routes to be registered before doing anything
    if (superAdminRoutesPromise) {
        await superAdminRoutesPromise;
    }

    // 2. Clear checking state
    store.commit('auth/updateAppChecking', false);

    // 3. Module Check
    if (to.meta && to.meta.module) {
        if (!allActiveModules.includes(to.meta.module)) {
            return next({ name: 'admin.login' });
        }
    }

    // 4. Run the LogFog logic
    checkLogFog(to, from, next);
});


export default router;
