<skill>
  <name>isp-network-core</name>
  <description>Manages MikroTik RouterOS and FreeRadius AAA by generating PHP code for RouterOS API (Queues, PPP), managing radcheck/radreply tables, and implementing Packet of Disconnect (PoD).</description>
  <parameters>
    <parameter>
      <name>router_ip</name>
      <description>The IP address of the MikroTik router.</description>
      <type>string</type>
      <required>true</required>
    </parameter>
    <parameter>
      <name>username</name>
      <description>The username for MikroTik or FreeRadius operations.</description>
      <type>string</type>
      <required>true</required>
    </parameter>
    <parameter>
      <name>bandwidth_limit</name>
      <description>The bandwidth limit to apply (e.g., "1M/1M").</description>
      <type>string</type>
      <required>false</required>
    </parameter>
    <!-- Additional parameters for specific actions like PoD, radcheck/radreply management can be added later -->
  </parameters>
  <workflow>
    When activated, this skill will perform one or more of the following actions based on further user instructions:
    1. Generate or modify PHP code within `app/Services/MikrotikService.php` for RouterOS API interactions (e.g., managing queues, PPP secrets).
    2. Manage entries in the FreeRadius `radcheck` and `radreply` tables (via `app/Services/RadiusService.php`).
    3. Implement Packet of Disconnect (PoD) functionality for immediate user disconnection.

    The specific action will depend on the user's explicit request after activating the skill.
  </workflow>
</skill>