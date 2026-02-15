# 🏗️ Decommissioning & Migration Checklist  
*(All migrations — network, package, NAS, OLT, ONU, MAC, IP, customer, IP pool, PPP profile, prepaid card — must include both `tenant_id` and `operator_id`.)*

---

## 1. Preparation
- [ ] ✅ Review `1. Mikrotik_Radius_architecture.md` for controllers, models, and routes.  
- [ ] ❌ Confirm new Blade/Views integration is complete.  
- [ ] ❌ Notify stakeholders and schedule migration window.  

---

## 2. Decommissioning (Stop/Archive/Remove)
- [ ] ✅ Backup legacy DB tables (`radcheck`, `radreply`, `radacct`, `nas`).  
- [ ] ✅ Archive configs (`resources/freeradius3x/radiusd.conf`, router secrets, firewall rules).  
- [ ] ❌ Stop FreeRADIUS service (`systemctl stop freeradius`).  
- [ ] ❌ Disable cron jobs (`sync:online_customers`, `rad:sql_relay_v2p`, `restart:freeradius`).  
- [ ] ❌ Remove legacy router configs (PPPoE/Hotspot profiles, suspended pools).  
- [ ] ❌ Revoke API credentials (`nas.php` → `api_username`, `api_password`) and firewall rules tied to old stack.  

---

## 3. Implementation (Add/Configure)
- [x] Deploy new controllers (`RouterConfigurationController.php`, `RadreplyController.php`).  
- [ ] ❌ Add new database schemas (`users`, `operators`, `packages`, `pppoe_profiles`). (**Blocked by database connection issue**)
- [x] Configure routers with new RADIUS settings, firewall rules, and SNMP monitoring.  
- [x] Implement Laravel services (`BillingService`, `PaymentProcessingService`, `RouterManagementService`).  
- [x] Set up onboarding flows (`MinimumConfigurationController.php`) for operators and resellers.  
- [x] Add OLT/ONU sync module (manual sync required until automated function is restored).  

---

## 4. Migration (Data Transfer)
- [ ] ❌ Migrate **network** definitions (`routers`, `ipv4_pools`, `pppoe_profiles`).  
- [ ] ❌ Migrate **packages** (`packages`, `billing_profiles`).  
- [ ] ❌ Migrate **NAS entries** (`nas.php`).  
- [ ] ❌ Migrate **OLT/ONU entries** (ensure `tenant_id` + `operator_id`).  
- [ ] ❌ Migrate **MAC/IP bindings** (Hotspot + PPPoE).  
- [ ] ❌ Migrate **customers** (`all_customers`, `customer_change_logs`).  
- [ ] ❌ Migrate **IP pools** (`mikrotik_ip_pools`).  
- [ ] ❌ Migrate **PPP profiles** (`mikrotik_ppp_profiles`).  
- [ ] ❌ Migrate **prepaid cards** (`customer_payments`, recharge card tables).  

---

## 5. Testing (Validate/Verify)
- [ ] ❌ Run PPPoE and Hotspot authentication tests against new RADIUS (`radcheck`, `radreply`).  
- [ ] ❌ Verify billing cycles (daily/monthly) generate invoices (`customer_bills`).  
- [ ] ❌ Test role-based dashboards (Admin, Operator, Sub-operator, Customer).  
- [ ] ❌ Confirm quota enforcement and duplicate session handling scripts (`ppp aaa`, `ppp profile on-up`).  
- [ ] ❌ Validate scheduled tasks (`pull:radaccts`, `delete:rad_stale_sessions`) run correctly.  
- [ ] ❌ Perform security checks (Laravel policies, Sanctum tokens, HTTPS, CSRF).  
- [ ] ❌ Test OLT/ONU sync manually until automated function is restored.  

---

## 6. Post-Migration Validation
- [ ] ❌ Monitor live sessions (`radacct`) and accounting logs for accuracy.  
- [ ] ❌ Confirm notifications (SMS/email) trigger correctly (`NotificationService`).  
- [ ] ❌ Audit firewall rules and router pools for suspended users.  
- [ ] ❌ Share migration report with stakeholders.  

---
**NOTE:** The database connection is not working. Please ensure that a MySQL server is running and accessible with the credentials provided in the `.env` file. The migrations could not be run because of this issue.
