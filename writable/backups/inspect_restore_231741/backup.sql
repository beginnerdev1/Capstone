-- Backup generated on 2025-11-25 23:17:19

-- Table structure for `admin`
DROP TABLE IF EXISTS `admin`;
admin;

INSERT INTO `admin` (`id`,`first_name`,`middle_name`,`last_name`,`profile_picture`,`username`,`email`,`password`,`position`,`is_verified`,`otp_code`,`otp_expire`,`created_at`,`updated_at`,`must_change_password`) VALUES ('1', 'Mike Lorenz', 'Verial', 'Vidal', NULL, 'Mike', 'mikevidal689@gmail.com', '$2y$10$4uVMTymsMKH/w0vRLAV8OOt7XDcxpJwW8u0HEmzBNYxP9r01TWM.a', 'President', '1', NULL, NULL, '2025-11-25 20:15:51', '2025-11-25 22:42:07', '0');

-- Table structure for `admin_activity_logs`
DROP TABLE IF EXISTS `admin_activity_logs`;
admin_activity_logs;

INSERT INTO `admin_activity_logs` (`id`,`actor_type`,`actor_id`,`action`,`route`,`method`,`resource`,`details`,`ip_address`,`user_agent`,`created_at`,`logged_out_at`) VALUES ('1', 'admin', '1', 'session', '/Capstone/public/admin/requestPasswordOtp', 'POST', NULL, '[{\"action\":\"start_log\",\"time\":\"2025-11-25 20:26:55\"},{\"action\":\"post\",\"route\":\"\\/Capstone\\/public\\/admin\\/requestPasswordOtp\",\"method\":\"POST\",\"resource\":\"requestPasswordOtp\",\"details\":\"{\\\"csrf_test_name\\\":\\\"28f9a05f53c926aabdce90f026343c81\\\",\\\"current_password\\\":\\\"123456\\\"}\",\"time\":\"2025-11-25 20:26:55\"},{\"action\":\"post\",\"route\":\"\\/Capstone\\/public\\/admin\\/requestPasswordOtp\",\"method\":\"POST\",\"resource\":\"requestPasswordOtp\",\"details\":\"{\\\"csrf_test_name\\\":\\\"28f9a05f53c926aabdce90f026343c81\\\",\\\"current_password\\\":\\\"123456\\\"}\",\"time\":\"2025-11-25 20:28:45\"},{\"action\":\"post\",\"route\":\"\\/Capstone\\/public\\/admin\\/changePassword\",\"method\":\"POST\",\"resource\":\"changePassword\",\"details\":\"{\\\"csrf_test_name\\\":\\\"28f9a05f53c926aabdce90f026343c81\\\",\\\"otp_code\\\":\\\"038463\\\",\\\"new_password\\\":\\\"Miks@1902\\\",\\\"confirm_password\\\":\\\"***\\\"}\",\"time\":\"2025-11-25 20:29:27\"},{\"action\":\"post\",\"route\":\"\\/Capstone\\/public\\/admin\\/requestPasswordOtp\",\"method\":\"POST\",\"resource\":\"requestPasswordOtp\",\"details\":\"{\\\"csrf_test_name\\\":\\\"28f9a05f53c926aabdce90f026343c81\\\",\\\"current_password\\\":\\\"123456\\\"}\",\"time\":\"2025-11-25 20:29:46\"},{\"action\":\"post\",\"route\":\"\\/Capstone\\/public\\/admin\\/requestPasswordOtp\",\"method\":\"POST\",\"resource\":\"requestPasswordOtp\",\"details\":\"{\\\"csrf_test_name\\\":\\\"28f9a05f53c926aabdce90f026343c81\\\",\\\"current_password\\\":\\\"123456\\\"}\",\"time\":\"2025-11-25 20:31:46\"},{\"action\":\"post\",\"route\":\"\\/Capstone\\/public\\/admin\\/requestPasswordOtp\",\"method\":\"POST\",\"resource\":\"requestPasswordOtp\",\"details\":\"{\\\"csrf_test_name\\\":\\\"28f9a05f53c926aabdce90f026343c81\\\",\\\"current_password\\\":\\\"Miks@1902\\\"}\",\"time\":\"2025-11-25 20:32:08\"},{\"action\":\"post\",\"route\":\"\\/Capstone\\/public\\/admin\\/requestPasswordOtp\",\"method\":\"POST\",\"resource\":\"requestPasswordOtp\",\"details\":\"{\\\"csrf_test_name\\\":\\\"28f9a05f53c926aabdce90f026343c81\\\",\\\"current_password\\\":\\\"Miks@1902\\\"}\",\"time\":\"2025-11-25 20:34:32\"},{\"action\":\"post\",\"route\":\"\\/Capstone\\/public\\/admin\\/changePassword\",\"method\":\"POST\",\"resource\":\"changePassword\",\"details\":\"{\\\"csrf_test_name\\\":\\\"28f9a05f53c926aabdce90f026343c81\\\",\\\"otp_code\\\":\\\"039869\\\",\\\"new_password\\\":\\\"Miks_2025\\\",\\\"confirm_password\\\":\\\"***\\\"}\",\"time\":\"2025-11-25 20:35:10\"},{\"action\":\"post\",\"route\":\"\\/Capstone\\/public\\/admin\\/requestPasswordOtp\",\"method\":\"POST\",\"resource\":\"requestPasswordOtp\",\"details\":\"{\\\"csrf_test_name\\\":\\\"28f9a05f53c926aabdce90f026343c81\\\",\\\"current_password\\\":\\\"Miks_2025\\\"}\",\"time\":\"2025-11-25 20:37:32\"},{\"action\":\"post\",\"route\":\"\\/Capstone\\/public\\/admin\\/changePassword\",\"method\":\"POST\",\"resource\":\"changePassword\",\"details\":\"{\\\"csrf_test_name\\\":\\\"28f9a05f53c926aabdce90f026343c81\\\",\\\"otp_code\\\":\\\"334679\\\",\\\"new_password\\\":\\\"Miks@0119\\\",\\\"confirm_password\\\":\\\"***\\\"}\",\"time\":\"2025-11-25 20:38:03\"},{\"action\":\"post\",\"route\":\"\\/Capstone\\/public\\/admin\\/requestPasswordOtp\",\"method\":\"POST\",\"resource\":\"requestPasswordOtp\",\"details\":\"{\\\"csrf_test_name\\\":\\\"28f9a05f53c926aabdce90f026343c81\\\",\\\"current_password\\\":\\\"Miks@0119\\\"}\",\"time\":\"2025-11-25 20:39:57\"},{\"action\":\"post\",\"route\":\"\\/Capstone\\/public\\/admin\\/changePassword\",\"method\":\"POST\",\"resource\":\"changePassword\",\"details\":\"{\\\"csrf_test_name\\\":\\\"28f9a05f53c926aabdce90f026343c81\\\",\\\"otp_code\\\":\\\"838540\\\",\\\"new_password\\\":\\\"Miks_2025\\\",\\\"confirm_password\\\":\\\"***\\\"}\",\"time\":\"2025-11-25 20:40:23\"},{\"action\":\"post\",\"route\":\"\\/Capstone\\/public\\/admin\\/requestPasswordOtp\",\"method\":\"POST\",\"resource\":\"requestPasswordOtp\",\"details\":\"{\\\"csrf_test_name\\\":\\\"28f9a05f53c926aabdce90f026343c81\\\",\\\"current_password\\\":\\\"Miks_2025\\\"}\",\"time\":\"2025-11-25 20:45:21\"},{\"action\":\"post\",\"route\":\"\\/Capstone\\/public\\/admin\\/changePassword\",\"method\":\"POST\",\"resource\":\"changePassword\",\"details\":\"{\\\"csrf_test_name\\\":\\\"28f9a05f53c926aabdce90f026343c81\\\",\\\"otp_code\\\":\\\"769141\\\",\\\"new_password\\\":\\\"Miks@0119\\\",\\\"confirm_password\\\":\\\"***\\\"}\",\"time\":\"2025-11-25 20:46:14\"},{\"action\":\"edit\",\"route\":\"\\/Capstone\\/public\\/admin\\/updateProfile\",\"method\":\"POST\",\"resource\":\"updateProfile\",\"details\":\"{\\\"csrf_test_name\\\":\\\"28f9a05f53c926aabdce90f026343c81\\\",\\\"first_name\\\":\\\"Mike Lorenz\\\",\\\"middle_name\\\":\\\"Verial\\\",\\\"last_name\\\":\\\"Vidal\\\",\\\"position\\\":\\\"President\\\",\\\"email\\\":\\\"mikevidal689@gmail.com\\\"}\",\"time\":\"2025-11-25 20:48:13\"},{\"action\":\"post\",\"route\":\"\\/Capstone\\/public\\/admin\\/requestPasswordOtp\",\"method\":\"POST\",\"resource\":\"requestPasswordOtp\",\"details\":\"{\\\"csrf_test_name\\\":\\\"28f9a05f53c926aabdce90f026343c81\\\",\\\"current_password\\\":\\\"Miks@0119\\\"}\",\"time\":\"2025-11-25 20:50:07\"},{\"action\":\"post\",\"route\":\"\\/Capstone\\/public\\/admin\\/changePassword\",\"method\":\"POST\",\"resource\":\"changePassword\",\"details\":\"{\\\"csrf_test_name\\\":\\\"28f9a05f53c926aabdce90f026343c81\\\",\\\"otp_code\\\":\\\"836066\\\",\\\"new_password\\\":\\\"Miks_2025\\\",\\\"confirm_password\\\":\\\"***\\\"}\",\"time\":\"2025-11-25 20:50:53\"},{\"action\":\"post\",\"route\":\"\\/Capstone\\/public\\/admin\\/requestPasswordOtp\",\"method\":\"POST\",\"resource\":\"requestPasswordOtp\",\"details\":\"{\\\"csrf_test_name\\\":\\\"28f9a05f53c926aabdce90f026343c81\\\",\\\"current_password\\\":\\\"Miks_2025\\\"}\",\"time\":\"2025-11-25 20:53:51\"},{\"action\":\"post\",\"route\":\"\\/Capstone\\/public\\/admin\\/changePassword\",\"method\":\"POST\",\"resource\":\"changePassword\",\"details\":\"{\\\"csrf_test_name\\\":\\\"28f9a05f53c926aabdce90f026343c81\\\",\\\"otp_code\\\":\\\"035151\\\",\\\"new_password\\\":\\\"Miks@0119\\\",\\\"confirm_password\\\":\\\"***\\\"}\",\"time\":\"2025-11-25 20:54:18\"},{\"action\":\"create\",\"route\":\"\\/Capstone\\/public\\/admin\\/addUser\",\"method\":\"POST\",\"resource\":\"addUser\",\"details\":\"{\\\"csrf_test_name\\\":\\\"28f9a05f53c926aabdce90f026343c81\\\",\\\"first_name\\\":\\\"Mike Lorenz\\\",\\\"last_name\\\":\\\"Vidal\\\",\\\"email\\\":\\\"mikevidal689@gmail.com\\\",\\\"password\\\":\\\"***\\\",\\\"phone\\\":\\\"09600947741\\\",\\\"gender\\\":\\\"Male\\\",\\\"age\\\":\\\"23\\\",\\\"family_number\\\":\\\"6\\\",\\\"purok\\\":\\\"3\\\",\\\"status\\\":\\\"approved\\\",\\\"line_number\\\":\\\"C0101\\\"}\",\"time\":\"2025-11-25 21:04:53\"},{\"action\":\"create\",\"route\":\"\\/Capstone\\/public\\/admin\\/addUser\",\"method\":\"POST\",\"resource\":\"addUser\",\"details\":\"{\\\"csrf_test_name\\\":\\\"28f9a05f53c926aabdce90f026343c81\\\",\\\"first_name\\\":\\\"Mike Lorenz\\\",\\\"last_name\\\":\\\"Vidal\\\",\\\"email\\\":\\\"mikevidal689@gmail.com\\\",\\\"password\\\":\\\"***\\\",\\\"phone\\\":\\\"09600947741\\\",\\\"gender\\\":\\\"Male\\\",\\\"age\\\":\\\"23\\\",\\\"family_number\\\":\\\"6\\\",\\\"purok\\\":\\\"3\\\",\\\"status\\\":\\\"approved\\\",\\\"line_number\\\":\\\"C0101\\\"}\",\"time\":\"2025-11-25 21:15:17\"},{\"action\":\"deactivate\",\"route\":\"\\/admin\\/deactivateUser\\/2\",\"method\":\"POST\",\"resource\":\"2\",\"details\":\"{\\\"id\\\":\\\"2\\\",\\\"user_name\\\":\\\"Mike Lorenz Vidal\\\",\\\"first_name\\\":\\\"Mike Lorenz\\\",\\\"last_name\\\":\\\"Vidal\\\",\\\"email\\\":\\\"mikevidal689@gmail.com\\\",\\\"reason\\\":\\\"\\\",\\\"inactive_ref_id\\\":1}\",\"time\":\"2025-11-25 21:25:02\"},{\"action\":\"post\",\"route\":\"\\/Capstone\\/public\\/admin\\/approve\\/2\",\"method\":\"POST\",\"resource\":\"2\",\"details\":\"{\\\"csrf_test_name\\\":\\\"28f9a05f53c926aabdce90f026343c81\\\",\\\"first_name\\\":\\\"Mike Lorenz\\\",\\\"last_name\\\":\\\"Vidal\\\",\\\"user_id\\\":2,\\\"user_name\\\":\\\"Mike Lorenz Vidal\\\"}\",\"time\":\"2025-11-25 21:29:18\"},{\"action\":\"get\",\"route\":\"\\/Capstone\\/public\\/admin\\/exportReports\",\"method\":\"GET\",\"resource\":\"exportReports\",\"details\":\"{\\\"start\\\":\\\"2024-12-31\\\",\\\"end\\\":\\\"2025-11-25\\\",\\\"format\\\":\\\"csv\\\"}\",\"time\":\"2025-11-25 22:20:29\"},{\"action\":\"get\",\"route\":\"\\/Capstone\\/public\\/admin\\/exportReports\",\"method\":\"GET\",\"resource\":\"exportReports\",\"details\":\"{\\\"start\\\":\\\"2024-12-31\\\",\\\"end\\\":\\\"2025-11-25\\\",\\\"format\\\":\\\"pdf\\\"}\",\"time\":\"2025-11-25 22:21:40\"},{\"action\":\"get\",\"route\":\"\\/Capstone\\/public\\/admin\\/exportReports\",\"method\":\"GET\",\"resource\":\"exportReports\",\"details\":\"{\\\"start\\\":\\\"2024-12-31\\\",\\\"end\\\":\\\"2025-11-25\\\",\\\"format\\\":\\\"pdf\\\"}\",\"time\":\"2025-11-25 22:22:03\"},{\"action\":\"get\",\"route\":\"\\/Capstone\\/public\\/admin\\/exportReports\",\"method\":\"GET\",\"resource\":\"exportReports\",\"details\":\"{\\\"start\\\":\\\"2024-12-31\\\",\\\"end\\\":\\\"2025-11-25\\\",\\\"format\\\":\\\"pdf\\\"}\",\"time\":\"2025-11-25 22:23:39\"},{\"action\":\"get\",\"route\":\"\\/Capstone\\/public\\/admin\\/exportReports\",\"method\":\"GET\",\"resource\":\"exportReports\",\"details\":\"{\\\"start\\\":\\\"2024-12-31\\\",\\\"end\\\":\\\"2025-11-25\\\",\\\"format\\\":\\\"pdf\\\"}\",\"time\":\"2025-11-25 22:31:02\"},{\"action\":\"logout\",\"time\":\"2025-11-25 22:37:51\"}]', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-25 20:26:55', '2025-11-25 22:37:51');
INSERT INTO `admin_activity_logs` (`id`,`actor_type`,`actor_id`,`action`,`route`,`method`,`resource`,`details`,`ip_address`,`user_agent`,`created_at`,`logged_out_at`) VALUES ('2', 'admin', '1', 'login', '/admin/login', 'POST', 'login', '[{\"action\":\"logout\",\"time\":\"2025-11-25 22:44:12\"}]', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36', '2025-11-25 22:42:15', '2025-11-25 22:44:12');
INSERT INTO `admin_activity_logs` (`id`,`actor_type`,`actor_id`,`action`,`route`,`method`,`resource`,`details`,`ip_address`,`user_agent`,`created_at`,`logged_out_at`) VALUES ('3', 'superadmin', '1', 'login', '/superadmin/login', 'POST', 'check-code', '[{\"action\":\"post\",\"route\":\"\\/Capstone\\/public\\/superadmin\\/check-code\",\"method\":\"POST\",\"resource\":\"check-code\",\"details\":\"{\\\"admin_code\\\":\\\"975125\\\"}\",\"time\":\"2025-11-25 22:54:48\"}]', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36', '2025-11-25 22:54:48', NULL);

-- Table structure for `admin_archived`
DROP TABLE IF EXISTS `admin_archived`;
admin_archived;


-- Table structure for `admin_chats`
DROP TABLE IF EXISTS `admin_chats`;
admin_chats;


-- Table structure for `archived_billings`
DROP TABLE IF EXISTS `archived_billings`;
archived_billings;


-- Table structure for `billings`
DROP TABLE IF EXISTS `billings`;
billings;

INSERT INTO `billings` (`id`,`user_id`,`description`,`bill_no`,`amount_due`,`carryover`,`balance`,`status`,`billing_month`,`due_date`,`paid_date`,`remarks`,`created_at`,`updated_at`) VALUES ('1', '2', NULL, 'BILL-20251125-0002', '60.00', '0.00', '10.00', 'Partial', '2025-11-01', '2025-12-02', NULL, NULL, '2025-11-25 22:33:25', '2025-11-25 22:33:25');

-- Table structure for `chat_messages`
DROP TABLE IF EXISTS `chat_messages`;
chat_messages;


-- Table structure for `gcash_settings`
DROP TABLE IF EXISTS `gcash_settings`;
gcash_settings;


-- Table structure for `inactive_users`
DROP TABLE IF EXISTS `inactive_users`;
inactive_users;

INSERT INTO `inactive_users` (`id`,`user_id`,`email`,`first_name`,`last_name`,`phone`,`purok`,`barangay`,`municipality`,`province`,`zipcode`,`inactivated_at`,`inactivated_by`,`reason`,`created_at`,`updated_at`) VALUES ('1', '2', 'mikevidal689@gmail.com', 'Mike Lorenz', 'Vidal', '09600947741', '3', 'Borlongan', 'Dipaculao', 'Aurora', '3203', '2025-11-25 21:25:02', '1', '', '2025-11-25 21:25:02', '2025-11-25 21:25:02');

-- Table structure for `migrations`
DROP TABLE IF EXISTS `migrations`;
migrations;

INSERT INTO `migrations` (`id`,`version`,`class`,`group`,`namespace`,`time`,`batch`) VALUES ('798', '2025-08-21-084947', 'App\\Database\\Migrations\\CreateUsersTable', 'default', 'App', '1764072893', '1');
INSERT INTO `migrations` (`id`,`version`,`class`,`group`,`namespace`,`time`,`batch`) VALUES ('799', '2025-09-06-080639', 'App\\Database\\Migrations\\CreateBillingTable', 'default', 'App', '1764072893', '1');
INSERT INTO `migrations` (`id`,`version`,`class`,`group`,`namespace`,`time`,`batch`) VALUES ('800', '2025-09-06-080934', 'App\\Database\\Migrations\\CreateAdminTable', 'default', 'App', '1764072893', '1');
INSERT INTO `migrations` (`id`,`version`,`class`,`group`,`namespace`,`time`,`batch`) VALUES ('801', '2025-09-13-070656', 'App\\Database\\Migrations\\CreateServiceReportsTable', 'default', 'App', '1764072893', '1');
INSERT INTO `migrations` (`id`,`version`,`class`,`group`,`namespace`,`time`,`batch`) VALUES ('802', '2025-09-21-095332', 'App\\Database\\Migrations\\CreateUserInformation', 'default', 'App', '1764072894', '1');
INSERT INTO `migrations` (`id`,`version`,`class`,`group`,`namespace`,`time`,`batch`) VALUES ('803', '2025-09-22-123400', 'App\\Database\\Migrations\\CreatePasswordResetTable', 'default', 'App', '1764072894', '1');
INSERT INTO `migrations` (`id`,`version`,`class`,`group`,`namespace`,`time`,`batch`) VALUES ('804', '2025-10-02-110152', 'App\\Database\\Migrations\\CreateSuperAdminTable', 'default', 'App', '1764072894', '1');
INSERT INTO `migrations` (`id`,`version`,`class`,`group`,`namespace`,`time`,`batch`) VALUES ('805', '2025-10-04-161829', 'App\\Database\\Migrations\\AddDescriptionToBillings', 'default', 'App', '1764072894', '1');
INSERT INTO `migrations` (`id`,`version`,`class`,`group`,`namespace`,`time`,`batch`) VALUES ('806', '2025-10-08-095645', 'App\\Database\\Migrations\\CreatePaymentsTable', 'default', 'App', '1764072894', '1');
INSERT INTO `migrations` (`id`,`version`,`class`,`group`,`namespace`,`time`,`batch`) VALUES ('807', '2025-11-11-074803', 'App\\Database\\Migrations\\CreateGcashSettingsTable', 'default', 'App', '1764072894', '1');
INSERT INTO `migrations` (`id`,`version`,`class`,`group`,`namespace`,`time`,`batch`) VALUES ('808', '2025-11-16-000001', 'App\\Database\\Migrations\\CreateAdminArchivedTable', 'default', 'App', '1764072894', '1');
INSERT INTO `migrations` (`id`,`version`,`class`,`group`,`namespace`,`time`,`batch`) VALUES ('809', '2025-11-16-000010', 'App\\Database\\Migrations\\CreateAdminActivityLogs', 'default', 'App', '1764072894', '1');
INSERT INTO `migrations` (`id`,`version`,`class`,`group`,`namespace`,`time`,`batch`) VALUES ('810', '2025-11-16-120000', 'App\\Database\\Migrations\\AddInactiveUsersAndArchivedBillings', 'default', 'App', '1764072894', '1');
INSERT INTO `migrations` (`id`,`version`,`class`,`group`,`namespace`,`time`,`batch`) VALUES ('811', '2025-11-18-020000', 'App\\Database\\Migrations\\AddUniqueIndexToInactiveUsers', 'default', 'App', '1764072894', '1');
INSERT INTO `migrations` (`id`,`version`,`class`,`group`,`namespace`,`time`,`batch`) VALUES ('812', '2025-11-18-120000', 'App\\Database\\Migrations\\AddNamesToSuperAdmin', 'default', 'App', '1764072894', '1');
INSERT INTO `migrations` (`id`,`version`,`class`,`group`,`namespace`,`time`,`batch`) VALUES ('813', '2025-11-18-220001', 'App\\Database\\Migrations\\AddSuperadminOtpAndFlags', 'default', 'App', '1764072894', '1');
INSERT INTO `migrations` (`id`,`version`,`class`,`group`,`namespace`,`time`,`batch`) VALUES ('814', '2025-11-18-220002', 'App\\Database\\Migrations\\AddSuperadminSecurityFields', 'default', 'App', '1764072894', '1');
INSERT INTO `migrations` (`id`,`version`,`class`,`group`,`namespace`,`time`,`batch`) VALUES ('815', '2025-11-18-220004', 'App\\Database\\Migrations\\AddAdminCodeUsedAt', 'default', 'App', '1764072894', '1');
INSERT INTO `migrations` (`id`,`version`,`class`,`group`,`namespace`,`time`,`batch`) VALUES ('816', '2025-11-19-000001', 'App\\Database\\Migrations\\CreateChatMessages', 'default', 'App', '1764072894', '1');
INSERT INTO `migrations` (`id`,`version`,`class`,`group`,`namespace`,`time`,`batch`) VALUES ('817', '2025-11-19-000002', 'App\\Database\\Migrations\\AddUserNameToChatMessages', 'default', 'App', '1764072894', '1');
INSERT INTO `migrations` (`id`,`version`,`class`,`group`,`namespace`,`time`,`batch`) VALUES ('818', '2025-11-20-000001', 'App\\Database\\Migrations\\AddMustChangePassword', 'default', 'App', '1764072894', '1');
INSERT INTO `migrations` (`id`,`version`,`class`,`group`,`namespace`,`time`,`batch`) VALUES ('819', '2025-11-20-120000', 'App\\Database\\Migrations\\AddActorToPasswordResets', 'default', 'App', '1764072894', '1');
INSERT INTO `migrations` (`id`,`version`,`class`,`group`,`namespace`,`time`,`batch`) VALUES ('820', '2025-11-21-113500', 'App\\Database\\Migrations\\AddIsInternalToChatMessages', 'default', 'App', '1764072894', '1');
INSERT INTO `migrations` (`id`,`version`,`class`,`group`,`namespace`,`time`,`batch`) VALUES ('821', '2025-11-21-230000', 'App\\Database\\Migrations\\AddAdminRecipientToChatMessages', 'default', 'App', '1764072894', '1');
INSERT INTO `migrations` (`id`,`version`,`class`,`group`,`namespace`,`time`,`batch`) VALUES ('822', '2025-11-23-000001', 'App\\Database\\Migrations\\CreateAdminChats', 'default', 'App', '1764072894', '1');
INSERT INTO `migrations` (`id`,`version`,`class`,`group`,`namespace`,`time`,`batch`) VALUES ('823', '2025-11-25-120000', 'App\\Database\\Migrations\\BackfillBillingStatusFromPayments', 'default', 'App', '1764072894', '1');

-- Table structure for `password_resets`
DROP TABLE IF EXISTS `password_resets`;
password_resets;


-- Table structure for `payment_settings`
DROP TABLE IF EXISTS `payment_settings`;
payment_settings;


-- Table structure for `payments`
DROP TABLE IF EXISTS `payments`;
payments;

INSERT INTO `payments` (`id`,`user_id`,`billing_id`,`payment_intent_id`,`payment_method_id`,`method`,`reference_number`,`admin_reference`,`receipt_image`,`amount`,`currency`,`status`,`expires_at`,`attempt_number`,`paid_at`,`created_at`,`updated_at`,`deleted_at`) VALUES ('1', '2', '1', 'manual_6925bfcb918fc', NULL, 'manual', '123456', '123456', 'uploads/receipts/123456_20251125_224011.jpg', '50', 'PHP', 'partial', NULL, '1', '2025-11-25 22:42:49', '2025-11-25 22:40:11', '2025-11-25 22:42:49', NULL);

-- Table structure for `service_reports`
DROP TABLE IF EXISTS `service_reports`;
service_reports;


-- Table structure for `super_admin`
DROP TABLE IF EXISTS `super_admin`;
super_admin;

INSERT INTO `super_admin` (`id`,`admin_code`,`email`,`password`,`created_at`,`updated_at`,`first_name`,`middle_name`,`last_name`,`otp_hash`,`otp_expires`,`is_primary`,`otp_failed_attempts`,`otp_locked_until`,`admin_code_used_at`,`must_change_password`) VALUES ('1', '$2y$10$QVnCnHgGhWPnShONvuwl.eVWV95lY7bMLOFbWu9F2ygU.jIvFS1wO', 'mikevidal689@gmail.com', '$2y$10$xX6UiwnSTulDHOhqORwlk.py9V/L8IubTqf9b/ByRYxkJRF3NduDu', '2025-11-25 22:54:03', '2025-11-25 22:54:48', NULL, NULL, NULL, NULL, NULL, '0', '0', NULL, NULL, '0');

-- Table structure for `superadmin_actions`
DROP TABLE IF EXISTS `superadmin_actions`;
superadmin_actions;


-- Table structure for `user_information`
DROP TABLE IF EXISTS `user_information`;
user_information;

INSERT INTO `user_information` (`info_id`,`user_id`,`first_name`,`last_name`,`gender`,`age`,`family_number`,`phone`,`line_number`,`purok`,`barangay`,`municipality`,`province`,`zipcode`,`profile_picture`,`created_at`,`updated_at`) VALUES ('2', '2', 'Mike Lorenz', 'Vidal', 'Male', '23', '6', '09600947741', 'C0101', '3', 'Borlongan', 'Dipaculao', 'Aurora', '3203', '1764077328_6925af10a69f4.jpg', '2025-11-25 21:15:16', '2025-11-25 21:28:48');

-- Table structure for `users`
DROP TABLE IF EXISTS `users`;
users;

INSERT INTO `users` (`id`,`email`,`password`,`active`,`status`,`is_verified`,`profile_complete`,`otp_code`,`otp_expires`,`created_at`,`updated_at`) VALUES ('2', 'mikevidal689@gmail.com', '$2y$10$S0Qkf6m31fWSGGuGKBRd3uya/39nJJ6CP/SiC53plVGthEJ.TZeN.', '2', 'approved', '1', '1', NULL, NULL, '2025-11-25 21:15:15', '2025-11-25 22:43:37');

