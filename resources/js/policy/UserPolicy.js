export default class PostPolicy
{
    static viewAny(user)
    {
        console.log(user.role);
        return user.role === 'admin';
    }

    static create(user)
    {
        return user.role === 'admin';
    }

    static view(user, post)
    {
        return user.role === 'admin';
    }

    static delete(user, post)
    {
        return user.role === 'admin' || user.id === post.user_id;
    }

    static update(user, post)
    {
        return user.role === 'admin' || user.id === post.user_id;
    }
}
