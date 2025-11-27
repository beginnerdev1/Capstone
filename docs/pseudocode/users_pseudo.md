Users subsystem — Pseudocode

Purpose: Manage registered users (add, update, view, list/filter). Show client-server validation interplay.

Client-side (JS) sanitizers and validation:
- Name fields: allow letters, spaces, dot -> sanitize with regex /[^A-Za-z. ]/g
- Phone fields: allow digits, N/A and / -> sanitize with regex /[^0-9NnAa\/]/g
- Use HTML `pattern` attributes for immediate browser validation; still perform server-side validation.

Controller: Admin->addUser(request)
- parse request body
- perform server-side validation:
    first_name: required, length 2-50, matches /^[A-Za-z. ]{2,50}$/
    last_name: required, length 2-50, matches /^[A-Za-z. ]{2,50}$/
    email: required, valid email, unique
    phone: required, matches /^[0-9NnAa/]{1,20}$/
    age, family_number: numeric ranges
- if validation fails: return 400 with errors
- hash password
- UsersModel.insert(userData)
- return success JSON

Controller: Admin->updateUser(user_id, request)
- authorize user/admin
- validate fields (similar rules; password optional)
- UsersModel.update(user_id, changes)
- return success or errors

Controller: admin/getUser(user_id)
- UsersModel.findById(user_id)
- return user row as JSON (normalize keys safely)

Controller: admin/filterUsers(query params)
- build filter conditions (search name/email, purok, line_number, status)
- UsersModel.filter(conditions)
- return array of user rows as JSON

Model pseudocode (UsersModel):
function insert(data):
    sanitize data
    run DB insert into `users` table
    return inserted id or row

function update(id, data):
    sanitize data
    run DB update where id
    return success boolean

function filter(filters):
    build SQL with safe parameters
    return rows

Security and edge-cases:
- Always re-validate on server side, never trust client-side sanitizer.
- Ensure email uniqueness in DB (unique index) and handle duplicate error gracefully.
- Trim and normalize input (e.g., uppercase/lowercase phone if desired).
- Escape outputs in views to prevent XSS.
