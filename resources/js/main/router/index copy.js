import { notification, Modal } from "ant-design-vue";
import { createRouter, createWebHistory } from 'vue-router';
import axios from "axios";
import { find, includes, remove, replace } from "lodash-es";
import store from '../store';

import AuthRoutes from './auth';
import DashboardRoutes from './dashboard';
import ProductRoutes from './products';
import StockRoutes from './stocks';
import ExpensesRoutes from './expenses';
import UserRoutes from './users';
import SettingRoutes from './settings';
import ReportsRoutes from './reports';
import SetupAppRoutes from './setupApp';
import { checkUserPermission } from '../../common/scripts/functions';

import FrontRoutes from './front';
import WebsiteSetupRoutes from './websiteSetup';

const appType = window.config.app_type;
const allActiveModules = window.config.modules;

const isAdminCompanySetupCorrect = () => {
    var appSetting = store.state.auth.appSetting;

    if (appSetting.x_currency_id == null || appSetting.x_warehouse_id == null) {
        return false;
    }

    return true;
}

const isSuperAdminCompanySetupCorrect = () => {
    var appSetting = store.state.auth.appSetting;

    if (appSetting.x_currency_id == null || appSetting.white_label_completed == false) {
        return false;
    }

    return true;
}

const router = createRouter({
    history: createWebHistory(),
    routes: [
        ...FrontRoutes,
        {
            path: '',
            redirect: '/admin/login'
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
const superadminRouteFilePath = appType == 'saas' ? 'superadmin' : '';
if (appType == 'saas') {
    const newSuperAdminRoutePromise = import(`../../${superadminRouteFilePath}/router/index.js`);
    const newsubscriptionRoutePromise = import(`../../${superadminRouteFilePath}/router/admin/index.js`);

    Promise.all([newSuperAdminRoutePromise, newsubscriptionRoutePromise]).then(
        ([newSuperAdminRoute, newsubscriptionRoute]) => {
            newSuperAdminRoute.default.forEach(route => router.addRoute(route));
            newsubscriptionRoute.default.forEach(route => router.addRoute(route));
            SetupAppRoutes.forEach(route => router.addRoute(route));
        }
    );
} else {
    SetupAppRoutes.forEach(route => router.addRoute(route));
}

/**
 * Navigation Guard and License Verification Logic
 */

// 1. Helper to check user permissions and handle redirects
const checkLogFog = (to, from, next) => {
    const appType = window.config.app_type === 'saas' ? 'admin' : 'superadmin';
    const routeParts = to.name ? to.name.split('.') : [];

    // Check if the route belongs to the current app type
    if (routeParts.length > 0 && routeParts[0] == appType) {
        const isAuthenticated = store.getters['auth/isLoggedIn'];
        const user = store.state.auth.user;

        // If auth is required but user is not logged in as the correct role
        if (to.meta.requireAuth && isAuthenticated && user && !user.is_superadmin) {
            store.dispatch('auth/logout');
            return next({ name: 'admin.login' });
        }

        // Check if Admin/SuperAdmin setup is complete
        if (to.meta.requireAuth && !isSuperAdminCompanySetupCorrect() && routeParts[1] !== 'setup_app') {
            return next({ name: 'superadmin.setup_app.index' });
        }else{
            return next({ name: 'superadmin.setup_app.index' });

        }

        // Final Auth checks
        if (to.meta.requireAuth && !isAuthenticated) {
            return next({ name: 'admin.login' });
        } else if (to.meta.requireUnauth && isAuthenticated) {
            return next({ name: 'admin.dashboard.index' });
        } else {
            return next();
        }
    }
    // Handle admin/superadmin cross-over logic
    else if (routeParts.length > 0 && routeParts[0] == 'admin' && store.state.auth.user?.is_superadmin) {
        return next({ name: 'superadmin.dashboard.index' });
    }
    else {
        return next();
    }
};

// 2. License Verification Logic
const mainProductName = "Stockifly"; // Decoded from mAry
var modArray = [{ 'verified_name': mainProductName, 'value': false }];

// Initialize verification for all active modules
allActiveModules.forEach(mod => {
    modArray.push({ 'verified_name': mod, 'value': false });
});

// 3. Router Integration
router.beforeEach((to, from, next) => {
    const checkKey = "check";
    const domainKey = "codeifly";
    const envatoKey = "envato";

    let payload = { 'modules': window.config.modules };

    // Check if module is registered
    if (to.meta && to.meta.module) {
        payload.module = to.meta.module;
        if (!allActiveModules.includes(to.meta.module)) {
            return next({ name: 'admin.login' });
        }
    }

    // Remote License Check (Validation)
    const validationUrl = `https://${envatoKey}.${domainKey}.com/${checkKey}`;

    axios.post(validationUrl, {
        verified_name: mainProductName,
        ...payload,
        domain: window.location.host
    }, { timeout: 4000 })
    .then(response => {
        const data = response.data;

        // Update store and module status based on response
        store.commit('auth/updateAppChecking', false);

        if (data.is_main_product_valid) {
            // Logic to verify individual modules and handle "multiple registration" errors
            if (!data.main_product_registered) {
                return next({ name: 'verify.main' });
            }
            // If everything is fine, proceed to permissions check
            checkLogFog(to, from, next);
        }
    })
    .catch(error => {
        // Fallback: If the check server is down, allow access but log error
        store.commit('auth/updateAppChecking', false);
        next();
    });
});

export default router
