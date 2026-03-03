# 🏗️ Decommissioning & Migration Checklist
*Review [Mikrotik_Radius_architecture.md](Mikrotik_Radius_architecture.md)  and `DEPRECATING_NETWORK_USERS_MIGRATION_GUIDE.md`*
*(All migrations must include `tenant_id`, `admin_id` `and operator_id` fields.)*

---

## 1. Preparation
- [ ]  Review `Mikrotik_Radius_architecture.md` for controllers, models, and routes.  
- [ ]  Confirm new Blade/Views integration is complete.  
- [x]  Notify stakeholders and schedule migration window.  

---

## 2. Decommissioning (Stop/Archive/Remove)
- [x]  Backup legacy DB tables (`radcheck`, `radreply`, `radacct`, `nas`).  
- [ ]  Archive configs (`resources/freeradius3x/radiusd.conf`, router secrets, firewall rules).  
- [x]  Stop FreeRADIUS service (`systemctl stop freeradius`).  
- [x]  Disable cron jobs (`sync:online_customers`, `rad:sql_relay_v2p`, `restart:freeradius`).  
- [ ]  Remove legacy router configs (PPPoE/Hotspot profiles, suspended pools).   

---

## 3. Implementation (Edit existing or generate new according to examples from `Mikrotik_Radius_architecture.md`)
- [ ]  Deploy/Edit existing controllers (`RouterConfigurationController.php`, `RadreplyController.php`).  
- [ ]  Add new/Edit existing database schemas (`users`,`customers`, `billing_profiles `, `customer_bills`, `customer_payments`, `customer_change_logs`, `payment_methods`, `sms_gateways`, `sms_logs`,`nas`,`olt`,`onu`, `packages`, `ipv4_pools`, `pppoe_profiles`,`accounts`,`invoices`,`cash_ins`,`cash_outs`,`activity_logs`,`device_monitors`,`mikrotik_ip_pools`,`mikrotik_ppp_ackages`,`pppoe_profiles`).
- [ ]  Configure routers with new RADIUS settings, firewall rules, and SNMP monitoring.  
- [ ]  Implement Laravel services (`BillingService`, `PaymentProcessingService`, `RouterManagementService`).  
- [ ]  Set up onboarding flows (`MinimumConfigurationController.php`) for admin.  
- [ ]  Add OLT/ONU sync module (manual sync required until automated function is restored).  

---

## 4. Generate Migration 
- [ ]  Migrate **Roles** (`developers`,`super_admin`,`admin`,`operator`,`sub-operator`,`stuff`).
- [ ]  Migrate **customers** (`all_customers`, `customer_change_logs`).  
- [ ]  Migrate **packages** (`packages`, `billing_profiles`).
- [ ]  Migrate **Payment Gateways** .
- [ ]  Migrate **SMS Gateways** .
- [ ]  Migrate **Invoices** .  
- [ ]  Migrate **NAS entries** (`nas`).  
- [ ]  Migrate **OLT/ONU entries** .  
- [ ]  Migrate **MAC/IP bindings** (Hotspot + PPPoE).  
- [ ]  Migrate **network** definitions (`routers`, `ipv4_pools`, `pppoe_profiles`).  
- [ ]  Migrate **IP pools** (`mikrotik_ip_pools`). (Note: Use `php artisan migrate:mikrotik_resources` command)  
- [ ]  Migrate **PPP profiles** (`mikrotik_ppp_profiles`). (Note: Use `php artisan migrate:mikrotik_resources` command)  
- [ ]  Migrate **prepaid cards** (`customer_payments`, recharge card tables).
- [ ]  Migrate **Logs** .  

---

## 5. Testing (Validate/Verify)
- [ ]  Run PPPoE and Hotspot authentication tests against new RADIUS (`radcheck`, `radreply`).  
- [ ]  Verify billing cycles (daily/monthly) generate invoices (`customer_bills`).  
- [ ]  Test role-based dashboards (Admin, Operator, Sub-operator, Customer).  
- [ ]  Confirm quota enforcement and duplicate session handling scripts (`ppp aaa`, `ppp profile on-up`).  
- [ ]  Validate scheduled tasks (`pull:radaccts`, `delete:rad_stale_sessions`) run correctly.  
- [ ]  Perform security checks (Laravel policies, Sanctum tokens, HTTPS, CSRF).  
- [ ]  Test OLT/ONU .  

---

## 6. Post-Migration Validation
- [ ] ❌ Monitor live sessions (`radacct`) and accounting logs for accuracy.  
- [ ] ❌ Confirm notifications (SMS/email) trigger correctly (`NotificationService`).  
- [ ] ❌ Audit firewall rules and router pools for suspended users.  
- [ ] ❌ Share migration report with stakeholders.  
