p="app/Services/OrganizationService.php"
s=open(p).read()
s=s.replace("                \"tenant_id\" => $tenant->id,\n                \"organization_id\" => $organization->id,\n                \"name\" => \"Header\",", "                \"tenant_id\" => $tenant->id,\n                \"name\" => \"Header\",")
open(p,"w").write(s)
print("done")
