Overview — System Pseudocode

Goal: Show the high-level request routing and component responsibilities.

Main entry (index.php)
- receive HTTP request
- bootstrap framework (CodeIgniter)
- route request to Controller based on path and method

Pseudocode:

function handleHttpRequest(request):
    bootstrapFramework()
    route = Router.match(request.path, request.method)
    if not route:
        return httpResponse(404, {error: 'Not found'})

    controllerClass = route.controller
    action = route.action

    controller = instantiate(controllerClass)
    try:
        response = controller.call(action, request)
        return formatResponse(response)
    catch ValidationError as e:
        return httpResponse(400, {errors: e.details})
    catch AuthError:
        return httpResponse(401, {error: 'Unauthorized'})
    catch Exception as e:
        logError(e)
        return httpResponse(500, {error: 'Server error'})

Notes:
- Controllers are responsible for: validating input, calling Models for DB operations, composing view or JSON responses.
- Models encapsulate DB queries and simple business logic; complex transactional flows should use DB transactions inside Models or Services.
- Views render HTML for admin pages; Controllers return JSON for AJAX endpoints.
- Important cross-cutting concerns: CSRF (handled via <?= csrf_field() ?> in forms), server-side validation, sanitization, logging.

Key flow pointers for other subsystems:
- Users: add/update/get/filter endpoints, server validation for names and phone, phone pattern rule: /^[0-9NnAa/]{1,20}$/ (server-side enforce)
- Payments: create gateway row (method=gateway, status=awaiting_payment), supersede previous awaiting rows when new gateway created, confirm manual payments and reject same-day awaiting gateway rows.
- Admin UI: polling & fingerprint to refresh tables, client-side sanitizers for name/phone, AJAX endpoints to fetch and update data.
- CLI tool: `tools/fix_failed_payments.php` — interactive, lists candidate rows, requires explicit confirmation to update DB.
