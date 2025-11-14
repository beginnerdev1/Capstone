# 🧹 Capstone Project Cleanup Checklist

## Priority 1: DELETE THESE FILES

### Controllers
- [ ] **DELETE** `app/Controllers/Admin/Reports.php` (completely redundant)

### Views  
- [ ] **DELETE** `app/Views/admin/proFile.php` (use edit_profile.php instead)
- [ ] **VERIFY & DELETE** `app/Views/admin/index.php` (if Reports::index not used)
- [ ] **CHECK IF USED** `app/Views/admin/tables.php` (generic template)
- [ ] **CHECK IF USED** `app/Views/admin/accountApproval.php` (might be old)

---

## Priority 2: CLEAN UP Admin.php Controller

### Delete These Methods (lines to remove):

```php
// Line 114 - No view file exists
public function layoutStatic() { return view('admin/layout-static'); }

// Line 115 - No view file exists  
public function charts() { return view('admin/charts'); }

// Line 878-888 - Duplicate of editProfile()
public function profile() { ... }

// Lines 669-687 - Already commented out
/* public function viewUser($id) { ... } */

// Lines 689-703 - Already commented out
/* public function toggleUserStatus($id) { ... } */

// Lines 852-876 - Already commented out
/* public function updateQR() { ... } */
```

### Keep Only ONE updateProfile() Method
- ✅ **KEEP:** The JSON version (lines 890-962) - modern, AJAX-ready
- ❌ **REMOVE:** Any older redirect-based version if exists

---

## Priority 3: FIX Routes.php

### Update These Routes:

```php
// CHANGE THIS:
$routes->get('reports', 'Reports::index');
// TO THIS:
$routes->get('reports', 'Admin::reports');

// DELETE THESE ROUTES:
$routes->get('charts', 'Admin::charts'); // No view file
$routes->get('profile', 'Admin::profile'); // Use edit-profile instead
$routes->get('tables', 'Admin::tables'); // If not needed

// VERIFY THESE ARE STILL NEEDED:
$routes->get('404', 'Admin::page404');
$routes->get('401', 'Admin::page401');
$routes->get('500', 'Admin::page500');
```

---

## Priority 4: VERIFY UNUSED VIEWS

Check if these are actually used anywhere:
- [ ] `app/Views/admin/tables.php`
- [ ] `app/Views/admin/accountApproval.php` 
- [ ] `app/Views/admin/register.php` (admin registration?)

---

## Priority 5: CODE IMPROVEMENTS

### In Admin.php:

1. **Line 620-640:** User activation methods
   - `activateUser()`, `deactivateUser()`, `suspendUser()`
   - Consider consolidating into ONE `updateUserStatus($id, $status)` method

2. **Line 645-668:** Approve/Reject methods
   - Could add better validation and email notifications

3. **Add PHPDoc comments** to all public methods for better documentation

---

## Testing Checklist (After Cleanup)

- [ ] Dashboard loads correctly
- [ ] User management works (filter, add, approve)
- [ ] Billing functions work
- [ ] Reports page displays correctly
- [ ] Profile editing works (use edit-profile route)
- [ ] GCash settings save correctly
- [ ] Monthly payments display
- [ ] Transaction records accessible
- [ ] All AJAX endpoints respond correctly

---

## Summary

**Files to DELETE:** 2-4 files
**Methods to REMOVE:** 6-8 methods  
**Routes to FIX:** 4-5 routes
**Estimated Time:** 30-45 minutes
**Risk Level:** LOW (mostly removing unused code)

---

## Notes

- Always backup before deleting
- Test each route after changes
- Check browser console for 404 errors
- Verify no other files reference deleted code
