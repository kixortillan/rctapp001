function isEmpty(s) {
    return (!s || s.length === 0 || s === '');
}

function hasRole(roles, target) {

    for (var r of roles) {

        if (target instanceof Array) {

            if (target.indexOf(r.role) > -1) {
                return true;
            }

        } else {

            if (r.role === 'role') {
                return true;
            }

        }

    }

    return false;
}

export {
    isEmpty,
    hasRole,
}