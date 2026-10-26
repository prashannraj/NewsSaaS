p='database/migrations/2026_10_25_074056_create_organization_users_table.php'
s=open(p).read()
s=s.replace("Schema::dropIfExists('organization_users');", "Schema::dropIfExists('organization_memberships');")
open(p,'w').write(s)

p='database/migrations/2026_10_25_074057_create_domains_table.php'
s=open(p).read()
s=s.replace("Schema::dropIfExists('domains');", "Schema::dropIfExists('tenant_domains');")
open(p,'w').write(s)
print('done')
