export default class PostPolicy
{
    static create(user)
    {
        return user.role.includes(['admin', 'user']);
    }

    static view(user, post)
    {
        return true;
    }

    static delete(user, post)
    {
        return user.id === post.user_id;
    }

    static update(user, post)
    {
        return user.id === post.user_id;
    }
}
