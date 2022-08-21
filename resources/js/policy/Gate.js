import PostPolicy from './PostPolicy';
import UserPolicy from './UserPolicy'
import store from 'vuex';

export default class Gate
{
    constructor()
    {
        const user = JSON.parse(localStorage.getItem('user'));
        this.user = user;

        this.policies = {
            post: PostPolicy,
            user: UserPolicy
        };
    }

    before()
    {
        return this.user.role === 'admin';
    }

    allow(action, type, model = null)
    {
        if (this.before()) {
            return true;
        }

        return this.policies[type][action](this.user, model);
    }

    deny(action, type, model = null)
    {
        return ! this.allow(action, type, model);
    }
}
